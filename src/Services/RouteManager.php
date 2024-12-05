<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:46 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2024-12-05 11:35:57
 */
namespace ViraNet\TemplatePluginManager\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class RouteManager
{
    /**
     * Loads the routes for a specific template if it exists.
     *
     * @param string $template
     * @return void
     */
    public static function loadTemplateRoutes($template)
    {
        $routeFile = base_path("packages/templates/{$template}/routes/web.php");

        if (file_exists($routeFile)) {
            Route::prefix("template/{$template}")->group(function() use ($routeFile) {
                require $routeFile;
            });
        }
    }

    /**
     * Loads the routes for a specific plugin if it exists.
     *
     * @param string $plugin
     * @return void
     */
    public static function loadPluginRoutes($plugin)
    {
        $routeFile = base_path("packages/plugins/{$plugin}/routes/web.php");

        if (file_exists($routeFile)) {
            Route::prefix("plugin/{$plugin}")->group(function() use ($routeFile) {
                require $routeFile;
            });
        }
    }

    /**
     * Loads the routes dynamically for all templates and plugins available.
     *
     * @return void
     */
    public static function loadAllRoutes()
    {
        $templates = File::directories(public_path('packages/templates'));
        foreach ($templates as $template) {
            $templateName = basename($template);
            self::loadTemplateRoutes($templateName);
        }

        $plugins = File::directories(public_path('packages/plugins'));
        foreach ($plugins as $plugin) {
            $pluginName = basename($plugin);
            self::loadPluginRoutes($pluginName);
        }
    }
}

