<?php

namespace OAuthServer\Model\Storage;

use Cake\Log\Log;
use Cake\ORM\Entity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\AuthCodeInterface;

class AuthCodeStorage extends AbstractStorage implements AuthCodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($code)
    {
        $this->loadModel('OAuthServer.AuthCodes');
        $result = $this->AuthCodes->find()
            ->where([
                'code' => $code,
                'expires >=' => time()
            ])
            ->first();

        if ($result) {
            $token = new AuthCodeEntity($this->server);
            $token->setId($result->code);
            $token->setRedirectUri($result->redirect_uri);
            $token->setExpireTime($result->expires);

            return $token;
        }

        return;
    }

    /**
     * {@inheritDoc}
     */
    public function create($token, $expireTime, $sessionId, $redirectUri)
    {
        $this->loadModel('OAuthServer.AuthCodes');
        $code = $this->AuthCodes->newEntity([
            'code' => $token,
            'redirect_uri' => $redirectUri,
            'session_id' => $sessionId,
            'expires' => $expireTime,
        ]);
        $this->AuthCodes->save($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(AuthCodeEntity $token)
    {
        $this->loadModel('OAuthServer.AuthCodeScopes');
        $result = $this->AuthCodeScopes->find()
            ->contain([
                'Scopes'
            ])
            ->where([
                'auth_code' => $token->getId()
            ])
            ->map(function (Entity $scope) {
                return (new ScopeEntity($this->server))->hydrate([
                        'id' => $scope->scope->id,
                        'description' => $scope->scope->description,
                    ]);
            });

        return $result->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        $this->loadModel('OAuthServer.AuthCodeScopes');
        $codeScope = $this->AuthCodeScopes->newEntity([
            'auth_code' => $token->getId(),
            'scope_id' => $scope->getId(),
        ]);
        $this->AuthCodeScopes->save($codeScope);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AuthCodeEntity $token)
    {
        $this->loadModel('OAuthServer.AuthCodes');
        $this->AuthCodes->deleteAll([
                'code' => $token->getId()
            ]);
    }
}
