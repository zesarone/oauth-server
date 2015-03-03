<?php
namespace OAuth\Model\Table;

use Cake\ORM\Table;

class AuthCodeScopesTable extends Table {

    public function initialize(array $config)
    {
        $this->table('oauth_auth_code_scopes');
        $this->belongsTo(
            'AuthCodes',
            [
                'className' => 'OAuth.AuthCodes',
                'foreignKey' => 'auth_code'
            ]
        );
        $this->belongsTo(
            'Scopes',
            [
                'className' => 'OAuth.Scopes'
            ]
        );
        parent::initialize($config);
    }

}