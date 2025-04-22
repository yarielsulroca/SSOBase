<?php

echo "=== Información del Sistema ===\n";
echo "Usuario actual: " . get_current_user() . "\n";
echo "Nombre del equipo: " . gethostname() . "\n\n";

echo "=== Variables de entorno ===\n";
$vars = [
    'LOGONSERVER',
    'USERDOMAIN',
    'USERDNSDOMAIN',
    'USERNAME',
    'USERPROFILE',
    'COMPUTERNAME'
];

foreach ($vars as $var) {
    echo $var . ": " . getenv($var) . "\n";
}

echo "\n=== Intentando obtener información del dominio ===\n";
exec('wmic computersystem get domain', $domain);
echo "Dominio (wmic): " . (isset($domain[1]) ? trim($domain[1]) : "No disponible") . "\n";

echo "\n=== Servidores DNS configurados ===\n";
exec('ipconfig /all | findstr "DNS Servers"', $dns);
foreach ($dns as $line) {
    echo trim($line) . "\n";
}

echo "\n=== Rutas activas ===\n";
exec('route print -4 | findstr "10.128"', $routes);
foreach ($routes as $route) {
    echo trim($route) . "\n";
}
