<?php

namespace OAuthServer\Model\Storage;

use League\OAuth2\Server\Entity\RefreshTokenEntity;
use League\OAuth2\Server\Storage\RefreshTokenInterface;

class RefreshTokenStorage extends AbstractStorage implements RefreshTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($token)
    {
        $this->loadModel('OAuth.RefreshTokens');
        $result = $this->RefreshTokens
            ->find()
            ->where([
                'refresh_token' => $token
            ])
            ->first();

        if ($result) {
            $token = (new RefreshTokenEntity($this->server))
                        ->setId($result->refresh_token)
                        ->setExpireTime($result->expires)
                        ->setAccessTokenId($result->access_token);

            return $token;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function create($token, $expireTime, $accessToken)
    {
        $this->loadModel('OAuth.RefreshTokens');
        $refresh_token = $this->RefreshTokens->newEntity([
            'refresh_token' => $token,
            'oauth_token' => $accessToken,
            'expires' => $expireTime,
        ]);
        $this->RefreshTokens->save($refresh_token);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(RefreshTokenEntity $token)
    {
        $this->loadModel('OAuth.RefreshTokens');
        $this->RefreshTokens->deleteAll([
            'refresh_token' => $token->getId()
        ]);
    }
}
