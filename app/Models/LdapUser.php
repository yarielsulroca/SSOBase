<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use JsonSerializable;

class LdapUser implements AuthenticatableContract, JsonSerializable
{
    private const CACHE_KEY = 'ldap_users';

    protected $fillable = [
        'username',
        'email',
        'domain',
        'name',
        'displayName',
        'api_token'
    ];

    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getAuthPassword()
    {
        return ''; // No almacenamos contraseñas
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // No implementado
    }

    public function getRememberTokenName()
    {
        return null;
    }

    public function jsonSerialize(): mixed
    {
        return $this->attributes;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function save(): void
    {
        if (isset($this->attributes['api_token'])) {
            $storage = Cache::get(self::CACHE_KEY, ['tokens' => [], 'users' => []]);
            $storage['tokens'][$this->attributes['api_token']] = $this;
            $storage['users'][$this->attributes['username']] = $this;
            Cache::put(self::CACHE_KEY, $storage);
        }
    }

    public static function findByToken(string $token): ?self
    {
        $storage = Cache::get(self::CACHE_KEY, ['tokens' => [], 'users' => []]);
        return $storage['tokens'][$token] ?? null;
    }
}
