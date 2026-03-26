<?php

namespace App\Http\Controllers;

use App\Domain\Services\AgendaIcalService;
use App\Domain\Services\AgendaService;
use App\Domain\Services\AlumnoService;
use App\Exports\AgendaExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf as PdfFacade;
use Maatwebsite\Excel\Facades\Excel;

class AgendaPublicController extends Controller
{
    public function __construct(
        private AgendaService $agendaService,
        private AlumnoService $alumnoService,
        private AgendaIcalService $agendaIcalService,
    ) {}

    /**
     * Parámetros de filtro actuales desde la request (para descargas y vista).
     */
    private function paramsFiltros(Request $request): array
    {
        $vista = $request->input('vista', 'semana');
        $params = ['vista' => $vista];
        if ($vista === 'mes') {
            $params['anio'] = (int) $request->input('anio', date('Y'));
            $params['mes'] = (int) $request->input('mes', (int) date('n'));
        } else {
            if ($request->filled('fecha_inicio')) {
                $params['fecha_inicio'] = $request->input('fecha_inicio');
            }
        }
        $raw = trim((string) ($request->input('alumno_id') ?? ''));
        if ($raw !== '' && ctype_digit($raw) && (int) $raw > 0) {
            $params['alumno_id'] = (int) $raw;
        }
        return $params;
    }

    /**
     * Resuelve las filas de agenda aplicando filtros (periodo y alumno). Usado por index, descargas e imprimir.
     *
     * @return array{0: array, 1: string} [filas, nombreArchivo]
     */
    private function resolverFilas(Request $request): array
    {
        $vista = $request->input('vista', 'semana');
        $alumnoId = $request->filled('alumno_id') && ctype_digit(trim((string) $request->input('alumno_id')))
            ? (int) $request->input('alumno_id')
            : null;
        if ($alumnoId === 0) {
            $alumnoId = null;
        }

        if ($vista === 'mes') {
            $anio = (int) $request->input('anio', date('Y'));
            $mes = (int) $request->input('mes', (int) date('n'));
            $filas = $this->agendaService->agendaMes($anio, $mes, $alumnoId);
            $nombre = "agenda_mes_{$anio}_{$mes}";
        } else {
            $fechaInicio = $request->filled('fecha_inicio')
                ? Carbon::parse($request->input('fecha_inicio'))->startOfWeek()
                : null;
            $filas = $this->agendaService->agendaSemana($fechaInicio, $alumnoId);
            $inicio = $filas[0]['fecha'] ?? now()->toDateString();
            $nombre = 'agenda_semana_' . str_replace('-', '', $inicio);
        }
        return [$filas, $nombre];
    }

    public function index(Request $request): View
    {
        $vista = $request->input('vista', 'semana');
        $anio = (int) $request->input('anio', (int) date('Y'));
        $mes = (int) $request->input('mes', (int) date('n'));
        $alumnoId = $request->filled('alumno_id') && ctype_digit(trim((string) $request->input('alumno_id')))
            ? (int) $request->input('alumno_id')
            : null;
        if ($alumnoId === 0) {
            $alumnoId = null;
        }

        if ($vista === 'mes') {
            $filas = $this->agendaService->agendaMes($anio, $mes, $alumnoId);
            $titulo = Carbon::createFromDate($anio, $mes, 1)->locale('es')->translatedFormat('F Y');
            $estadisticasMes = $this->agendaService->estadisticasResumenMes($anio, $mes);
        } else {
            $inicioSemana = $request->filled('fecha_inicio')
                ? Carbon::parse($request->input('fecha_inicio'))->startOfWeek()
                : Carbon::today()->startOfWeek();
            $filas = $this->agendaService->agendaSemana($inicioSemana, $alumnoId);
            $finSemana = $inicioSemana->copy()->endOfWeek();
            $titulo = 'Semana del ' . $inicioSemana->format('d/m/Y') . ' al ' . $finSemana->format('d/m/Y');
            $estadisticasMes = null;
        }

        $hoy = Carbon::today();
        $alumnosParaFiltro = $this->alumnoService->activosParaFiltro();
        $avisosProximos = $alumnoId ? $this->agendaService->getAvisosProximosParaAlumno($alumnoId) : [];

        return view('agenda.index', [
            'filas' => $filas,
            'titulo' => $titulo,
            'vista' => $vista,
            'anio' => $anio,
            'mes' => $mes,
            'inicioSemana' => $vista === 'semana' ? ( $request->filled('fecha_inicio') ? Carbon::parse($request->input('fecha_inicio'))->startOfWeek() : Carbon::today()->startOfWeek() ) : null,
            'hoy' => $hoy,
            'alumnosParaFiltro' => $alumnosParaFiltro,
            'alumnoIdFiltro' => $alumnoId,
            'paramsFiltros' => $this->paramsFiltros($request),
            'avisosProximos' => $avisosProximos,
            'estadisticasMes' => $estadisticasMes ?? null,
        ]);
    }

    public function descargarCsv(Request $request)
    {
        [$filas, $nombre] = $this->resolverFilas($request);
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombre}.csv\"",
        ];
        return Response::streamDownload(function () use ($filas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Fecha', 'Día', 'Cereal', 'Fruta', 'Elaboración', 'Es feriado'], ';');
            foreach ($filas as $row) {
                fputcsv($out, [
                    $row['fecha'],
                    $row['dia'],
                    $row['cereal'],
                    $row['fruta']['nombre'] ?? '',
                    $row['elaboracion']['nombre'] ?? '',
                    $row['es_feriado'] ? 'Sí' : 'No',
                ], ';');
            }
            fclose($out);
        }, "{$nombre}.csv", $headers);
    }

    public function descargarExcel(Request $request)
    {
        [$filas, $nombre] = $this->resolverFilas($request);
        return Excel::download(new AgendaExport($filas), "{$nombre}.xlsx");
    }

    public function descargarPdf(Request $request)
    {
        [$filas, $nombre] = $this->resolverFilas($request);
        $vista = $request->input('vista', 'semana');
        $titulo = $vista === 'mes'
            ? 'Agenda mes ' . $request->input('anio') . '-' . $request->input('mes')
            : 'Agenda semana';
        $pdf = PdfFacade::loadView('agenda.pdf', ['filas' => $filas, 'titulo' => $titulo]);
        return $pdf->download("{$nombre}.pdf");
    }

    /**
     * Vista solo con tabla y título para imprimir. Aplica los mismos filtros que la agenda.
     */
    public function imprimir(Request $request): View
    {
        [$filas, $nombre] = $this->resolverFilas($request);
        $vista = $request->input('vista', 'semana');
        $anio = (int) $request->input('anio', (int) date('Y'));
        $mes = (int) $request->input('mes', (int) date('n'));
        if ($vista === 'mes') {
            $titulo = Carbon::createFromDate($anio, $mes, 1)->locale('es')->translatedFormat('F Y');
        } else {
            $inicioSemana = $request->filled('fecha_inicio')
                ? Carbon::parse($request->input('fecha_inicio'))->startOfWeek()
                : Carbon::today()->startOfWeek();
            $finSemana = $inicioSemana->copy()->endOfWeek();
            $titulo = 'Semana del ' . $inicioSemana->format('d/m/Y') . ' al ' . $finSemana->format('d/m/Y');
        }
        return view('agenda.imprimir', ['filas' => $filas, 'titulo' => $titulo]);
    }

    public function descargarIcal(Request $request)
    {
        [$filas, $nombre] = $this->resolverFilas($request);
        $ics = $this->agendaIcalService->generarIcs($filas);
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombre . '.ics"',
        ]);
    }

    /**
     * iCal de un solo día (un evento). Para el enlace "Añadir al calendario" por fila.
     */
    public function icalUnDia(string $fecha)
    {
        $carbon = Carbon::createFromFormat('Y-m-d', $fecha);
        if (!$carbon || $carbon->format('Y-m-d') !== $fecha) {
            abort(404);
        }
        $fila = $this->agendaService->agendaUnDia($carbon);
        if (!$fila) {
            abort(404);
        }
        $ics = $this->agendaIcalService->generarIcsUnDia($fila);
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="merienda-' . $fecha . '.ics"',
        ]);
    }
}
