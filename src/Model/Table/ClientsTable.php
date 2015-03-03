<?php
namespace OAuthServer\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Table;
use OAuthServer\Model\Entity\Client;

/**
 * Client Model
 *
 * @property AccessToken $AccessToken
 * @property AuthCode $AuthCode
 * @property RefreshToken $RefreshToken
 */
class ClientsTable extends Table
{

    public function initialize(array $config)
    {
        $this->table('oauth_clients');
        $this->primaryKey('client_id');
        $this->addBehavior(
            'OAuthServer.HashedField',
            [
                'fields' => [
                    'client_secret'
                ],
            ]
        );
        $this->hasMany('Sessions', [
            'className' => 'OAuthServer.Sessions'
        ]);
        parent::initialize($config);
    }

    public function beforeSave(Event $event, Client $client)
    {
        if ($client->isNew()) {
            $client->client_id = base64_encode(uniqid() . substr(uniqid(), 11, 2));// e.g. NGYcZDRjODcxYzFkY2Rk (seems popular format)
            $client->generateSecret();
        }
    }
    
    public function updateRedirectUrl($client_id = null, $redirect_url = null)
    {
        $this->data['Client'] = [];
        
        $this->id = $client_id;
        $this->data['Client']['redirect_uri'] = $redirect_url;
        return $this->save($this->data);
    }
    
    public function getClient($client_id = null)
    {
        $options = ['conditions' => ['Client.client_id' => $client_id]];
        
        return $this->find('first', $options);
    }
}
