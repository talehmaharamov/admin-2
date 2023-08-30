<?php

namespace App\Console\Commands\Routes;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateRoutes extends Command
{
    protected $signature = 'generate:routes {controller} {--api} {--status} {--resource} {--delete}';
    protected $description = 'Generate different types of routes';

    public function handle(): void
    {
        $controller = $this->argument('controller');
        $controllerName = ucfirst($controller) . 'Controller';
        $routeName = Str::lower($controller);
        $controllerNamespace = Str::of("App\Http\Controllers\Backend\\")->append($controllerName);

        if ($this->option('api')) {
            $this->generateApiRoutes($controllerNamespace, $routeName);
        }

        if ($this->option('status')) {
            $this->generateStatusRoutes($controllerNamespace, $routeName);
        }

        if ($this->option('resource')) {
            $this->generateResourceRoutes($controllerNamespace, $routeName);
        }

        if ($this->option('delete')) {
            $this->generateDeleteRoutes($controllerNamespace, $routeName);
        }
        $this->formatRouteFile('api');
        $this->formatRouteFile('admin');
        $this->info('Routes generated successfully!');
    }

    private function generateApiRoutes($controllerNamespace, $routeName): void
    {
        $routePath = base_path('routes/api.php');
        $routeContents = file_get_contents($routePath);

        $newRoute = "\nRoute::get('/{$routeName}', [{$controllerNamespace}::class, 'index']);";
        $newShowRoute = "\nRoute::get('/{$routeName}/{id}', [{$controllerNamespace}::class, 'show']);";

        $pattern = "/Route::group\(\[\s*'name'\s*=>\s*'api'\s*\],\s*function\s*\(\)\s*{/";
        preg_match($pattern, $routeContents, $matches, PREG_OFFSET_CAPTURE);
        $offset = $matches[0][1] + strlen($matches[0][0]);

        $routeContents = substr_replace($routeContents, $newShowRoute, $offset, 0);
        $routeContents = substr_replace($routeContents, $newRoute, $offset, 0);
        file_put_contents($routePath, $routeContents, LOCK_EX);

        $this->info('API routes added successfully!');
    }

    private function generateStatusRoutes($controllerNamespace, $routeName): void
    {
        $routePath = base_path('routes/admin.php');
        $routeContents = file_get_contents($routePath);

        $newRoute = "\nRoute::get('/{$routeName}/{id}/change-status', [{$controllerNamespace}::class, 'status'])";
        $newRoute .= "->name('{$routeName}Status');";

        $pattern = "/Route::group\(\[\s*'name'\s*=>\s*'status'\s*\],\s*function\s*\(\)\s*{/";
        preg_match($pattern, $routeContents, $matches, PREG_OFFSET_CAPTURE);
        $offset = $matches[0][1] + strlen($matches[0][0]);

        $routeContents = substr_replace($routeContents, $newRoute, $offset, 0);
        file_put_contents($routePath, $routeContents, LOCK_EX);

        $this->info('Status routes added successfully!');
    }

    private function generateResourceRoutes($controllerNamespace, $routeName): void
    {
        $routePath = base_path('routes/admin.php');
        $routeContents = file_get_contents($routePath);

        $newRoute = "\nRoute::resource('/{$routeName}', {$controllerNamespace}::class);";

        $pattern = "/Route::group\(\[\s*'name'\s*=>\s*'resource'\s*\],\s*function\s*\(\)\s*{/";
        preg_match($pattern, $routeContents, $matches, PREG_OFFSET_CAPTURE);
        $offset = $matches[0][1] + strlen($matches[0][0]);

        $routeContents = substr_replace($routeContents, $newRoute, $offset, 0);
        file_put_contents($routePath, $routeContents, LOCK_EX);

        $this->info('Resource routes added successfully!');
    }

    private function generateDeleteRoutes($controllerNamespace, $routeName): void
    {
        $routePath = base_path('routes/admin.php');
        $routeContents = file_get_contents($routePath);

        $newRoute = "\nRoute::get('/{$routeName}/{id}/delete', [{$controllerNamespace}::class, 'delete'])";
        $newRoute .= "->name('{$routeName}Delete');";

        $pattern = "/Route::group\(\[\s*'name'\s*=>\s*'delete'\s*\],\s*function\s*\(\)\s*{/";
        preg_match($pattern, $routeContents, $matches, PREG_OFFSET_CAPTURE);
        $offset = $matches[0][1] + strlen($matches[0][0]);

        $routeContents = substr_replace($routeContents, $newRoute, $offset, 0);
        file_put_contents($routePath, $routeContents, LOCK_EX);

        $this->info('Delete routes added successfully!');
    }

    private function formatRouteFile($fileType): void
    {
        $filePath = base_path("routes/{$fileType}.php");
        $routeFileContents = File::get($filePath);

        $formattedContents = ltrim($routeFileContents, "<?php");
        $formattedContents = trim($formattedContents);

        $lines = explode("\n", $formattedContents);
        $formattedLines = [];

        foreach ($lines as $line) {
            $formattedLines[] = trim($line);
        }

        $formattedContents = "<?php\n\n" . implode("\n", $formattedLines) . "\n";

        File::put($filePath, $formattedContents, LOCK_EX);

        $this->info("{$fileType}.php route file formatted successfully!");
    }

}
