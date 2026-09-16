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
                            'color_primario' => $model->color_primario ?? '#2563EB',
                            'color_primario_hover' => $model->color_primario_hover ?? '#1D4ED8',
                            'color_sidebar' => $model->color_sidebar ?? '#1E293B',
                            'color_sidebar_hover' => $model->color_sidebar_hover ?? '#334155',
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
                    'color_primario' => '#2563EB',
                    'color_primario_hover' => '#1D4ED8',
                    'color_sidebar' => '#1E293B',
                    'color_sidebar_hover' => '#334155',
                ];
            }

            $view->with('gymConfig', $gymConfig);
        });
    }
}
