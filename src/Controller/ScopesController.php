<?php
namespace OAuthServer\Controller;

use App\Controller\AppController;
use Crud\Controller\ControllerTrait;

/**
 * Scopes Controller
 *
 * @property \OAuthServer\Model\Table\ScopesTable $Scopes
 */
class ScopesController extends AppController
{

    use ControllerTrait;

    public $modelClass = 'OAuthServer.Scopes';

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
                            'id' => [
                                'label' => 'ID',
                                'type' => 'text'
                            ],
                            'description',
                        ]
                    ]
                ],
                'add' => [
                    'className' => 'Crud.Add',
                    'scaffold' => [
                        'tables' => $tables,
                        'fields' => [
                            'id' => [
                                'label' => 'ID',
                                'type' => 'text'
                            ],
                            'description',
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
            ],
        ]);
    }
}
