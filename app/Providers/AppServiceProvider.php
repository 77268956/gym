<?php

namespace App\Providers;

use App\Models\ConfiguracionGeneral;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $gymConfig = null;
            try {
                if (Schema::hasTable('configuraciones_generales')) {
                    $gymConfigData = Cache::remember('configuracion_general', 86400, function () {
                        $model = ConfiguracionGeneral::first();

                        return [
                            'nombre_gimnasio' => $model->nombre_gimnasio ?? 'EcoGim',
                            'logo_path' => $model->logo_path ?? null,
                            'simbolo_moneda' => $model->simbolo_moneda ?? 'L.',
                        ];
                    });

                    if (is_array($gymConfigData)) {
                        $gymConfig = (object) $gymConfigData;
                    }
                }
            } catch (\Throwable $e) {
                // Si ocurre algún error (ej. durante comandos de CLI o instalación inicial)
            }

            if (! $gymConfig || ! is_object($gymConfig) || $gymConfig instanceof \__PHP_Incomplete_Class) {
                $gymConfig = (object) [
                    'nombre_gimnasio' => 'EcoGim',
                    'logo_path' => null,
                    'simbolo_moneda' => 'L.',
                ];
            }

            $view->with('gymConfig', $gymConfig);
        });
    }
}
