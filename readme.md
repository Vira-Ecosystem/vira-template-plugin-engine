# Vira Template & Plugin Manager for Laravel

This project provides a Template and Plugin Manager for Laravel that allows you to easily manage and integrate templates and plugins. With this system, you can install, activate, deactivate, delete, update, and download templates and plugins. It also supports loading custom routes and views for each plugin and template. Additionally, metadata for each template and plugin can be read and displayed, providing detailed information about each item.

## Features

- Install, activate, deactivate, and delete templates and plugins.
- Check for updates and download the latest versions of templates and plugins.
- Metadata support for templates and plugins (preview image, version description, download link).
- Load routes and custom pages for plugins and templates dynamically.
- License validation for plugins.
- Search functionality for templates and plugins via a web service.
- Admin Panel Integration: View metadata, install, activate, deactivate, and delete templates and plugins directly from the admin interface.

## Requirements

- PHP >= 7.4
- Laravel >= 8.0
- Composer

## Installation

1. **Install via Composer**:

   Add the package to your Laravel project using Composer:
   
   ```bash
   composer require composer require viranet/vira-template-plugin-engine
   ```

2. **Publish Configuration**:

   Publish the configuration file to your `config` directory:
   
   ```bash
   php artisan vendor:publish --provider="ViraNet\TemplatePluginManager\ServiceProvider"
   ```

   This will create the `config/viranet-tp-engine.php` configuration file where you can adjust the paths, license API URL, and search API URL.

3. **Set Up Directories**:

   Make sure the `public/vira-tp/templates/` and `public/vira-tp/plugins/` directories exist in your project. If they don't, create them:
   
   ```bash
   mkdir -p public/vira-tp/templates
   mkdir -p public/vira-tp/plugins
   ```

## Configuration

The configuration file (`config/viranet-tp-engine.php`) contains the following settings:

```php
return [
    'template_path' => storage_path('public/vira-tp/templates/'),
    'plugin_path' => storage_path('public/vira-tp/plugins/'),
    'license_api_url' => env('LICENSE_API_URL', 'https://license-server.com/api/validate'),
    'search_api_url' => env('SEARCH_API_URL', 'https://search-server.com/api/search'),
];
```

### .env

Ensure the following environment variables are set in your `.env` file:

```env
LICENSE_API_URL=https://license-server.com/api/validate
SEARCH_API_URL=https://search-server.com/api/search
```

## Admin Panel Integration

### Displaying Metadata in Admin Panel

You can integrate the Template & Plugin Manager into your admin panel by fetching and displaying metadata for templates and plugins. The metadata includes details like the name, description, version, preview image, and download link.

#### Example: Fetching and Displaying Metadata

In your admin panel controller, you can fetch and display metadata as follows:

```php
use YourNamespace\TemplatePluginManager\TemplateManager;
use YourNamespace\TemplatePluginManager\PluginManager;

class AdminController extends Controller
{
    protected $templateManager;
    protected $pluginManager;

    public function __construct(TemplateManager $templateManager, PluginManager $pluginManager)
    {
        $this->templateManager = $templateManager;
        $this->pluginManager = $pluginManager;
    }

    // Show the templates in the admin panel
    public function showTemplates()
    {
        $templates = $this->getAllTemplates();
        return view('admin.templates.index', compact('templates'));
    }

    // Show the plugins in the admin panel
    public function showPlugins()
    {
        $plugins = $this->getAllPlugins();
        return view('admin.plugins.index', compact('plugins'));
    }

    private function getAllTemplates()
    {
        $templates = [];
        $templateDirs = glob(storage_path('public/vira-tp/templates/*'), GLOB_ONLYDIR);

        foreach ($templateDirs as $templateDir) {
            $templateName = basename($templateDir);
            $metadata = $this->templateManager->getTemplateMetadata($templateName);
            if ($metadata) {
                $templates[] = $metadata;
            }
        }

        return $templates;
    }

    private function getAllPlugins()
    {
        $plugins = [];
        $pluginDirs = glob(storage_path('public/vira-tp/plugins/*'), GLOB_ONLYDIR);

        foreach ($pluginDirs as $pluginDir) {
            $pluginName = basename($pluginDir);
            $metadata = $this->pluginManager->getPluginMetadata($pluginName);
            if ($metadata) {
                $plugins[] = $metadata;
            }
        }

        return $plugins;
    }
}
```

In the above code, the `getAllTemplates()` and `getAllPlugins()` methods fetch all templates and plugins, respectively, and read their metadata. You can then pass this data to your views and display it in your admin panel.

#### Example View for Templates (`resources/views/admin/templates/index.blade.php`)

```blade
@extends('layouts.admin')

@section('content')
    <h1>Templates</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Version</th>
                <th>Preview</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
                <tr>
                    <td>{{ $template['name'] }}</td>
                    <td>{{ $template['description'] }}</td>
                    <td>{{ $template['version'] }}</td>
                    <td><img src="{{ $template['preview_image'] }}" alt="{{ $template['name'] }}" width="50"></td>
                    <td>
                        <!-- Add your action buttons here -->
                        <form action="{{ route('admin.templates.activate', $template['name']) }}" method="POST">
                            @csrf
                            <button type="submit">Activate</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
```

This view lists all the templates and their metadata (such as name, description, version, and preview image). You can customize this view to add action buttons like "Activate", "Deactivate", and "Delete" for each template.

### Admin Panel Routes

You can define routes for the admin panel to manage templates and plugins:

```php
// routes/web.php

use App\Http\Controllers\AdminController;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('templates', [AdminController::class, 'showTemplates'])->name('templates.index');
    Route::get('plugins', [AdminController::class, 'showPlugins'])->name('plugins.index');
    Route::post('templates/activate/{template}', [AdminController::class, 'activateTemplate'])->name('templates.activate');
    Route::post('plugins/activate/{plugin}', [AdminController::class, 'activatePlugin'])->name('plugins.activate');
});
```

These routes handle the display and management of templates and plugins in your admin panel.

## Routes

When you install a template or plugin, if it contains a `routes/web.php` file, the system will automatically load the routes for the template/plugin. This allows templates and plugins to define their custom routes without modifying the main `routes/web.php` file of your Laravel project.

#### Example of a Plugin Route File (`routes/web.php`)

```php
// Inside your plugin's routes/web.php

Route::get('/my-plugin', function () {
    return view('my-plugin::home');
});
```

## Metadata Format

Each template and plugin should include a `metadata.json` file in their root directory. This file contains information about the template/plugin such as the name, version, description, preview image, and download URL.

#### Example `metadata.json` for a Plugin

```json
{
    "name": "My Plugin",
    "version": "1.0.0",
    "description": "This is a powerful plugin for Laravel.",
    "preview_image": "https://example.com/plugin-preview.jpg",
    "download_url": "https://example.com/download/my-plugin.zip",
    "latest_version": "1.1.0"
}
```

#### Example `metadata.json` for a Template

```json
{
    "name": "My Template",
    "version": "1.0.0",
    "description": "A beautiful template for your Laravel application.",
    "preview_image": "https://example.com/template-preview.jpg",
    "download_url": "https://example.com/download/my-template.zip",
    "latest_version": "1.1.0"
}
```

## License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.
