<?php

namespace Tests\Unit;

use App\Domain\Models\Alumno;
use App\Domain\Models\Familia;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use App\Domain\Services\RecolectaAportesService;
use App\Domain\Services\RecolectandoService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RecolectandoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_recolectas_del_mes_actual_filtra_por_mes_y_estado()
    {
        Carbon::setTestNow(Carbon::create(2026, 3, 10));

        $familiaBeneficiaria = new Familia();
        $familiaBeneficiaria->id = 1;

        $familiaRecolectora = new Familia();
        $familiaRecolectora->id = 2;
        $familiaRecolectora->familia_regalo_id = 1;

        $alumnoConCumpleEnMes = new Alumno();
        $alumnoConCumpleEnMes->id = 10;
        $alumnoConCumpleEnMes->nombre = 'Alumno Marzo';
        $alumnoConCumpleEnMes->familia_id = 1;
        $alumnoConCumpleEnMes->fecha_cumpleanos = Carbon::create(2015, 3, 20);
        $alumnoConCumpleEnMes->setRelation('familia', $familiaBeneficiaria);

        $alumnoFueraDeMes = new Alumno();
        $alumnoFueraDeMes->id = 11;
        $alumnoFueraDeMes->nombre = 'Alumno Abril';
        $alumnoFueraDeMes->familia_id = 1;
        $alumnoFueraDeMes->fecha_cumpleanos = Carbon::create(2015, 4, 5);
        $alumnoFueraDeMes->setRelation('familia', $familiaBeneficiaria);

        $alumnoRepository = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepository->method('activos')->willReturn(new Collection([
            $alumnoConCumpleEnMes,
            $alumnoFueraDeMes,
        ]));

        $familiaRepository = $this->createMock(FamiliaRepositoryInterface::class);
        $familiaRepository->method('activas')->willReturn(new Collection([
            $familiaRecolectora,
        ]));

        $recolectaAportes = $this->createMock(RecolectaAportesService::class);
        $recolectaAportes->method('getAlumnoIdsQueAportaron')->willReturn([1, 2]);
        $recolectaAportes->method('alumnosParaAportes')->willReturn([1, 2, 3, 4]);

        $service = new RecolectandoService(
            $alumnoRepository,
            $familiaRepository,
            $recolectaAportes
        );

        $result = $service->recolectasDelMesActual();

        $this->assertCount(1, $result);
        $this->assertSame('activa', $result[0]['estado']);
        $this->assertEquals('2026-03-20', $result[0]['fecha_cumpleanos']->toDateString());
        $this->assertSame(2, $result[0]['aportaron_count']);
        $this->assertSame(4, $result[0]['total_count']);
    }

    public function test_recolectas_del_mes_actual_devuelve_todos_los_cumpleanos_del_mes_en_orden_y_con_estado()
    {
        Carbon::setTestNow(Carbon::create(2026, 3, 10));

        $familiaBeneficiaria1 = new Familia();
        $familiaBeneficiaria1->id = 1;

        $familiaBeneficiaria2 = new Familia();
        $familiaBeneficiaria2->id = 2;

        $familiaRecolectora = new Familia();
        $familiaRecolectora->id = 20;
        $familiaRecolectora->familia_regalo_id = 1;

        $alumnoDia20 = new Alumno();
        $alumnoDia20->id = 10;
        $alumnoDia20->nombre = 'Alumno Dia 20';
        $alumnoDia20->familia_id = 1;
        $alumnoDia20->fecha_cumpleanos = Carbon::create(2015, 3, 20);
        $alumnoDia20->setRelation('familia', $familiaBeneficiaria1);

        $alumnoDia05 = new Alumno();
        $alumnoDia05->id = 11;
        $alumnoDia05->nombre = 'Alumno Dia 05';
        $alumnoDia05->familia_id = 2;
        $alumnoDia05->fecha_cumpleanos = Carbon::create(2014, 3, 5);
        $alumnoDia05->setRelation('familia', $familiaBeneficiaria2);

        $alumnoRepository = $this->createMock(AlumnoRepositoryInterface::class);
        $alumnoRepository->method('activos')->willReturn(new Collection([
            $alumnoDia20,
            $alumnoDia05,
        ]));

        $familiaRepository = $this->createMock(FamiliaRepositoryInterface::class);
        $familiaRepository->method('activas')->willReturn(new Collection([
            $familiaRecolectora,
        ]));

        $recolectaAportes = $this->createMock(RecolectaAportesService::class);
        $recolectaAportes->method('getAlumnoIdsQueAportaron')->willReturn([1, 2]);
        $recolectaAportes->method('alumnosParaAportes')->willReturn([1, 2, 3, 4]);

        $service = new RecolectandoService(
            $alumnoRepository,
            $familiaRepository,
            $recolectaAportes
        );

        $result = $service->recolectasDelMesActual();

        $this->assertCount(2, $result);

        $this->assertSame('Alumno Dia 05', $result[0]['alumno_beneficiario']->nombre);
        $this->assertEquals('2026-03-05', $result[0]['fecha_cumpleanos']->toDateString());
        $this->assertSame('sin_recolectora', $result[0]['estado']);
        $this->assertSame(2, $result[0]['aportaron_count']);
        $this->assertSame(4, $result[0]['total_count']);

        $this->assertSame('Alumno Dia 20', $result[1]['alumno_beneficiario']->nombre);
        $this->assertEquals('2026-03-20', $result[1]['fecha_cumpleanos']->toDateString());
        $this->assertSame('activa', $result[1]['estado']);
        $this->assertSame(2, $result[1]['aportaron_count']);
        $this->assertSame(4, $result[1]['total_count']);
    }
}

