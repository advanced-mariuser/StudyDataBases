<?php
declare(strict_types=1);

use App\Controller\CrmAppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = CrmAppFactory::createApp();
$app->run();