<?php

namespace OAuthServer\Model\Storage;

use League\OAuth2\Server\Entity\ScopeEntity;
use League\OAuth2\Server\Storage\ScopeInterface;

class ScopeStorage extends AbstractStorage implements ScopeInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($scope, $grantType = null, $clientId = null)
    {
        $this->loadModel('OAuthServer.Scopes');
        $result = $this->Scopes->find()
            ->where([
                'id' => $scope
            ])
            ->first();

        if ($result) {
            return (new ScopeEntity($this->server))->hydrate(
                [
                    'id' => $result->id,
                    'description' => $result->description,
                ]
            );
        }

        return;
    }
}
