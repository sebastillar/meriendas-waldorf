<?php

namespace App\Providers;

use App\Domain\Contracts\NotificadorIntercambioInterface;
use App\Domain\Contracts\RecordatorioNotifierInterface;
use App\Domain\Repositories\AlumnoRepositoryInterface;
use App\Domain\Repositories\AsignacionRepositoryInterface;
use App\Domain\Repositories\CerealPorDiaRepositoryInterface;
use App\Domain\Repositories\ConfiguracionRepositoryInterface;
use App\Domain\Repositories\DiaSinClaseRepositoryInterface;
use App\Domain\Repositories\Eloquent\AlumnoEloquentRepository;
use App\Domain\Repositories\Eloquent\AsignacionEloquentRepository;
use App\Domain\Repositories\Eloquent\CerealPorDiaEloquentRepository;
use App\Domain\Repositories\Eloquent\ConfiguracionEloquentRepository;
use App\Domain\Repositories\Eloquent\DiaSinClaseEloquentRepository;
use App\Domain\Repositories\Eloquent\FamiliaEloquentRepository;
use App\Domain\Repositories\Eloquent\RecolectaAporteEloquentRepository;
use App\Domain\Repositories\FamiliaRepositoryInterface;
use App\Domain\Repositories\RecolectaAporteRepositoryInterface;
use App\Notifications\NotificadorIntercambioMail;
use App\Notifications\RecordatorioNotifierMail;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AsignacionRepositoryInterface::class, AsignacionEloquentRepository::class);
        $this->app->bind(AlumnoRepositoryInterface::class, AlumnoEloquentRepository::class);
        $this->app->bind(FamiliaRepositoryInterface::class, FamiliaEloquentRepository::class);
        $this->app->bind(RecolectaAporteRepositoryInterface::class, RecolectaAporteEloquentRepository::class);
        $this->app->bind(CerealPorDiaRepositoryInterface::class, CerealPorDiaEloquentRepository::class);
        $this->app->bind(DiaSinClaseRepositoryInterface::class, DiaSinClaseEloquentRepository::class);
        $this->app->bind(ConfiguracionRepositoryInterface::class, ConfiguracionEloquentRepository::class);
        $this->app->bind(RecordatorioNotifierInterface::class, RecordatorioNotifierMail::class);
        $this->app->bind(NotificadorIntercambioInterface::class, NotificadorIntercambioMail::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
