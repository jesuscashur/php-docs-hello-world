<?php
// Recuperar variables de entorno de Azure
$dbHost = getenv('DB_HOST');
$dbName = "prueba";         
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASSWORD');

// Validación de la existencia de las variables
if (!$dbHost || !$dbUser || $dbPass === false) {
    throw new \RuntimeException('Faltan variables de entorno para la conexión a la base de datos.');
}

// Configuración del DSN con codificación utf8mb4
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    $options = [
        // Activación de excepciones para el control de errores
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        // Configuración del modo de obtención de datos como array asociativo
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Desactivar la emulación de sentencias preparadas
        PDO::ATTR_EMULATE_PREPARES   => false,

        // Configuración de los atributos SSL para la conexión segura con Azure
        PDO::MYSQL_ATTR_SSL_CA        => '/etc/ssl/certs/BaltimoreCyberTrustRoot.crt.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    // Inicialización de la conexión PDO
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

    // Consulta de prueba para verificar la comunicación activa
    $stmt = $pdo->query('SELECT NOW() AS fecha_actual;');
    $fila = $stmt->fetch();
    
    echo "Conectado correctamente. Hora del servidor: " . $fila['fecha_actual'];

} catch (PDOException $e) {
    // Registro y muestra de errores en caso de fallo en la conexión
    error_log('Error de conexión PDO: ' . $e->getMessage());
    echo "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
    exit;
}
