<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use JsonSerializable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class LdapUser implements AuthenticatableContract, JsonSerializable, JWTSubject
{
    public const CACHE_KEY = 'ldap_users';

    protected $fillable = [
        'username',
        'email',
        'domain',
        'name',
        'displayName',
        'givenName',
        'surname',
        'department',
        'title',
        'company',
        'manager',
        'memberOf',
        'employeeID',
        'employeeNumber',
        'employeeType',
        'division',
        'office',
        'telephoneNumber',
        'mobile',
        'pager',
        'street',
        'city',
        'state',
        'postalCode',
        'country',
        'description',
        'whenCreated',
        'whenChanged',
        'lastLogon',
        'accountExpires',
        'userAccountControl',
        'groups',
        'api_token',
        'token_expires_at'
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
        $user = $storage['tokens'][$token] ?? null;

        if ($user && isset($user->token_expires_at) && $user->token_expires_at < time()) {
            // Token expirado, eliminarlo
            unset($storage['tokens'][$token]);
            Cache::put(self::CACHE_KEY, $storage);
            return null;
        }

        return $user;
    }

    public function isTokenExpired(): bool
    {
        return isset($this->token_expires_at) && $this->token_expires_at < time();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->username;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'displayName' => $this->displayName
        ];
    }
}
