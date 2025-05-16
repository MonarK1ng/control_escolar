<?php
// Script de configuración inicial (se ejecutará solo una vez)
header('Content-Type: text/plain');

echo "Configurando base de datos...\n\n";

// 1. Conexión como administrador (usa variables de entorno de Azure)
$host = getenv('DB_HOST') ?: "dbmysql-paas.mysql.database.azure.com";
$adminUser = getenv('DB_ADMIN_USER') ?: "azureadmin";
$adminPass = getenv('DB_ADMIN_PASS') ?: "PaaSSec456";
$dbName = "control_escolar";
$appUser = "appuser";
$appPass = "AppUser!23";

$sslOptions = [
    PDO::MYSQL_ATTR_SSL_CA => '/home/site/wwwroot/DigiCertGlobalRootCA.crt.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true
];

try {
    // 2. Crear usuario y base de datos
    $adminConn = new PDO("mysql:host=$host", $adminUser, $adminPass, $sslOptions);
    $adminConn->exec("CREATE DATABASE IF NOT EXISTS $dbName");
    $adminConn->exec("CREATE USER IF NOT EXISTS '$appUser'@'%' IDENTIFIED BY '$appPass'");
    $adminConn->exec("GRANT ALL PRIVILEGES ON $dbName.* TO '$appUser'@'%'");
    $adminConn->exec("FLUSH PRIVILEGES");

    // 3. Crear tabla
    $appConn = new PDO("mysql:host=$host;dbname=$dbName", $appUser, $appPass, $sslOptions);
    $appConn->exec("
        CREATE TABLE IF NOT EXISTS alumnos (
            Num_Control INT(8) PRIMARY KEY,
            Correo VARCHAR(320) NOT NULL,
            Semestre INT(2) NOT NULL,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    echo "✅ Configuración completada.\n";
    echo "Base de datos: $dbName\n";
    echo "Usuario: $appUser\n\n";
    echo "Ahora puedes reemplazar este archivo con tu aplicación real.\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
