<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:37 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2024-12-05 11:13:32
 */
namespace ViraNet\TemplatePluginManager\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class UpdateService
{
    public static function checkForUpdates($updateUrl, $currentVersion)
    {
        $response = Http::get($updateUrl, ['version' => $currentVersion]);

        if ($response->successful() && isset($response['new_version'])) {
            return [
                'update_available' => true,
                'new_version' => $response['new_version'],
                'download_url' => $response['download_url']
            ];
        }

        return ['update_available' => false];
    }

    public static function downloadUpdate($updateUrl, $type, $name)
    {
        $response = Http::get($updateUrl);

        if ($response->successful()) {
            $storagePath = public_path("packages/{$type}/{$name}");
            File::put($storagePath.'.zip', $response->body());

            // استخراج فایل‌ها
            $zip = new \ZipArchive;
            if ($zip->open($storagePath.'.zip') === true) {
                $zip->extractTo(public_path("packages/{$type}/{$name}"));
                $zip->close();
                File::delete($storagePath.'.zip');
                return "Update for {$name} installed successfully.";
            }

            return "Failed to extract update package.";
        }

        return "Failed to download update for {$name}.";
    }
}
