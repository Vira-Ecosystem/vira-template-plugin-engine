<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:46 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2024-12-05 11:13:16
 */
namespace ViraNet\TemplatePluginManager\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class RouteManager
{
    public static function loadPluginRoutes($pluginName)
    {
        $routeFile = public_path('packages/plugins/'.$pluginName.'/routes/web.php');
        if (File::exists($routeFile)) {
            require $routeFile;
        }
    }

    public static function loadTemplateRoutes($templateName)
    {
        $routeFile = public_path('packages/templates/'.$templateName.'/routes/web.php');
        if (File::exists($routeFile)) {
            require $routeFile;
        }
    }
}
