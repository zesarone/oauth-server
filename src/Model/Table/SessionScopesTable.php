<?php
namespace OAuth\Model\Table;

use Cake\ORM\Table;

class SessionScopesTable extends Table {

    public function initialize(array $config)
    {
        $this->table('oauth_session_scopes');
        $this->belongsTo(
            'Sessions',
            [
                'className' => 'OAuth.Sessions',
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