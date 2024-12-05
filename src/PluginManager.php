<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:22 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2024-12-05 11:31:40
 */
namespace ViraNet\TemplatePluginManager;

use Illuminate\Support\Facades\File;
use ViraNet\TemplatePluginManager\Services\RouteManager;
use ViraNet\TemplatePluginManager\Services\UpdateService;

class PluginManager
{
    protected $basePath;

    public function __construct()
    {
        $this->basePath = public_path('packages/plugins'); // مسیر جدید برای افزونه‌ها
    }

    public function installPlugin($pluginName, $path, $licenseKey)
    {
        $pluginPath = $this->basePath . '/' . $pluginName;
        File::copyDirectory($path, $pluginPath);

        // تأیید لایسنس و بارگذاری روت‌های افزونه
        RouteManager::loadPluginRoutes($pluginName);

        return "Plugin installed successfully!";
    }

    public function getPluginMetadata($pluginName)
    {
        $metadataPath = $this->basePath . "/{$pluginName}/metadata.json";
        if (File::exists($metadataPath)) {
            return json_decode(File::get($metadataPath), true);
        }

        return "Metadata not found.";
    }

    public function checkForPluginUpdates($pluginName)
    {
        $metadata = $this->getPluginMetadata($pluginName);
        if (is_array($metadata) && isset($metadata['update_url'])) {
            return UpdateService::checkForUpdates($metadata['update_url'], $metadata['version']);
        }

        return "No update URL found for plugin {$pluginName}.";
    }

    public function updatePlugin($pluginName)
    {
        $metadata = $this->getPluginMetadata($pluginName);
        if (is_array($metadata) && isset($metadata['update_url'])) {
            return UpdateService::downloadUpdate($metadata['update_url'], 'plugins', $pluginName);
        }

        return "Update not available for plugin {$pluginName}.";
    }


    public function activatePlugin($pluginName)
    {
        RouteManager::loadPluginRoutes($pluginName);
        return "Plugin {$pluginName} activated.";
    }

    public function deactivatePlugin($pluginName)
    {
        return "Plugin {$pluginName} deactivated.";
    }

    public function deletePlugin($pluginName)
    {
        $pluginPath = storage_path('app/plugins/'.$pluginName);

        if (File::exists($pluginPath)) {
            File::deleteDirectory($pluginPath);
            return "Plugin deleted successfully!";
        }

        return "Plugin not found.";
    }
}
