<?php
declare(strict_types=1);

// Устанавливаем переменную окружения, сигнализирующу
// приложению, что оно запускается в режиме тестирования.
putenv('APP_ENV=test');

// Подключаем автозагрузку по PSR-4
require_once __DIR__ . '/../vendor/autoload.php';