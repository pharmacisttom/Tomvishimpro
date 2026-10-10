<?php

define('TOMVIS_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

use Tomvis\HIMPro\Core\Application\Application;
use Tomvis\HIMPro\Core\Request\Request;

$app = new Application(dirname(__DIR__));
$container = $app->getContainer();

$router = $container->make(\Tomvis\HIMPro\Core\Routing\Router::class);

// Bind Adapters
$container->singleton(\Tomvis\HIMPro\Contracts\HIS\PatientRepositoryInterface::class, \Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter::class);
$container->singleton(\Tomvis\HIMPro\Contracts\HIS\VisitRepositoryInterface::class, \Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter::class);
$container->singleton(\Tomvis\HIMPro\Contracts\HIS\DiagnosisRepositoryInterface::class, \Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter::class);
$container->singleton(\Tomvis\HIMPro\Contracts\HIS\MedicationRepositoryInterface::class, \Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter::class);
$container->singleton(\Tomvis\HIMPro\Contracts\HIS\AllergyRepositoryInterface::class, \Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter::class);
$container->singleton(\Tomvis\HIMPro\Contracts\HIS\LaboratoryRepositoryInterface::class, \Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter::class);
$container->singleton(\Tomvis\HIMPro\Contracts\HIS\AdmissionRepositoryInterface::class, \Tomvis\HIMPro\Infrastructure\HIMPro\MockHISAdapter::class);

// Load Routes
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

$request = Request::capture();
$response = $router->dispatch($request);
$response->send();
