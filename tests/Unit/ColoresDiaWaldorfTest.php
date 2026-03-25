<?php

namespace Tests\Unit;

use App\Support\ColoresDiaWaldorf;
use PHPUnit\Framework\TestCase;

class ColoresDiaWaldorfTest extends TestCase
{
    public function test_clase_fila_agenda_feriado_vacia(): void
    {
        $this->assertSame('', ColoresDiaWaldorf::claseFilaAgenda('2026-04-06', true));
    }

    public function test_clase_fila_agenda_lunes_a_viernes(): void
    {
        $this->assertSame('agenda-col-lun', ColoresDiaWaldorf::claseFilaAgenda('2026-04-06', false));
        $this->assertSame('agenda-col-mar', ColoresDiaWaldorf::claseFilaAgenda('2026-04-07', false));
        $this->assertSame('agenda-col-mie', ColoresDiaWaldorf::claseFilaAgenda('2026-04-08', false));
        $this->assertSame('agenda-col-jue', ColoresDiaWaldorf::claseFilaAgenda('2026-04-09', false));
        $this->assertSame('agenda-col-vie', ColoresDiaWaldorf::claseFilaAgenda('2026-04-10', false));
    }

    public function test_clase_fila_agenda_fin_de_semana_sin_feriado_flag_sigue_vacia(): void
    {
        $this->assertSame('', ColoresDiaWaldorf::claseFilaAgenda('2026-04-11', false));
    }
}
