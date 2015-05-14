<?php
namespace OAuthServer\Controller;

use App\Controller\AppController;

/**
 * OauthClients Controller
 *
 * @property \OAuthServer\Model\Table\OauthClientsTable $Clients
 */
class ClientsController extends AppController
{

    public $modelClass = 'OAuthServer.Clients';

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('clients', $this->paginate($this->Clients));
        $this->set('_serialize', ['oauthClients']);
    }

    /**
     * View method
     *
     * @param string|null $id Oauth Client id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $client = $this->Clients->get($id, [
            'contain' => false
        ]);
        $this->set('client', $client);
        $this->set('_serialize', ['oauthClient']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $client = $this->Clients->newEntity();
        if ($this->request->is('post')) {
            $client = $this->Clients->patchEntity($client, $this->request->data);
            if ($this->Clients->save($client)) {
                $this->Flash->success('The oauth client has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The oauth client could not be saved. Please, try again.');
            }
        }
        $this->set(compact('client'));
        $this->set('_serialize', ['client']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Oauth Client id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $client = $this->Clients->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $client = $this->Clients->patchEntity($client, $this->request->data);
            if ($this->Clients->save($client)) {
                $this->Flash->success('The oauth client has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The oauth client could not be saved. Please, try again.');
            }
        }
        $this->set(compact('client'));
        $this->set('_serialize', ['client']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Oauth Client id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $client = $this->Clients->get($id);
        if ($this->Clients->delete($client)) {
            $this->Flash->success('The oauth client has been deleted.');
        } else {
            $this->Flash->error('The oauth client could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
