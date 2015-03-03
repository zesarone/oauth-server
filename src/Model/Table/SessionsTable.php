<?php
namespace OAuth\Model\Table;

use Cake\ORM\Table;

class SessionsTable extends Table {

    public function initialize(array $config)
    {
        $this->table('oauth_sessions');
        $this->hasMany(
            'SessionScopes',
            [
                'className' => 'OAuth.SessionScopes',
                'dependant' => true
            ]
        );
        $this->hasMany('AuthCodes', [
            'className' => 'OAuth.AuthCodes',
            'dependant' => true
        ]);
        $this->hasMany(
            'AccessTokens',
            [
                'className' => 'OAuth.AccessTokens',
                'dependant' => true
            ]
        );
        $this->hasMany(
            'RefreshTokens',
            [
                'className' => 'OAuth.RefreshTokens',
                'dependant' => true
            ]
        );
        parent::initialize($config);
    }

}