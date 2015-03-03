<?php
namespace OAuthServer\Model\Table;

use Cake\ORM\Table;

class SessionScopesTable extends Table {

    public function initialize(array $config)
    {
        $this->table('oauth_session_scopes');
        $this->belongsTo(
            'Sessions',
            [
                'className' => 'OAuthServer.Sessions',
            ]
        );
        $this->belongsTo(
            'Scopes',
            [
                'className' => 'OAuthServer.Scopes'
            ]
        );
        parent::initialize($config);
    }

}