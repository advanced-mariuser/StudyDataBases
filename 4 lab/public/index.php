<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;
use App\Controller\BeautySalonCrmController;
use App\Controller\ClientsController;
use App\Controller\MastersController;
use App\Controller\AppointmentsController;

require __DIR__ . '/../vendor/autoload.php';

$isProduction = getenv('APP_ENV') === 'prod';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(!$isProduction, true, true);

$app->get('/', BeautySalonCrmController::class . ':index');

//Маршруты master
$app->get('/masters', MastersController::class . ':listMasters');
$app->get('/master/edit', MastersController::class . ':editMaster');
$app->get('/master/new', MastersController::class . ':newMaster');
$app->post('/master/create', MastersController::class . ':createMaster');
$app->post('/master/delete', MastersController::class . ':deleteMaster');
$app->post('/master/update', MastersController::class . ':updateMaster');

//Маршруты client
$app->get('/clients', ClientsController::class . ':listClients');
$app->get('/client/edit', ClientsController::class . ':editClient');
$app->get('/client/new', ClientsController::class . ':newClient');
$app->post('/client/create', ClientsController::class . ':createClient');
$app->post('/client/update', ClientsController::class . ':updateClient');
$app->post('/client/delete', ClientsController::class . ':deleteClient');

//Маршруты appointment
$app->get('/appointment/edit', AppointmentsController::class . ':editAppointment');
$app->get('/appointment/new', AppointmentsController::class . ':newAppointment');
$app->post('/appointment/create', AppointmentsController::class . ':createAppointment');
$app->post('/appointment/update', AppointmentsController::class . ':updateAppointment');
$app->post('/appointment/delete', AppointmentsController::class . ':deleteAppointment');

$app->run();