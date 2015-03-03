<?php

namespace OAuth\Model\Storage;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AccessTokenInterface;

class AccessTokenStorage extends AbstractStorage implements AccessTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($token)
    {
        $this->loadModel('OAuth.AccessTokens');
        $result = $this->AccessTokens->find()
            ->select([
                'oauth_token',
                'expires'
            ])
            ->where([
                'oauth_token' => $token
            ])
            ->first();

        if ($result) {
            return (new AccessTokenEntity($this->server))
                        ->setId($result->oauth_token)
                        ->setExpireTime($result->expires);
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(AccessTokenEntity $token)
    {
        $this->loadModel('OAuth.AccessTokenScopes');
        $result = $this->AccessTokenScopes->find()
            ->contain([
                'Scopes'
            ])
            ->where([
                'oauth_token' => $token->getId()
            ])
            ->map(function (Entity $scope) {
                return (new ScopeEntity($this->server))->hydrate(
                    [
                        'id' => $scope->scope->id,
                        'description' => $scope->scope->description,
                    ]
                );
            });

        return $result->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function create($token, $expireTime, $sessionId)
    {
        $this->loadModel('OAuth.AccessTokens');
        $token = $this->AccessTokens->newEntity([
            'oauth_token' => $token,
            'session_id' => $sessionId,
            'expires' => $expireTime,
        ]);
        $this->AccessTokens->save($token);
    }

    /**
     * {@inheritdoc}
     */
    public function associateScope(AccessTokenEntity $token, ScopeEntity $scope)
    {
        $this->loadModel('OAuth.AccessTokenScopes');
        $token_scope = $this->AccessTokenScopes->newEntity(
            [
                'oauth_token' => $token->getId(),
                'scope_id' => $scope->getId(),
            ]
        );
        $this->AccessTokenScopes->save($token_scope);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AccessTokenEntity $token)
    {
        $this->loadModel('OAuth.AccessTokens');
        $access_token = $this->AccessTokens->findByOauthToken($token->getId())->first();
        $this->AccessTokens
            ->delete($access_token, ['cascade' => true]);
    }
}
