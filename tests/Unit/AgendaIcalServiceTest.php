<?php

namespace Tests\Unit;

use App\Domain\Services\AgendaIcalService;
use PHPUnit\Framework\TestCase;

class AgendaIcalServiceTest extends TestCase
{
    private AgendaIcalService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AgendaIcalService;
    }

    public function test_url_google_calendar_template_includes_action_dates_and_text(): void
    {
        $fila = [
            'fecha' => '2026-04-15',
            'cereal' => 'Avena',
            'fruta' => ['nombre' => 'Manzana'],
            'elaboracion' => ['nombre' => 'Galletas'],
            'es_feriado' => false,
        ];

        $url = $this->service->urlGoogleCalendarTemplate($fila);

        $this->assertStringContainsString('https://calendar.google.com/calendar/render?', $url);
        $query = parse_url($url, PHP_URL_QUERY);
        $this->assertIsString($query);
        parse_str($query, $params);

        $this->assertSame('TEMPLATE', $params['action']);
        $this->assertSame('20260415/20260416', $params['dates']);
        $this->assertStringStartsWith('Merienda:', $params['text']);
        $this->assertStringContainsString('Avena', $params['text']);
        $this->assertStringContainsString('Cereal: Avena', $params['details']);
        $this->assertStringContainsString('Fruta: Manzana', $params['details']);
        $this->assertStringContainsString('Elaboración: Galletas', $params['details']);
    }

    public function test_url_google_calendar_fallback_when_fecha_invalid(): void
    {
        $this->assertSame(
            'https://calendar.google.com/calendar/u/0/r',
            $this->service->urlGoogleCalendarTemplate(['fecha' => 'invalid'])
        );
    }

    public function test_resumen_feriado_sin_datos_incluye_dia_sin_clase(): void
    {
        $fila = [
            'fecha' => '2026-01-01',
            'es_feriado' => true,
        ];

        $this->assertSame('Merienda: Día sin clase', $this->service->resumenMeriendaFila($fila));
    }
}
