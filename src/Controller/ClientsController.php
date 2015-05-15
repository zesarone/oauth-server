<?php
namespace OAuthServer\Controller;

use App\Controller\AppController;
use Crud\Controller\ControllerTrait;

/**
 * OauthClients Controller
 *
 * @property \OAuthServer\Model\Table\ClientsTable $Clients
 */
class ClientsController extends AppController
{

    use ControllerTrait;

    public $modelClass = 'OAuthServer.Clients';

    public function initialize()
    {
        parent::initialize();
        $this->viewClass = 'CrudView\View\CrudView';
        $tables = [
            'Clients',
            'Scopes'
        ];
        $this->loadComponent('Crud.Crud', [
            'actions' => [
                'index' => [
                    'className' => 'Crud.Index',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
                'view' => [
                    'className' => 'Crud.View',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
                'edit' => [
                    'className' => 'Crud.Edit',
                    'scaffold' => [
                        'tables' => $tables,
                        'fields' => [
                            'name',
                            'redirect_uri',
                            'parent_model',
                            'parent_id' => [
                                'label' => 'Parent ID',
                                'type' => 'text'
                            ]
                        ]
                    ]
                ],
                'add' => [
                    'className' => 'Crud.Add',
                    'scaffold' => [
                        'tables' => $tables,
                        'fields' => [
                            'name',
                            'redirect_uri',
                            'parent_model',
                            'parent_id' => [
                                'label' => 'Parent ID',
                                'type' => 'text'
                            ]
                        ]
                    ]
                ],
                'delete' => [
                    'className' => 'Crud.Delete',
                    'scaffold' => [
                        'tables' => $tables
                    ]
                ],
            ],
            'listeners' => [
                'CrudView.View',
                'Crud.RelatedModels',
                'Crud.Redirect',
                'Crud.Api'
            ],
        ]);
    }
}
