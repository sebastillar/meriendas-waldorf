<?php

namespace Tests\Unit;

use App\Domain\Models\ConfiguracionCalendario;
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
}

