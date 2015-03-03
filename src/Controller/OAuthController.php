<?php
namespace OAuthServer\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Event\EventManager;
use League\OAuth2\Server\Exception\AccessDeniedException;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\Util\RedirectUri;

/**
 * Class OAuthController
 *
 * @property \OAuthServer\Controller\Component\OAuthComponent $OAuth
 */
class OAuthController extends AppController
{
    public function initialize()
    {
        $this->loadComponent(
            'Security',
            [
                'blackHoleCallback' => 'blackHole'
            ]
        );
        $this->loadComponent(
            'Csrf',
            [
                'secure' => true
            ]
        );
        $this->loadComponent('OAuth.OAuth');
        $this->loadComponent('RequestHandler');
        parent::initialize();
    }

    /**
     * beforeFilter
     *
     */
    public function beforeFilter(Event $event)
    {
        if ($this->Auth) {
            $this->Auth->allow(['oauth', 'authorize', 'accessToken']);
        }

        $this->Security->config('unlockedActions', ['accessToken']);

        if ($this->isAction('accessToken')) {
            $this->eventManager()->off($this->Csrf);
        }

        parent::beforeFilter($event);
    }

    public function oauth()
    {
        if ($this->OAuth->checkAuthParams('authorization_code')) {
            if (!$this->Auth->user()) {
                $query = $this->request->query;
                $query['redir'] = 'oauth';

                $this->redirect(
                    [
                        'plugin' => false,
                        'controller' => 'Users',
                        'action' => 'login',
                        '?' => $query
                    ]
                );
            } else {
                $this->redirect(
                    [
                        'action' => 'authorize',
                        '?' => $this->request->query
                    ]
                );
            }
        }
    }

    public function authorize() {
        if (!$authParams = $this->OAuth->checkAuthParams('authorization_code')) {
            return;
        }

        if (!$this->Auth->user()) {
            $query = $this->request->query;
            $query['redir'] = 'oauth';

            $this->redirect(
                [
                    'plugin' => false,
                    'controller' => 'Users',
                    'action' => 'login',
                    '?' => $query
                ]
            );
        }

        $event = new Event('OAuth.beforeAuthorize', $this);
        EventManager::instance()->dispatch($event);

        if (is_array($event->result)) {
            $this->set($event->result);
        }

        if ($this->request->is('post') && $this->request->data['authorization'] === 'Approve') {
            $ownerModel = isset($this->request->data['owner_model']) ? $this->request->data['owner_model'] : 'Users';
            $ownerId = isset($this->request->data['owner_id']) ? $this->request->data['owner_id'] : $this->Auth->user('id');
            $redirectUri = $this->OAuth->Server->getGrantType('authorization_code')->newAuthorizeRequest($ownerModel, $ownerId, $authParams);
            $event = new Event('OAuth.afterAuthorize', $this);
            EventManager::instance()->dispatch($event);
            return $this->redirect($redirectUri);
        } elseif ($this->request->is('post')) {
            $event = new Event('OAuth.afterDeny', $this);
            EventManager::instance()->dispatch($event);

            $error = new AccessDeniedException();

            $redirectUri = new RedirectUri(
                $authParams['redirect_uri'],
                [
                    'error' => $error->errorType,
                    'message' => $error->getMessage()
                ]
            );

            return $this->redirect($redirectUri);
        }

        $this->set('authParams', $authParams);
        $this->set('user', $this->Auth->user());
    }

    public function accessToken() {
        try {
            $response = $this->OAuth->Server->issueAccessToken();
            $this->RequestHandler->renderAs($this, 'json');
            $this->set('response', $response);
        } catch (OAuthException $e) {
            $this->RequestHandler->renderAs($this, 'json');
            $this->response->statusCode($e->httpStatusCode);
            $this->response->header($e->getHttpHeaders());
            $this->set('response', $e);
            return false;
        }
    }

}