در اینجا یک فایل `README.md` کامل برای پروژه به همراه دستورالعمل‌های استفاده و یک راه‌حل برای نمایش قالب‌ها و افزونه‌ها در پنل مدیریت آمده است.

---

### **`README.md`**

```markdown
# Template and Plugin Manager for Laravel

A simple and extendable package to manage templates and plugins in Laravel projects. This package allows you to easily install, update, and manage templates and plugins via a dedicated file structure. Templates and plugins can include routes, views, and metadata for easy installation and configuration.

## Features
- Install templates and plugins from a specified directory.
- Load routes dynamically without modifying the main `routes/web.php`.
- Check for updates for templates and plugins.
- Preview templates and plugins with images and metadata.
- Simple and intuitive installation and configuration process.
  
## Installation

### Step 1: Install via Composer
In your Laravel project, run the following command to install the package:

```bash
composer require your-vendor/template-plugin-manager
```

### Step 2: Publish the configuration file (Optional)
If you want to customize the configuration, publish the config file:

```bash
php artisan vendor:publish --provider="YourNamespace\TemplatePluginManager\TemplatePluginManagerServiceProvider" --tag="config"
```

This will publish a configuration file named `template-plugin-manager.php` to your `config` directory.

### Step 3: Add RouteServiceProvider (Optional)
If your package needs to manage routes dynamically, make sure to load the package's routes inside `RouteServiceProvider`:

```php
public function boot()
{
    parent::boot();

    // Load routes for templates and plugins
    \YourNamespace\TemplatePluginManager\Services\RouteManager::loadPluginRoutes('your-plugin-name');
    \YourNamespace\TemplatePluginManager\Services\RouteManager::loadTemplateRoutes('your-template-name');
}
```

### Step 4: Update your `.env` file (Optional)
Add the following to your `.env` to define your license and search API URLs:

```env
LICENSE_API_URL=https://your-license-api-url
SEARCH_API_URL=https://your-search-api-url
```

---

## Usage

### Managing Templates

To install a template, use the following method:

```php
$templateManager = new \YourNamespace\TemplatePluginManager\TemplateManager();
$templateManager->installTemplate('template-name', '/path/to/template');
```

This will install the template at `public/packages/templates/template-name`.

To get the metadata of a template:

```php
$templateMetadata = $templateManager->getTemplateMetadata('template-name');
```

To check for updates for a template:

```php
$templateManager->checkForTemplateUpdates('template-name');
```

To update a template:

```php
$templateManager->updateTemplate('template-name');
```

### Managing Plugins

To install a plugin:

```php
$pluginManager = new \YourNamespace\TemplatePluginManager\PluginManager();
$pluginManager->installPlugin('plugin-name', '/path/to/plugin', 'your-license-key');
```

To get the metadata of a plugin:

```php
$pluginMetadata = $pluginManager->getPluginMetadata('plugin-name');
```

To check for updates for a plugin:

```php
$pluginManager->checkForPluginUpdates('plugin-name');
```

To update a plugin:

```php
$pluginManager->updatePlugin('plugin-name');
```

---

## Admin Panel Integration

To integrate the templates and plugins in the admin panel, you can create a simple interface to list and manage installed templates and plugins. Here is an example:

### Step 1: Create a Controller
Create a controller to handle template and plugin management.

```php
php artisan make:controller TemplatePluginController
```

### Step 2: Controller Code
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use YourNamespace\TemplatePluginManager\TemplateManager;
use YourNamespace\TemplatePluginManager\PluginManager;

class TemplatePluginController extends Controller
{
    protected $templateManager;
    protected $pluginManager;

    public function __construct()
    {
        $this->templateManager = new TemplateManager();
        $this->pluginManager = new PluginManager();
    }

    public function showTemplates()
    {
        // Retrieve all installed templates
        $templates = File::directories(public_path('packages/templates'));
        
        return view('admin.templates.index', compact('templates'));
    }

    public function showPlugins()
    {
        // Retrieve all installed plugins
        $plugins = File::directories(public_path('packages/plugins'));
        
        return view('admin.plugins.index', compact('plugins'));
    }

    // Other methods for managing updates and installations
}
```

### Step 3: Create Views
Create views for displaying templates and plugins. For example, `resources/views/admin/templates/index.blade.php`:

```blade
@extends('layouts.admin')

@section('content')
    <h1>Installed Templates</h1>

    <ul>
        @foreach($templates as $template)
            <li>
                {{ basename($template) }}
                <img src="{{ asset('packages/templates/' . basename($template) . '/preview.png') }}" alt="Preview">
                <!-- Add buttons for actions like update, install, etc. -->
            </li>
        @endforeach
    </ul>
@endsection
```

Create a similar view for plugins: `resources/views/admin/plugins/index.blade.php`:

```blade
@extends('layouts.admin')

@section('content')
    <h1>Installed Plugins</h1>

    <ul>
        @foreach($plugins as $plugin)
            <li>
                {{ basename($plugin) }}
                <img src="{{ asset('packages/plugins/' . basename($plugin) . '/preview.png') }}" alt="Preview">
                <!-- Add buttons for actions like update, install, etc. -->
            </li>
        @endforeach
    </ul>
@endsection
```

### Step 4: Define Routes
In `routes/web.php`, define routes to show templates and plugins.

```php
Route::prefix('admin')->middleware('auth')->group(function() {
    Route::get('templates', [TemplatePluginController::class, 'showTemplates'])->name('admin.templates.index');
    Route::get('plugins', [TemplatePluginController::class, 'showPlugins'])->name('admin.plugins.index');
});
```

---

## Additional Features

### Dynamic Routes
If a template or plugin requires its own routes, they will be loaded automatically via the `RouteManager`. You don’t need to manually add them to the main `routes/web.php`. Simply include routes in the `public/packages/templates/{template-name}/routes/web.php` or `public/packages/plugins/{plugin-name}/routes/web.php` file, and they will be loaded when the template/plugin is installed.

### Template and Plugin Metadata
Templates and plugins should include a `metadata.json` file that contains relevant information such as version, description, and update URL. Here’s an example `metadata.json` file:

```json
{
    "name": "My Template",
    "version": "1.0.0",
    "description": "A beautiful template for your website.",
    "update_url": "https://your-update-api.com/template/my-template",
    "preview_image": "preview.png"
}
```

---

## License

This package is licensed under the MIT License.
```
