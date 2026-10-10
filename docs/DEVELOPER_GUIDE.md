# TomvisHIMPro Developer Guide

## Developing a New Clinical Module

To generate a new clinical module (e.g. `PharmacyQueue`), use the Tomvis CLI tool:

```bash
php scripts/tomvis make module PharmacyQueue
```

This will automatically create:
- Module directory: `app/Modules/PharmacyQueue`
- Controller: `PharmacyQueueController.php`
- Base response handler & route bindings.

## Registering Routes
Add web routes in `routes/web.php` or API endpoints in `routes/api.php`:

```php
$router->get('/pharmacy/queue', [PharmacyQueueController::class, 'index']);
```

## Accessing HIS Data
Always use Repository Interfaces:

```php
use Tomvis\HIMPro\Contracts\HIS\PatientRepositoryInterface;

class PharmacyQueueController extends BaseController {
    public function __construct(protected PatientRepositoryInterface $patientRepo) {}
}
```
