<?php
namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

class SessionsTable extends Table {

    public function initialize(array $config)
    {
        $this->table('oauth_sessions');
        $this->hasMany(
            'SessionScopes',
            [
                'className' => 'OAuthServer.SessionScopes',
                'dependant' => true
            ]
        );
        $this->hasMany('AuthCodes', [
            'className' => 'OAuthServer.AuthCodes',
            'dependant' => true
        ]);
        $this->hasMany(
            'AccessTokens',
            [
                'className' => 'OAuthServer.AccessTokens',
                'dependant' => true
            ]
        );
        $this->hasMany(
            'RefreshTokens',
            [
                'className' => 'OAuthServer.RefreshTokens',
                'dependant' => true
            ]
        );
        parent::initialize($config);
    }

}