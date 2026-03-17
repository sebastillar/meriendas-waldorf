<?php

namespace App\Filament\Widgets;

use App\Domain\Services\RecolectandoService;
use Carbon\CarbonInterface;
use Filament\Widgets\Widget;

class RecolectandoWidget extends Widget
{
    protected static string $view = 'filament.widgets.recolectando-widget';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    /** @var array<int, array<string, mixed>> */
    public array $recolectasMes = [];

    public ?array $proximoCumpleanos = null;

    public function mount(RecolectandoService $recolectandoService): void
    {
        $this->refreshRecolectaData($recolectandoService);
    }

    protected function refreshRecolectaData(?RecolectandoService $service = null): void
    {
        $service = $service ?? app(RecolectandoService::class);
        $this->recolectasMes = array_map(
            fn (array $row): array => $this->toArrayRecolectaDelMes($row),
            $service->recolectasDelMesActual()
        );

        $this->proximoCumpleanos = empty($this->recolectasMes)
            ? $service->proximoCumpleanosSinRecolectora()
            : null;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->refreshRecolectaData();
        return parent::render();
    }

    /**
     * @param array{
     *   alumno_beneficiario: \App\Domain\Models\Alumno,
     *   familia_beneficiaria: \App\Domain\Models\Familia,
     *   familia_recolectora: ?\App\Domain\Models\Familia,
     *   fecha_cumpleanos: \Carbon\Carbon,
     *   estado: string,
     *   aportaron_count: int,
     *   total_count: int
     * } $data
     */
    private function toArrayRecolectaDelMes(array $data): array
    {
        $recolectora = $data['familia_recolectora'];
        $alumno = $data['alumno_beneficiario'];
        /** @var CarbonInterface $fecha */
        $fecha = $data['fecha_cumpleanos'];

        return [
            'alumno_beneficiario_nombre' => $alumno->nombre,
            'fecha_formato' => $fecha->format('d/m/Y'),
            'estado' => $data['estado'],
            'familia_recolectora_nombre' => $recolectora?->nombre_para_listado,
            'monto_aportar' => config('meriendas.regalo.monto_aportar', 300),
            'banco' => $recolectora?->banco,
            'numero_cuenta' => $recolectora?->numero_cuenta,
            'tipo_cuenta' => $recolectora?->tipo_cuenta,
            'nombre_cuenta' => $recolectora?->nombre_cuenta,
            'moneda' => $recolectora?->moneda,
            'aportaron_count' => $data['aportaron_count'] ?? 0,
            'total_count' => $data['total_count'] ?? 0,
        ];
    }
}
