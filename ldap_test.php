<?php

// Configuración del servidor
$config = [
    'hosts' => ['10.128.225.9'],
    'port' => 389,
    'base_dn' => 'DC=tuteurgroup,DC=com',
    'timeout' => 5,
    'use_ssl' => false,
    'use_tls' => false,
    'username' => 'ysulroca@tuteurgroup.com', // Usar el formato UPN
    'password' => '12345',
    'options' => [
        LDAP_OPT_PROTOCOL_VERSION => 3,
        LDAP_OPT_REFERRALS => 0,
        LDAP_OPT_NETWORK_TIMEOUT => 5,
    ]
];

// Configuración SSL
putenv('LDAPTLS_REQCERT=never');

// Diferentes formatos de usuario para probar
$user_formats = [
    $config['username'], // Formato UPN
    "TUTEURGROUP\\ysulroca", // Formato NetBIOS
    "CN=ysulroca,CN=Users,{$config['base_dn']}", // Formato DN
    "ysulroca" // sAMAccountName
];

echo "Intentando conectar a LDAP...\n";

try {
    // Intentar conexión
    $ldap = ldap_connect($config['hosts'][0]);
    if (!$ldap) {
        throw new Exception("No se pudo crear la conexión LDAP");
    }
    
    // Configurar opciones LDAP
    foreach ($config['options'] as $option => $value) {
        ldap_set_option($ldap, $option, $value);
    }
    
    // Intentar bind inicial con las credenciales configuradas
    $bind = @ldap_bind($ldap, $config['username'], $config['password']);
    if ($bind) {
        echo "Bind inicial exitoso con {$config['username']}\n";
    } else {
        echo "Fallo en bind inicial: " . ldap_error($ldap) . "\n";
    }
    
    echo "Conexión LDAP creada\n";
    
    // Configurar opciones LDAP
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 10);
    
    echo "Intentando bind con diferentes formatos de usuario...\n";
    
    $success = false;
    foreach ($user_formats as $ldap_user) {
        echo "\nProbando con usuario: {$ldap_user}\n";
        try {
            // Intentar bind
            $bind = @ldap_bind($ldap, $ldap_user, $config['password']);
            if ($bind) {
                echo "¡Bind exitoso con formato: {$ldap_user}!\n";
                $success = true;
                break;
            }
        } catch (Exception $e) {
            echo "Fallo: " . $e->getMessage() . "\n";
            continue;
        }
    }
    
    if (!$success) {
        throw new Exception("Fallo en todos los intentos de bind");
    }
    
    echo "Bind exitoso!\n";
    
    // Buscar usuario específico
    $filter = "(samaccountname=testsso)";
    $search = ldap_search($ldap, $ldap_base_dn, $filter);
    if (!$search) {
        throw new Exception("Error en la búsqueda: " . ldap_error($ldap));
    }
    
    $entries = ldap_get_entries($ldap, $search);
    echo "Resultados encontrados: " . $entries["count"] . "\n";
    
    if ($entries["count"] > 0) {
        echo "Usuario encontrado!\n";
        echo "DN: " . $entries[0]["dn"] . "\n";
    } else {
        echo "Usuario no encontrado\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($ldap) && $ldap) {
        ldap_close($ldap);
    }
}
