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
        $this->loadModel('OAuth.AuthCodes');
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

    public function create($token, $expireTime, $sessionId, $redirectUri)
    {
        $this->loadModel('OAuth.AuthCodes');
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
        $this->loadModel('OAuth.AuthCodeScopes');
        $result = $this->AuthCodeScopes->find()
            ->contain([
                'Scopes'
            ])
            ->where([
                'auth_code' => $token->getId()
            ])
            ->map(
                function (Entity $scope) {
                    return (new ScopeEntity($this->server))->hydrate(
                        [
                            'id' => $scope->scope->id,
                            'description' => $scope->scope->description,
                        ]
                    );
                }
            );

        return $result->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function associateScope(AuthCodeEntity $token, ScopeEntity $scope)
    {
        $this->loadModel('OAuth.AuthCodeScopes');
        $code_scope = $this->AuthCodeScopes->newEntity([
            'code' => $token->getId(),
            'scope_id' => $scope->getId(),
        ]);
        $this->CodeScopes->save($code_scope);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AuthCodeEntity $token)
    {
        $this->loadModel('OAuth.AuthCodes');
        $this->AuthCodes
            ->deleteAll([
                'code' => $token->getId()
            ]);
    }
}
