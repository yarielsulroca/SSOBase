<?php

$ldap_host = "10.128.255.9";
$ldap_port = 389;
$ldap_user = "CN=SSO_ADSync,CN=Users,DC=rwtuteur,DC=com,DC=ar";
$ldap_pass = "Cl4v3-d1f1c1l+";


$ldap_base_dn = "DC=rwtuteur,DC=com,DC=ar";

// Configuración SSL
putenv('LDAPTLS_REQCERT=never');

echo "Intentando conectar a LDAP...\n";

try {
    // Intentar conexión
    $ldap = ldap_connect($ldap_host, $ldap_port);
    if (!$ldap) {
        throw new Exception("No se pudo crear la conexión LDAP");
    }
    
    echo "Conexión LDAP creada\n";
    
    // Configurar opciones LDAP
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 10);
    
    echo "Intentando bind con usuario...\n";
    
    // Intentar bind
    $bind = ldap_bind($ldap, $ldap_user, $ldap_pass);
    if (!$bind) {
        throw new Exception("Fallo en el bind: " . ldap_error($ldap));
    }
    
    echo "Bind exitoso!\n";
    
    // Buscar usuario específico
    $filter = "(samaccountname=ysulroca)";
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
