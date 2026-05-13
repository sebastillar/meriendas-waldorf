<?php

namespace Tests\Unit;

use App\Domain\Models\Alumno;
use App\Domain\Models\Asignacion;
use App\Domain\Models\ConfiguracionCalendario;
use App\Domain\Models\Familia;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Domain\Repositories\DiaSinClaseRepositoryInterface;
use App\Domain\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AgendaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_semana_respeta_configuracion_calendario_en_fecha_inicio()
    {
        ConfiguracionCalendario::factory()->create([
            'anio' => 2026,
            'fecha_inicio_clases' => '2026-03-03',
            'fecha_fin_clases' => '2026-12-20',
        ]);

        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);

        $asignacionRepo->expects($this->once())
            ->method('getEntreFechas')
            ->with(
                $this->callback(function (Carbon $desde) {
                    // Debe ajustarse al 3/3/2026 por configuracion_calendario
                    return $desde->toDateString() === '2026-03-03';
                }),
                $this->anything()
            )
            ->willReturn(new Collection());

        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection());

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);

        $service->agendaSemana(Carbon::create(2026, 3, 1));
    }

    public function test_agenda_semana_marca_fines_de_semana_y_dias_sin_clase()
    {
        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);

        $asignacionRepo->method('getEntreFechas')->willReturn(new Collection());

        // Semana del lunes 2026-03-02 al domingo 2026-03-08
        $inicio = Carbon::create(2026, 3, 2); // lunes
        $fin = $inicio->copy()->addDays(6);

        // Marcamos el miércoles 2026-03-04 como día sin clase
        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection([
            Carbon::create(2026, 3, 4),
        ]));

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);

        $filas = $service->agendaSemana($inicio);

        $this->assertCount(7, $filas);

        $porFecha = collect($filas)->keyBy('fecha');

        $miercoles = $porFecha['2026-03-04'];
        $this->assertTrue($miercoles['es_feriado']);
        $this->assertSame('Día sin clase', $miercoles['etiqueta_feriado']);

        $sabado = $porFecha['2026-03-07'];
        $this->assertTrue($sabado['es_feriado']);
        $this->assertSame('Fin de semana', $sabado['etiqueta_feriado']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // proximaDiaLectivoConMerienda
    // ──────────────────────────────────────────────────────────────────────────

    private function asignacionParaFecha(string $ymd, string $cereal, int $frutaId = 5, int $elabId = 6): Asignacion
    {
        $asig = new Asignacion();
        $asig->fill(['fecha' => $ymd, 'cereal' => $cereal, 'alumno_fruta_id' => $frutaId, 'alumno_elaboracion_id' => $elabId]);

        $alumnoFruta = new Alumno();
        $alumnoFruta->id = $frutaId;
        $alumnoFruta->nombre = 'Alumno ' . $frutaId;

        $alumnoElab = new Alumno();
        $alumnoElab->id = $elabId;
        $alumnoElab->nombre = 'Alumno ' . $elabId;

        $asig->setRelation('alumnoFruta', $alumnoFruta);
        $asig->setRelation('alumnoElaboracion', $alumnoElab);

        return $asig;
    }

    private function familiaConAlumno(int $familiaId, Alumno $alumno): Familia
    {
        $familia = new Familia();
        $familia->id = $familiaId;
        $familia->setRelation('alumnos', new Collection([$alumno]));
        return $familia;
    }

    public function test_proxima_dia_lectivo_retorna_hoy_con_etiqueta_hoy_si_antes_15(): void
    {
        // Lunes 2026-05-11 a las 10:00
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 10, 0, 0));

        $asig = $this->asignacionParaFecha('2026-05-11', 'Arroz');

        $alumnoFruta = $asig->alumnoFruta;
        $alumnoFruta->setRelation('familia', $this->familiaConAlumno(1, $alumnoFruta));

        $alumnoElab = $asig->alumnoElaboracion;
        $alumnoElab->setRelation('familia', $this->familiaConAlumno(2, $alumnoElab));

        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        $asignacionRepo->method('getEntreFechas')->willReturn(new Collection([$asig]));

        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);
        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection());

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('find')->willReturnCallback(fn(int $id) => match ($id) {
            5 => $alumnoFruta,
            6 => $alumnoElab,
            default => null,
        });
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);
        $result = $service->proximaDiaLectivoConMerienda();

        $this->assertNotNull($result);
        $this->assertSame('2026-05-11', $result['fecha']);
        $this->assertSame('Hoy', $result['etiqueta']);
        $this->assertSame('Arroz', $result['cereal']);
        $this->assertNotEmpty($result['familia_fruta']);
        $this->assertNotEmpty($result['familia_elaboracion']);

        Carbon::setTestNow(null);
    }

    public function test_proxima_dia_lectivo_salta_hoy_y_retorna_manana_si_despues_de_15(): void
    {
        // Lunes 2026-05-11 a las 15:30 → cursor = martes
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 15, 30, 0));

        $asigMartes = $this->asignacionParaFecha('2026-05-12', 'Cebada');
        $alumnoFruta = $asigMartes->alumnoFruta;
        $alumnoFruta->setRelation('familia', $this->familiaConAlumno(1, $alumnoFruta));
        $alumnoElab = $asigMartes->alumnoElaboracion;
        $alumnoElab->setRelation('familia', $this->familiaConAlumno(2, $alumnoElab));

        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        $asignacionRepo->method('getEntreFechas')->willReturn(new Collection([$asigMartes]));

        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);
        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection());

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('find')->willReturnCallback(fn(int $id) => match ($id) {
            5 => $alumnoFruta,
            6 => $alumnoElab,
            default => null,
        });
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);
        $result = $service->proximaDiaLectivoConMerienda();

        $this->assertNotNull($result);
        $this->assertSame('2026-05-12', $result['fecha']);
        $this->assertSame('Mañana', $result['etiqueta']);

        Carbon::setTestNow(null);
    }

    public function test_proxima_dia_lectivo_salta_fines_de_semana(): void
    {
        // Sábado 2026-05-16 a las 10:00 → debe saltar sáb y dom, y llegar al lunes
        Carbon::setTestNow(Carbon::create(2026, 5, 16, 10, 0, 0));

        $asigLunes = $this->asignacionParaFecha('2026-05-18', 'Arroz');
        $alumnoFruta = $asigLunes->alumnoFruta;
        $alumnoFruta->setRelation('familia', $this->familiaConAlumno(1, $alumnoFruta));
        $alumnoElab = $asigLunes->alumnoElaboracion;
        $alumnoElab->setRelation('familia', $this->familiaConAlumno(2, $alumnoElab));

        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        // getEntreFechas solo se llama para el lunes (sáb y dom se saltan antes)
        $asignacionRepo->method('getEntreFechas')->willReturn(new Collection([$asigLunes]));

        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);
        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection());

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('find')->willReturnCallback(fn(int $id) => match ($id) {
            5 => $alumnoFruta,
            6 => $alumnoElab,
            default => null,
        });
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);
        $result = $service->proximaDiaLectivoConMerienda();

        $this->assertNotNull($result);
        $this->assertSame('2026-05-18', $result['fecha']); // lunes
        $this->assertSame('El Lunes', $result['etiqueta']);

        Carbon::setTestNow(null);
    }

    public function test_proxima_dia_lectivo_retorna_null_si_no_hay_merienda_asignada(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 10, 0, 0));

        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        // Nunca hay asignaciones (cereal vacío)
        $asignacionRepo->method('getEntreFechas')->willReturn(new Collection());

        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);
        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection());

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);
        $result = $service->proximaDiaLectivoConMerienda();

        $this->assertNull($result);

        Carbon::setTestNow(null);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // agendaSemanaConFamilias
    // ──────────────────────────────────────────────────────────────────────────

    public function test_agenda_semana_con_familias_enriquece_filas_con_nombre_de_familia(): void
    {
        // Semana del 2026-05-11 (lunes) al 2026-05-17
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 9, 0, 0));

        $alumnoFruta = new Alumno();
        $alumnoFruta->id = 5;
        $alumnoFruta->nombre = 'Lucía';
        $familiaFruta = new Familia();
        $familiaFruta->id = 10;
        $familiaFruta->setRelation('alumnos', new Collection([$alumnoFruta]));
        $alumnoFruta->setRelation('familia', $familiaFruta);

        $alumnoElab = new Alumno();
        $alumnoElab->id = 6;
        $alumnoElab->nombre = 'Tomás';
        $familiaElab = new Familia();
        $familiaElab->id = 11;
        $familiaElab->setRelation('alumnos', new Collection([$alumnoElab]));
        $alumnoElab->setRelation('familia', $familiaElab);

        $asig = $this->asignacionParaFecha('2026-05-11', 'Arroz');
        $asig->setRelation('alumnoFruta', $alumnoFruta);
        $asig->setRelation('alumnoElaboracion', $alumnoElab);

        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        $asignacionRepo->method('getEntreFechas')->willReturn(new Collection([$asig]));

        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);
        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection());

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('find')->willReturnCallback(fn(int $id) => match ($id) {
            5 => $alumnoFruta,
            6 => $alumnoElab,
            default => null,
        });
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);
        $filas = $service->agendaSemanaConFamilias(Carbon::create(2026, 5, 11));

        $porFecha = collect($filas)->keyBy('fecha');
        $filaLunes = $porFecha['2026-05-11'];

        $this->assertFalse($filaLunes['es_feriado']);
        $this->assertSame('Lucía', $filaLunes['fruta']['familia_nombre']);
        $this->assertSame('Tomás', $filaLunes['elaboracion']['familia_nombre']);

        Carbon::setTestNow(null);
    }

    public function test_agenda_semana_con_familias_no_toca_filas_feriado(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 5, 11, 9, 0, 0));

        $asignacionRepo = $this->createMock(AsignacionRepositoryInterface::class);
        $asignacionRepo->method('getEntreFechas')->willReturn(new Collection());

        $diaSinClaseRepo = $this->createMock(DiaSinClaseRepositoryInterface::class);
        // Martes marcado como día sin clase
        $diaSinClaseRepo->method('fechasEntre')->willReturn(new Collection([
            Carbon::create(2026, 5, 12),
        ]));

        $alumnoRepo = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepo->method('find')->willReturn(null);
        $alumnoRepo->method('activos')->willReturn(new Collection());

        $service = new AgendaService($asignacionRepo, $diaSinClaseRepo, $alumnoRepo);
        $filas = $service->agendaSemanaConFamilias(Carbon::create(2026, 5, 11));

        $porFecha = collect($filas)->keyBy('fecha');
        $filaMartes = $porFecha['2026-05-12'];

        $this->assertTrue($filaMartes['es_feriado']);
        // Las filas feriado no deben tener familia_nombre inyectado
        $this->assertArrayNotHasKey('familia_nombre', $filaMartes['fruta']);

        Carbon::setTestNow(null);
    }
}

