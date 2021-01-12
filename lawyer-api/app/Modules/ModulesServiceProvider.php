<?php namespace App\Modules;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $modules = config("module.modules");

        while (list(, $module) = each($modules)) {
            $module = ucfirst($module);
            $routesPath = __DIR__ . '/' . $module . '/routes';
            foreach (array_diff(scandir($routesPath),['.','..']) as $file) {
                $file = "{$routesPath}/{$file}";
                if (file_exists($file)) {
                    include $file;
                }
            }

            $views = __DIR__ . '/' . $module . '/resources/views';
            if (is_dir($views)) {
                $this->loadViewsFrom($views, $module);
            }
        }
    }

    public function register()
    {
    }

}