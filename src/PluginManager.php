<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:22 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2024-12-05 11:46:05
 */
namespace ViraNet\TemplatePluginManager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use ViraNet\TemplatePluginManager\Services\RouteManager;
use ViraNet\TemplatePluginManager\Services\LicenseService;

class PluginManager
{
    public function installPlugin($pluginName, $path, $licenseKey)
    {
        $pluginPath = storage_path('app/plugins/'.$pluginName);
        File::copyDirectory($path, $pluginPath);

        // تأیید لایسنس
        if (!LicenseService::validateLicense($licenseKey)) {
            return "Invalid license.";
        }

        // بارگذاری روت‌ها
        RouteManager::loadPluginRoutes($pluginName);

        return "Plugin installed successfully!";
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

    // متد جدید برای دریافت اطلاعات افزونه
    public function getPluginMetadata($pluginName)
    {
        $metadataPath = storage_path('app/plugins/'.$pluginName.'/metadata.json');

        if (File::exists($metadataPath)) {
            return json_decode(File::get($metadataPath), true);
        }

        return null;
    }

    // متد جدید برای بررسی بروزرسانی افزونه
    public function checkForUpdates($pluginName)
    {
        $metadata = $this->getPluginMetadata($pluginName);

        if ($metadata) {
            $currentVersion = $metadata['version'];
            $latestVersion = $metadata['latest_version'];

            if (version_compare($currentVersion, $latestVersion, '<')) {
                return [
                    'update_available' => true,
                    'latest_version' => $latestVersion,
                    'download_url' => $metadata['download_url']
                ];
            }

            return ['update_available' => false];
        }

        return null;
    }

    // متد جدید برای دانلود افزونه
    public function downloadPlugin($pluginName)
    {
        $metadata = $this->getPluginMetadata($pluginName);

        if ($metadata) {
            $downloadUrl = $metadata['download_url'];

            // دانلود از URL
            $filePath = storage_path('app/plugins/'.$pluginName.'.zip');
            file_put_contents($filePath, file_get_contents($downloadUrl));

            return "Plugin downloaded successfully!";
        }

        return "Plugin not found.";
    }
}
