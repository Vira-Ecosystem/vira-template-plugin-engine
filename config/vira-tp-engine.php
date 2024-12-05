<?php
/*
 * @Author: Arvin Loripour - ViraEcosystem 
 * @Date: 2024-12-05 11:12:59 
 * Copyright by Arvin Loripour 
 * WebSite : http://www.arvinlp.ir 
 * @Last Modified by:   Arvin.Loripour 
 * @Last Modified time: 2024-12-05 11:12:59 
 */

return [
    'template_path' => public_path('packages/templates/'),
    'plugin_path' => public_path('packages/plugins/'),
    'license_api_url' => env('LICENSE_API_URL', 'https://license-server.com/api/validate'),
    'search_api_url' => env('SEARCH_API_URL', 'https://search-server.com/api/search'),
];
