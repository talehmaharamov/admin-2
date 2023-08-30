<?php

namespace App\Console\Commands\lang;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddTranslationKey extends Command
{
    protected $signature = 'translation:add {key} {values*}';
    protected $description = 'Add translations for a key in language files';

    public function handle(): void
    {
        $key = $this->argument('key');
        $values = $this->argument('values');

        $langPath = resource_path('lang');
        $langDirs = File::directories($langPath);

        foreach ($langDirs as $index => $langDir) {
            if (strpos($langDir, 'vendor') !== false) {
                continue;
            }

            $langCode = basename($langDir);
            $langFile = $langDir . '/backend.php';
            $langData = require $langFile;

            foreach ($values as $valueIndex => $value) {
                if ($index === $valueIndex) {
                    $langData[$key] = $value;
                    File::put($langFile, '<?php return ' . var_export($langData, true) . ';');
                    $this->info("Added translation value [$value] to language file [$langCode] with key [$key].");
                    break;
                }
            }
        }

        $this->info('All language files updated.');
    }
}
