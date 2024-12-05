<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:15:57 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by:   Arvin.Loripour 
 * @Last Modified time: 2024-12-05 11:15:57 
 */
namespace ViraNet\TemplatePluginManager\Services;

use Illuminate\Support\Facades\Http;

class LicenseService
{
    public static function validateLicense($licenseKey)
    {
        $response = Http::post(config('viranet-tp-engine.license_api_url'), [
            'license_key' => $licenseKey,
        ]);

        return $response->successful() && $response->json()['valid'] === true;
    }
}
