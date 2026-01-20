<?php

$lockFile = __DIR__ . '/installed.lock';

if (file_exists($lockFile)) {
    die('Already installed.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request');
}

$rootPath = dirname(__DIR__, 2);
$envPath  = $rootPath . '/.env';

$dbHost = trim($_POST['db_host']);
$dbName = trim($_POST['db_name']);
$dbUser = trim($_POST['db_user']);
$dbPass = trim($_POST['db_pass']);

$env = <<<ENV
APP_NAME=Stocky
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=

DB_CONNECTION=mysql
DB_HOST=$dbHost
DB_PORT=3306
DB_DATABASE=$dbName
DB_USERNAME=$dbUser
DB_PASSWORD=$dbPass
ENV;

// Create .env
file_put_contents($envPath, $env);

// Test DB connection
try {
    new PDO(
        "mysql:host=$dbHost;dbname=$dbName",
        $dbUser,
        $dbPass
    );
} catch (Exception $e) {
    unlink($envPath);
    die('Database connection failed: ' . $e->getMessage());
}

// Run Laravel commands
exec("php $rootPath/artisan key:generate");
exec("php $rootPath/artisan migrate --force");

// Lock installer
file_put_contents($lockFile, 'installed');

echo "Installation successful. You can now login.";
