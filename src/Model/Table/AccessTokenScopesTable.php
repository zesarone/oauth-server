<?php
namespace OAuth\Model\Table;

use Cake\ORM\Table;

class AccessTokenScopesTable extends Table {

    public function initialize(array $config)
    {
        $this->table('oauth_access_token_scopes');
        $this->belongsTo('AccessTokens', [
            'className' => 'OAuth.AccessTokens',
            'foreignKey' => 'oauth_token'
        ]);
        $this->belongsTo('Scopes', [
            'className' => 'OAuth.Scopes'
        ]);
        parent::initialize($config);
    }

}