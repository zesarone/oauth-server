<?php

namespace OAuthServer\Model\Storage;

use Cake\ORM\Entity;
use League\OAuth2\Server\Entity\AccessTokenEntity;
use League\OAuth2\Server\Entity\AuthCodeEntity;
use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Entity\SessionEntity;
use League\OAuth2\Server\Storage\SessionInterface;

class SessionStorage extends AbstractStorage implements SessionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getByAccessToken(AccessTokenEntity $accessToken)
    {
        $this->loadModel('OAuth.Sessions');
        $result = $this->Sessions
            ->find()
            ->matching(
                'AccessTokens',
                function ($q) use ($accessToken) {
                    return $q
                        ->where(
                            [
                                'oauth_token' => $accessToken->getId()
                            ]
                        );
                }
            )
            ->first();

        if ($result) {
            $session = new SessionEntity($this->server);
            $session->setId($result->id);
            $session->setOwner($result->owner_model, $result->owner_id);

            return $session;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getByAuthCode(AuthCodeEntity $authCode)
    {
        $this->loadModel('OAuth.Sessions');
        $result = $this->Sessions
            ->find()
            ->matching('AuthCodes', function($q) use ($authCode) {
                return $q
                    ->where(
                        [
                            'code' => $authCode->getId()
                        ]
                    );
            })
            ->first();

        if ($result) {
            $session = new SessionEntity($this->server);
            $session->setId($result->id);
            $session->setOwner($result->owner_model, $result->owner_id);

            return $session;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(SessionEntity $session)
    {
        $this->loadModel('OAuth.SessionScopes');
        $result = $this->SessionScopes->find()
            ->contain(
                [
                    'Scopes'
                ]
            )
            ->where(
                [
                    'session_id' => $session->getId()
                ]
            )
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
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null)
    {
        $this->loadModel('OAuth.Sessions');
        $session = $this->Sessions->newEntity(
            [
                'owner_model' => $ownerType,
                'owner_id' => $ownerId,
                'client_id' => $clientId,
            ]
        );
        $this->Sessions->save($session);

        return $session->id;
    }

    /**
     * {@inheritdoc}
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope)
    {
        $this->loadModel('OAuth.SessionScopes');
        $session_scope = $this->SessionScopes->newEntity(
            [
                'session_id' => $session->getId(),
                'scope_id' => $scope->getId(),
            ]
        );
        $this->SessionScopes->save($session_scope);
    }
}
