<?php

namespace App\Console\Commands\Models;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationModel extends Command
{
    protected $signature = 'add:model {model} {--t : Generate translation model} {--p : Generate photos model}';
    protected $description = 'Generate different types of models';

    public function handle(): void
    {
        $modelName = $this->argument('model');
        $generateTranslationModel = $this->option('t');
        $generatePhotosModel = $this->option('p');

        $this->generateModel($modelName, 'Main');

        if ($generateTranslationModel) {
            $this->generateModel($modelName, 'Translation');
        }

        if ($generatePhotosModel) {
            $this->generateModel($modelName, 'Photos');
        }
    }

    protected function generateModel($modelName, $modelType): void
    {
        if ($modelType !== 'Main') {
            $modelName .= $modelType;
        }

        $controllerPath = app_path("Models/{$modelName}.php");
        $stubPath = base_path("app/Utils/stubs/models/{$modelType}.stub");

        if (File::exists($stubPath)) {
            $content = File::get($stubPath);
            $content = str_replace('$name', Str::lower($modelName), $content);
            $content = str_replace('$controller', $modelName, $content);

            File::put($controllerPath, $content);

            $this->info("{$modelType} model content replaced with the custom stub.");
        } else {
            $this->error('Custom stub file not found.');
        }
    }
}
