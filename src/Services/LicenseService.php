<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:15:57 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by: Arvin.Loripour
 * @Last Modified time: 2024-12-05 11:46:17
 */
namespace ViraNet\TemplatePluginManager\Services;

use Illuminate\Support\Facades\Http;

class LicenseService
{
    /**
     * Check if the license is valid via an external API.
     *
     * @param string $licenseKey
     * @return bool
     */
    public function validateLicense($licenseKey)
    {
        $licenseApiUrl = config('viranet-tp-engine.license_api_url');
        $response = Http::post($licenseApiUrl, [
            'license_key' => $licenseKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return $data['status'] === 'valid';  // Check if the license is valid
        }

        return false;
    }

    /**
     * Get the license details from the external API.
     *
     * @param string $licenseKey
     * @return array|null
     */
    public function getLicenseDetails($licenseKey)
    {
        $licenseApiUrl = config('viranet-tp-engine.license_api_url');
        $response = Http::post($licenseApiUrl, [
            'license_key' => $licenseKey,
        ]);

        if ($response->successful()) {
            return $response->json();  // Return license details if valid
        }

        return null;
    }
}
