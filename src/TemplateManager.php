<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:25 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2024-12-05 12:03:33
 */
namespace ViraNet\TemplatePluginManager;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use ViraNet\TemplatePluginManager\Services\RouteManager;

class TemplateManager
{
    public function installTemplate($templateName, $path)
    {
        $templatePath = storage_path('app/templates/'.$templateName);
        File::copyDirectory($path, $templatePath);

        // بارگذاری صفحات اختصاصی و روت‌ها
        $this->loadTemplatePages($templateName);
        RouteManager::loadTemplateRoutes($templateName);

        return "Template installed successfully!";
    }

    public function activateTemplate($templateName)
    {
        self::loadTemplatePages($templateName);
        // فعال‌سازی قالب
        return "Template {$templateName} activated.";
    }

    public function deactivateTemplate($templateName)
    {
        // غیر فعال‌سازی قالب
        return "Template {$templateName} deactivated.";
    }

    public function deleteTemplate($templateName)
    {
        $templatePath = storage_path('app/templates/'.$templateName);

        if (File::exists($templatePath)) {
            File::deleteDirectory($templatePath);
            return "Template deleted successfully!";
        }

        return "Template not found.";
    }

    // متد جدید برای دریافت اطلاعات قالب
    public function getTemplateMetadata($templateName)
    {
        $metadataPath = storage_path('app/templates/'.$templateName.'/metadata.json');

        if (File::exists($metadataPath)) {
            return json_decode(File::get($metadataPath), true);
        }

        return null;
    }

    // متد جدید برای بررسی بروزرسانی قالب
    public function checkForTemplateUpdates($templateName)
    {
        $metadata = $this->getTemplateMetadata($templateName);

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

    // متد جدید برای دانلود قالب
    public function downloadTemplate($templateName)
    {
        $metadata = $this->getTemplateMetadata($templateName);

        if ($metadata) {
            $downloadUrl = $metadata['download_url'];

            // دانلود از URL
            $filePath = storage_path('app/templates/'.$templateName.'.zip');
            file_put_contents($filePath, file_get_contents($downloadUrl));

            return "Template downloaded successfully!";
        }

        return "Template not found.";
    }

    public function loadTemplatePages($templateName)
    {
        $templatePath = storage_path('app/templates/'.$templateName.'/views');

        if (File::exists($templatePath)) {
            View::addNamespace('template', $templatePath);
        }
    }
}
