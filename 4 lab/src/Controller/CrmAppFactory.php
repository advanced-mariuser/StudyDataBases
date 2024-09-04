<?php
declare(strict_types=1);

namespace App\Controller;

use Slim\App;
use Slim\Factory\AppFactory;
use App\Controller\BeautySalonCrmController;
use App\Controller\ClientsController;
use App\Controller\MastersController;
use App\Controller\AppointmentsController;

class CrmAppFactory
{
    public static function createApp(): App
    {
        $isProduction = getenv('APP_ENV') === 'prod';

        $app = AppFactory::create();

        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(!$isProduction, true, true);

        $app->get('/', BeautySalonCrmController::class . ':index');

        //Маршруты master
        $app->get('/masters', MastersController::class . ':listMasters');
        $app->get('/master/edit', MastersController::class . ':editMasterForm');
        $app->get('/master/new', MastersController::class . ':newMasterForm');
        $app->post('/master/create', MastersController::class . ':createMaster');
        $app->post('/master/delete', MastersController::class . ':deleteMaster');
        $app->post('/master/update', MastersController::class . ':updateMaster');

        //Маршруты client
        $app->get('/clients', ClientsController::class . ':listClients');
        $app->get('/client/edit', ClientsController::class . ':editClientForm');
        $app->get('/client/new', ClientsController::class . ':newClientForm');
        $app->post('/client/create', ClientsController::class . ':createClientForm');
        $app->post('/client/update', ClientsController::class . ':updateClient');

        //Маршруты appointment
        $app->get('/appointment/edit', AppointmentsController::class . ':editAppointmentForm');
        $app->get('/appointment/new', AppointmentsController::class . ':newAppointmentForm');
        $app->post('/appointment/create', AppointmentsController::class . ':createAppointment');
        $app->post('/appointment/update', AppointmentsController::class . ':updateAppointment');
        $app->post('/appointment/delete', AppointmentsController::class . ':deleteAppointment');

        return $app;
    }
}