<?php

namespace Laluciole\GdprForms;

use Laluciole\GdprForms\Actions\MarkAsDone;
use Laluciole\GdprForms\Actions\MarkAsTodo;
use Laluciole\GdprForms\Widgets\Formsubmissions;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\CP\Nav;
use Laluciole\GdprForms\Console\Commands\GdprCheck;
use Statamic\Facades\Permission;

class ServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
        'console' => __DIR__.'/../routes/console.php',
    ];

    protected $widgets = [
        Formsubmissions::class
    ];

    protected $actions = [
        MarkAsDone::class,
        MarkAsTodo::class
    ];

    protected $commands = [
        GdprCheck::class
    ];

    protected $vite = [
        'input' => [
            'resources/js/request.js'
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang','general');
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang');

        Permission::group('gdpr-forms', 'GDPR', function () {
            Permission::register('gdpr-utility', function ($permission) {
                $permission->label('GDPR Utility')->description("Grants access to the GDPR utility");
            });
        });

        Nav::extend(function (\Statamic\CP\Navigation\Nav $nav) {
            $nav->tools("Gestion RGPD")
                ->route('statamic.cp.gdpr.index')
                ->can('gdpr-utility')
                ->icon('checkmark');
        });

    }
    /**
     * Register any application services.
     *
     * @return void
    */
    public function register()
    {
        $toOverride = collect([
            // Form RGPD
            \Statamic\Http\Controllers\CP\Forms\FormsController::class =>
                \Laluciole\GdprForms\Overrides\Http\Controllers\FormsController::class,
        ]);

        $toOverride->each(function($classOverriding, $classToOverride) {
            $this->app->bind(
                $classToOverride,
                $classOverriding
            );
        });

        // $this->mergeConfigFrom(__DIR__.'/../config/gdpr.php', 'gdpr');
    }
}
