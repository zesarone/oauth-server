<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

/**
 * AccessToken Model
 *
 * @property Client $Client
 * @property User $User
 */
class AccessTokensTable extends Table
{

    public function initialize(array $config)
    {
        $this->table('oauth_access_tokens');
        $this->primaryKey('oauth_token');
        $this->belongsTo('Sessions', [
            'className' => 'OAuthServer.Sessions',
        ]);
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuthServer.AccessTokenScopes',
            'foreignKey' => 'oauth_token',
            'dependant' => true
        ]);
        parent::initialize($config);
    }
}
