<?php

namespace Tests\Feature\Api;


use App\Models\User;
use App\Services\AuthService\Token;

trait AuthTrait
{
    /**
     * @var \App\Models\User
     */
    protected $user;

    protected function setUser($type = User::TYPE_CLIENT)
    {
        $this->user = factory(User::class)->create([
            'type' => $type,
            'settings'=>(new \App\Services\UserService\Settings())->getDefaultSettings($type)
        ]);
        app(Token::class)->createTokens($this->user);
    }

    protected function getAuthHeader()
    {
        return ['Authorization' => $this->user->token->access_token];
    }
}