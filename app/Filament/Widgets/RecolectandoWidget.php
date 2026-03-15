<?php

namespace App\Filament\Widgets;

use App\Domain\Services\RecolectandoService;
use Filament\Widgets\Widget;

class RecolectandoWidget extends Widget
{
    protected static string $view = 'filament.widgets.recolectando-widget';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    public ?array $recolecta = null;

    public ?array $proximoCumpleanos = null;

    public function mount(RecolectandoService $recolectandoService): void
    {
        $this->refreshRecolectaData($recolectandoService);
    }

    protected function refreshRecolectaData(?RecolectandoService $service = null): void
    {
        $service = $service ?? app(RecolectandoService::class);
        $this->recolecta = $this->toArrayRecolecta($service->recolectaActual());
        $this->proximoCumpleanos = $this->recolecta === null ? $service->proximoCumpleanosSinRecolectora() : null;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->refreshRecolectaData();
        return parent::render();
    }

    private function toArrayRecolecta(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }
        $familia = $data['familia_recolectora'];
        $alumno = $data['alumno_beneficiario'];
        return [
            'alumno_beneficiario_nombre' => $alumno->nombre,
            'familia_recolectora_nombre' => $familia->nombre_para_listado,
            'monto_aportar' => config('meriendas.regalo.monto_aportar', 300),
            'banco' => $familia->banco,
            'numero_cuenta' => $familia->numero_cuenta,
            'tipo_cuenta' => $familia->tipo_cuenta,
            'nombre_cuenta' => $familia->nombre_cuenta,
            'moneda' => $familia->moneda,
            'aportaron_count' => $data['aportaron_count'] ?? 0,
            'total_count' => $data['total_count'] ?? 0,
        ];
    }
}
