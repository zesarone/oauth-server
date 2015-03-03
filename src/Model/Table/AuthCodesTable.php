<?php

namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

/**
 * AuthCode Model
 *
 * @property Client $Client
 * @property User $User
 */
class AuthCodesTable extends Table
{

    public function initialize(array $config)
    {
        $this->table('oauth_auth_codes');
        $this->primaryKey('code');

        $this->belongsTo(
            'Sessions',
            [
                'className' => 'OAuth.Sessions',
            ]
        );
        $this->hasMany(
            'AuthCodeScopes',
            [
                'className' => 'OAuth.AuthCodeScopes',
                'foreignKey' => 'auth_code',
                'dependant' => true
            ]
        );
        parent::initialize($config);
    }
}
