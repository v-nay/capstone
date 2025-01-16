<?php

namespace App\OAuth;

use Config;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Laravel\Passport\Bridge\User;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use RuntimeException;

class PassportUserRepository implements UserRepositoryInterface
{
    /**
     * The hasher implementation.
     *
     * @var Hasher
     */
    protected $hasher;

    /**
     * Create a new repository instance.
     *
     * @param  Hasher  $hasher
     * @return void
     */
    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @inheritdoc
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $guard = 'frontendUsers'; // obtain current guard name from config
        $provider = Config::get('auth.guards.'.$guard.'.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        if (method_exists($model, 'findAndValidateForPassport')) {
            $user = (new $model)->findAndValidateForPassport($username, $password);

            if (! $user) {
                return;
            }

            return new User($user->getAuthIdentifier());
        }

        if (method_exists($model, 'findForPassport')) {
            $user = (new $model)->findForPassport($username);
        } else {
            $user = (new $model)->where('email', $username)->first();
        }

        if (! $user) {
            return;
        } elseif (method_exists($user, 'validateForPassportPasswordGrant')) {
            if (! $user->validateForPassportPasswordGrant($password)) {
                return;
            }
        } elseif (! $this->hasher->check($password, $user->getAuthPassword())) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }
}
