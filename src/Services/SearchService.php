<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:16:39 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by:   Arvin.Loripour 
 * @Last Modified time: 2024-12-05 11:16:39 
 */

namespace ViraNet\TemplatePluginManager\Services;

use Illuminate\Support\Facades\Http;

class SearchService
{
    public static function searchTemplates($query)
    {
        $response = Http::get(config('template-plugin-manager.search_api_url').'/templates', [
            'query' => $query,
        ]);

        return $response->json()['data'] ?? [];
    }

    public static function searchPlugins($query)
    {
        $response = Http::get(config('template-plugin-manager.search_api_url').'/plugins', [
            'query' => $query,
        ]);

        return $response->json()['data'] ?? [];
    }
}
