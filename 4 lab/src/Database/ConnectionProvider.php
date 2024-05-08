<?php
declare(strict_types=1);

namespace App\Database;

class ConnectionProvider
{
    const string CONFIG_DIR_NAME = 'config';
    const string DATABASE_CONFIG_NAME = 'crm.db.ini';

    public static function connectDatabase(): \PDO
    {
        $connectionProvider = new self();
        $configPath = $connectionProvider->getConfigPath(self::DATABASE_CONFIG_NAME);
        if (!file_exists($configPath)) {
            throw new \RuntimeException("Could not find database configuration at '$configPath'");
        }
        $config = parse_ini_file($configPath);
        if (!$config) {
            throw new \RuntimeException("Failed to parse database configuration from '$configPath'");
        }

        // Проверяем наличие всех ключей конфигурации.
        $expectedKeys = ['dsn', 'user', 'password'];
        $missingKeys = array_diff($expectedKeys, array_keys($config));
        if ($missingKeys) {
            throw new \RuntimeException('Wrong database configuration: missing options ' . implode(' ', $missingKeys));
        }

        return new \PDO($config['dsn'], $config['user'], $config['password']);
    }

    /**
     * Соединяет компоненты пути в один путь, используя разделитель '/' для Unix или '\' для Windows
     */
    private function joinPath(string ...$components): string
    {
        return implode(DIRECTORY_SEPARATOR, array_filter($components));
    }

    /**
     * Возвращает путь к файлу конфигурации
     */
    private function getConfigPath(string $configFileName): string
    {
        return $this->joinPath($this->getProjectRootPath(), self::CONFIG_DIR_NAME, $configFileName);

    }

    /**
     * Возвращает путь к каталогу проекта
     */
    private function getProjectRootPath(): string
    {
        return dirname(__DIR__, 2);
    }
}