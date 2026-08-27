<?php
declare(strict_types=1);

// Cargar el bootstrap
$config = require __DIR__ . '/bootstrap/app.php';

// Probar que las variables se cargaron
echo "<h1>✅ Verificación del .env</h1>";
echo "<pre>";
echo "📌 APP_NAME: " . getenv('APP_NAME') . "\n";
echo "📌 DB_HOST: " . getenv('DB_HOST') . "\n";
echo "📌 DB_NAME: " . getenv('DB_NAME') . "\n";
echo "📌 Zona horaria: " . date_default_timezone_get() . "\n";
echo "📌 Config cargada: " . print_r($config, true);
echo "</pre>";