<?php
$host = "dbmysql-paas.mysql.database.azure.com";
$adminUser = "azureadmin";
$adminPass = "PaaSSec456!";
$dbName = "control_escolar";

try {
    // Conexión como administrador
    $adminConn = new PDO(
        "mysql:host=$host;dbname=mysql", 
        $adminUser, 
        $adminPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => '/path/to/DigiCertGlobalRootCA.crt.pem',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true
        ]
    );

    // 1. Crear la base de datos (si no existe)
    $adminConn->exec("CREATE DATABASE IF NOT EXISTS $dbName");

    // 2. Crear usuario y asignar privilegios
    $adminConn->exec("CREATE USER IF NOT EXISTS 'appuser'@'%' IDENTIFIED BY 'AppUserMy123!'");
    $adminConn->exec("GRANT ALL PRIVILEGES ON $dbName.* TO 'appuser'@'%'");
    $adminConn->exec("FLUSH PRIVILEGES");

    // 3. Crear tabla (usando la nueva conexión con la base de datos creada)
    $dbConn = new PDO(
        "mysql:host=$host;dbname=$dbName", 
        $adminUser, 
        $adminPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => '/path/to/DigiCertGlobalRootCA.crt.pem'
        ]
    );
    
    $dbConn->exec("
        CREATE TABLE IF NOT EXISTS alumnos (
            Num_Control INT(8) PRIMARY KEY,
            Correo VARCHAR(320) NOT NULL,
            Semestre INT(2) NOT NULL,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    echo "¡Base de datos, usuario y tabla configurados correctamente!";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
