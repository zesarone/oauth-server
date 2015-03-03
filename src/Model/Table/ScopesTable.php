<?php
namespace OAuth\Model\Table;

use Cake\ORM\Table;

class ScopesTable extends Table {

    public function initialize(array $config)
    {
        $this->table('oauth_scopes');
        $this->hasMany('AccessTokenScopes', [
            'className' => 'OAuth.AccessTokenScopes'
        ]);
        $this->hasMany(
            'AuthCodeScopes',
            [
                'className' => 'OAuth.AuthCodeScopes'
            ]
        );
        $this->hasMany(
            'SessionScopes',
            [
                'className' => 'OAuth.SessionScopes'
            ]
        );
        parent::initialize($config);
    }

}