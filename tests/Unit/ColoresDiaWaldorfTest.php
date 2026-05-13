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

    public function test_dias_semana_incluye_campos_nuevos_en_todos_los_dias(): void
    {
        foreach (ColoresDiaWaldorf::diasSemana() as $dia) {
            $this->assertArrayHasKey('simbolo_planeta', $dia, "Falta simbolo_planeta en {$dia['slug']}");
            $this->assertArrayHasKey('bg_tint_css', $dia, "Falta bg_tint_css en {$dia['slug']}");
            $this->assertArrayHasKey('border_color_css', $dia, "Falta border_color_css en {$dia['slug']}");
            $this->assertNotEmpty($dia['simbolo_planeta'], "simbolo_planeta vacío en {$dia['slug']}");
            $this->assertStringStartsWith('rgba(', $dia['bg_tint_css'], "bg_tint_css inválido en {$dia['slug']}");
            $this->assertStringStartsWith('rgba(', $dia['border_color_css'], "border_color_css inválido en {$dia['slug']}");
        }
    }

    public function test_info_por_dia_semana_retorna_datos_correctos_para_lunes_a_viernes(): void
    {
        $slugsEsperados = ['lun', 'mar', 'mie', 'jue', 'vie'];

        // Carbon dayOfWeek: 1=lunes, 2=martes, 3=miércoles, 4=jueves, 5=viernes
        foreach (range(1, 5) as $dow) {
            $info = ColoresDiaWaldorf::infoPorDiaSemana($dow);
            $this->assertNotNull($info, "infoPorDiaSemana({$dow}) no debe retornar null");
            $this->assertSame($slugsEsperados[$dow - 1], $info['slug']);
        }
    }

    public function test_info_por_dia_semana_retorna_null_para_sabado_y_domingo(): void
    {
        $this->assertNull(ColoresDiaWaldorf::infoPorDiaSemana(0)); // domingo Carbon
        $this->assertNull(ColoresDiaWaldorf::infoPorDiaSemana(6)); // sábado Carbon
    }

    public function test_info_por_dia_semana_lunes_tiene_simbolo_luna(): void
    {
        $info = ColoresDiaWaldorf::infoPorDiaSemana(1);
        $this->assertSame('☽', $info['simbolo_planeta']);
        $this->assertSame('Luna', $info['planeta']);
    }
}
