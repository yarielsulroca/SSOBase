<?php

namespace App\Services;

use LdapRecord\Container;
use LdapRecord\Connection;
use LdapRecord\Models\ActiveDirectory\User;
use Illuminate\Support\Facades\Log;

class LdapService
{
    protected $connection;
    protected $allowedDomains = ['tuteurgroup.com', 'RWTUTEUR.com'];

    public function __construct()
    {
        $this->connection = new Connection([
            'hosts'    => [config('ldap.connections.default.hosts.0')],
            'port'     => config('ldap.connections.default.port'),
            'base_dn'  => config('ldap.connections.default.base_dn'),
            'use_ssl'  => config('ldap.connections.default.use_ssl'),
            'use_tls'  => config('ldap.connections.default.use_tls'),
            'version'  => config('ldap.connections.default.version'),
            'timeout'  => config('ldap.connections.default.timeout'),
        ]);

        Container::addConnection($this->connection, 'default');
    }

    public function authenticate(string $username, string $password): ?array
    {
        try {
            Log::info('Attempting authentication', ['username' => $username]);

            // Extraer el dominio del nombre de usuario
            $domain = $this->extractDomain($username);
            Log::info('Extracted domain', ['domain' => $domain]);

            if (!$this->isDomainAllowed($domain)) {
                Log::warning('Domain not allowed', ['domain' => $domain]);
                return null;
            }

            // Intentar autenticar con el usuario
            $this->connection->connect();
            $this->connection->auth()->attempt($username, $password);

            // Si llegamos aquí, la autenticación fue exitosa
            // Extraer el nombre de usuario sin el dominio
            $usernameWithoutDomain = explode('@', $username)[0];

            // Crear un objeto de usuario con información mínima
            $userData = [
                'username' => $usernameWithoutDomain,
                'email' => $username,
                'name' => $usernameWithoutDomain,
                'displayName' => $usernameWithoutDomain,
                'groups' => []
            ];

            Log::info('User authenticated successfully', [
                'username' => $userData['username'],
                'domain' => $domain
            ]);

            return $userData;

        } catch (\Exception $e) {
            Log::error('LDAP authentication error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function extractDomain(string $username): string
    {
        if (strpos($username, '@') !== false) {
            return explode('@', $username)[1];
        }
        return '';
    }

    protected function isDomainAllowed(string $domain): bool
    {
        return in_array(strtolower($domain), array_map('strtolower', $this->allowedDomains));
    }
}
