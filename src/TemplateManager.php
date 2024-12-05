<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:25 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by:   Arvin.Loripour 
 * @Last Modified time: 2024-12-05 11:12:25 
 */
namespace ViraNet\TemplatePluginManager;

use Illuminate\Support\Facades\File;
use ViraNet\TemplatePluginManager\Services\RouteManager;
use ViraNet\TemplatePluginManager\Services\UpdateService;

class TemplateManager
{
    protected $basePath;

    public function __construct()
    {
        $this->basePath = public_path('packages/templates'); // مسیر جدید برای قالب‌ها
    }

    public function installTemplate($templateName, $path)
    {
        $templatePath = $this->basePath . '/' . $templateName;
        File::copyDirectory($path, $templatePath);

        // بارگذاری صفحات و روت‌های قالب
        $this->loadTemplatePages($templateName);
        RouteManager::loadTemplateRoutes($templateName);

        return "Template installed successfully!";
    }

    public function getTemplateMetadata($templateName)
    {
        $metadataPath = $this->basePath . "/{$templateName}/metadata.json";
        if (File::exists($metadataPath)) {
            return json_decode(File::get($metadataPath), true);
        }

        return "Metadata not found.";
    }

    public function checkForTemplateUpdates($templateName)
    {
        $metadata = $this->getTemplateMetadata($templateName);
        if (is_array($metadata) && isset($metadata['update_url'])) {
            return UpdateService::checkForUpdates($metadata['update_url'], $metadata['version']);
        }

        return "No update URL found for template {$templateName}.";
    }

    public function updateTemplate($templateName)
    {
        $metadata = $this->getTemplateMetadata($templateName);
        if (is_array($metadata) && isset($metadata['update_url'])) {
            return UpdateService::downloadUpdate($metadata['update_url'], 'templates', $templateName);
        }

        return "Update not available for template {$templateName}.";
    }

    public function loadTemplatePages($templateName)
    {
        $templatePath = $this->basePath . "/{$templateName}/views";

        if (File::exists($templatePath)) {
            View::addNamespace('template', $templatePath);
        }
    }
}
