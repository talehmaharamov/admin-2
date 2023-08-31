<?php

namespace App\Console\Commands\Controllers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateControllers extends Command
{
    protected $signature = 'generate:controllers {controller} {--a : Modify API controller} {--f : Modify frontend controller}';
    protected $description = 'Modify controller functions';

    public function handle(): void
    {
        $controllerName = $this->argument('controller');
        $isApi = $this->option('a');
        $isFrontend = $this->option('f');
        if ($isApi && $isFrontend) {
            $this->modifyOrCreateController($controllerName, 'Api');
            $this->modifyOrCreateController($controllerName, 'Frontend');
        } elseif ($isApi) {
            $this->modifyOrCreateController($controllerName, 'Api');
        } elseif ($isFrontend) {
            $this->modifyOrCreateController($controllerName, 'Frontend');
        }
        $this->modifyOrCreateController($controllerName, 'Backend');
    }

    protected function modifyOrCreateController($controllerName, $directory): void
    {
        $controllerPath = app_path("Http/Controllers/{$directory}/{$controllerName}Controller.php");

        if (!File::exists($controllerPath)) {
            $this->call('make:controller', [
                'name' => "{$directory}\\{$controllerName}Controller",
            ]);
        }

        if (File::exists($controllerPath)) {
            $controllerPath = app_path("Http/Controllers/{$directory}/{$controllerName}Controller.php");
            $stubPath = base_path("app/Utils/stubs/controllers/{$directory}.stub");
            if (File::exists($stubPath)) {
                $content = File::get($stubPath);
                $content = str_replace('$name', Str::lower($controllerName), $content);
                $content = str_replace('$controller', $controllerName, $content);
                File::put($controllerPath, $content);
                $this->info("{$directory} Controller content replaced with the custom stub.");
            } else {
                $this->error("Custom {$directory} stub file not found.");
            }
            $this->info("{$directory} Controller modified or created.");
        } else {
            $this->error("{$directory} Controller file not found.");
        }
    }
}
