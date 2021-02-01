<?php

namespace App\Providers;

use App\Domain\TransaccionDomainImp;
use App\Domain\TransaccionDomainInterface;
use App\Repositories\LogTransaccionesRepository\LogTransaccionesImp;
use App\Repositories\LogTransaccionesRepository\LogTransaccionesInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\MontoRepository\MontoRepositoryImp;
use App\Repositories\MontoRepository\MontoRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            TransaccionDomainInterface::class,
            TransaccionDomainImp::class
        );
        $this->app->bind(
            MontoRepositoryInterface::class,
            MontoRepositoryImp::class
        );
        $this->app->bind(
            LogTransaccionesInterface::class,
            LogTransaccionesImp::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
