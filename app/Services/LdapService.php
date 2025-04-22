<?php

namespace App\Services;

use App\Models\LdapUser;
use Exception;
use Illuminate\Support\Facades\Log;

class LdapService
{
    private array $config;
    private array $domains = ['tuteurgroup.com', 'tuteurgroup.com.ar'];
    private array $tokens = [];

    public function __construct()
    {
        $this->config = [
            'hosts' => ['10.128.225.9'],
            'port' => 389,
            'base_dn' => 'DC=tuteurgroup,DC=com',
            'timeout' => 5,
            'use_ssl' => false,
            'use_tls' => false,
            'options' => [
                LDAP_OPT_PROTOCOL_VERSION => 3,
                LDAP_OPT_REFERRALS => 0,
                LDAP_OPT_NETWORK_TIMEOUT => 5,
            ]
        ];
    }

    public function authenticate(string $username, string $password): ?LdapUser
    {
        putenv('LDAPTLS_REQCERT=never');

        foreach ($this->domains as $domain) {
            try {
                $userDn = $username . '@' . $domain;
                Log::info('Attempting authentication', ['username' => $userDn, 'domain' => $domain]);

                // Intentar conexión
                $ldap = ldap_connect($this->config['hosts'][0]);
                if (!$ldap) {
                    throw new Exception("No se pudo crear la conexión LDAP");
                }

                // Configurar opciones LDAP
                foreach ($this->config['options'] as $option => $value) {
                    ldap_set_option($ldap, $option, $value);
                }

                // Intentar bind con las credenciales del usuario
                $bind = @ldap_bind($ldap, $userDn, $password);
                if ($bind) {
                    Log::info('User authenticated successfully', ['domain' => $domain]);
                    
                    // Buscar información adicional del usuario
                    $search = @ldap_search($ldap, $this->config['base_dn'], "(userPrincipalName={$userDn})");
                    if ($search) {
                        $entries = ldap_get_entries($ldap, $search);
                        if ($entries['count'] > 0) {
                            $userInfo = $entries[0];
                            return new LdapUser([
                                'username' => $username,
                                'email' => $userDn,
                                'domain' => $domain,
                                'name' => $userInfo['cn'][0] ?? null,
                                'displayName' => $userInfo['displayname'][0] ?? null
                            ]);
                        }
                    }
                    
                    return new LdapUser([
                        'username' => $username,
                        'email' => $userDn,
                        'domain' => $domain
                    ]);
                }

                Log::warning('Bind failed', ['error' => ldap_error($ldap)]);
            } catch (Exception $e) {
                Log::warning('Authentication failed for domain', [
                    'domain' => $domain,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return null;
    }

    private function connect()
    {
        $ldap = ldap_connect($this->config['hosts'][0], $this->config['port']);
        if (!$ldap) {
            throw new Exception("Could not create LDAP connection");
        }

        foreach ($this->config['options'] as $option => $value) {
            ldap_set_option($ldap, $option, $value);
        }

        // Intentar bind inicial
        $bind = @ldap_bind($ldap);
        if (!$bind) {
            throw new Exception("Initial bind failed: " . ldap_error($ldap));
        }

        return $ldap;
    }

    private function bindUser($ldap, string $userDn, string $password): bool
    {
        $bind = @ldap_bind($ldap, $userDn, $password);
        if (!$bind) {
            Log::warning('Bind failed', ['error' => ldap_error($ldap)]);
            return false;
        }
        return true;
    }

    private function getUserInfo($ldap, string $userDn): array
    {
        $username = explode('@', $userDn)[0];
        $userInfo = [
            'username' => $username,
            'email' => $userDn,
            'name' => null,
            'displayName' => null
        ];

        $search = @ldap_search($ldap, $this->config['base_dn'], "(userPrincipalName={$userDn})");
        if ($search) {
            $entries = ldap_get_entries($ldap, $search);
            if ($entries['count'] > 0) {
                $entry = $entries[0];
                $userInfo['name'] = $entry['cn'][0] ?? null;
                $userInfo['displayName'] = $entry['displayname'][0] ?? null;
            }
        }

        return $userInfo;
    }
}
