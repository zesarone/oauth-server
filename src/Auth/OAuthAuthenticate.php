<?php
namespace OAuthServer\Auth;

use Cake\Auth\BaseAuthenticate;
use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use League\OAuth2\Server\Exception\OAuthException;
use League\OAuth2\Server\ResourceServer;
use OAuthServer\Model\Storage;

class OAuthAuthenticate extends BaseAuthenticate
{

    /**
     * @var \League\OAuth2\Server\ResourceServer
     */
    public $Server;

    protected $_exception;

    protected $_defaultConfig = [
        'continue' => false,
    ];

    public function __construct(ComponentRegistry $registry, $config) {
        parent::__construct($registry, $config);

        if ($this->config('server')) {
            $this->Server = $this->config('server');
        } else {
            $sessionStorage = new Storage\SessionStorage();
            $accessTokenStorage = new Storage\AccessTokenStorage();
            $clientStorage = new Storage\ClientStorage();
            $scopeStorage = new Storage\ScopeStorage();

            $server = new ResourceServer(
                $sessionStorage,
                $accessTokenStorage,
                $clientStorage,
                $scopeStorage
            );

            $this->Server = $server;
        }
    }

    /**
     * Authenticate a user based on the request information.
     *
     * @param \Cake\Network\Request $request Request to get authentication information from.
     * @param \Cake\Network\Response $response A response object that can have headers added.
     * @return mixed Either false on failure, or an array of user data on success.
     */
    public function authenticate(Request $request, Response $response)
    {
        return false;
    }

    public function unauthenticated(Request $request, Response $response)
    {
        if ($this->_config['continue']) {
            return false;
        }
        if (isset($this->_exception)) {
            $response->statusCode($this->_exception->httpStatusCode);
            $response->header($this->_exception->getHttpHeaders());
            $response->body(
                json_encode(
                    [
                        'error' => $this->_exception->errorType,
                        'message' => $this->_exception->getMessage()
                    ]
                )
            );
            return $response;
        }
        $message = __d('authenticate', 'You are not authenticated.');
        throw new BadRequestException($message);
    }

    public function getUser(Request $request)
    {
        try {
            $this->Server->isValidRequest();
            $ownerModel = $this->Server->getAccessToken()->getSession()->getOwnerType();
            $ownerId = $this->Server->getAccessToken()->getSession()->getOwnerId();
            $event = new Event('OAuth.getUser', $request, [$ownerModel, $ownerId]);
            EventManager::instance()->dispatch($event);
            if ($event->result) {
                return $event->result;
            } else {
                $model = TableRegistry::get($ownerModel);
                return $model->get($ownerId)->toArray();
            }
        } catch (OAuthException $e) {
            $this->_exception = $e;
            return false;
        }
    }

}