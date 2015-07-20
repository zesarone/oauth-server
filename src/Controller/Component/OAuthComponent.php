<?php
namespace OAuthServer\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\App;
use Cake\Network\Exception\NotImplementedException;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use OAuthServer\Model\Storage;

class OAuthComponent extends Component
{

    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    public $Server;

    /**
     * Grant types currently supported by the plugin
     *
     * @var array
     */
    protected $_allowedGrants = ['AuthCode', 'RefreshToken', 'ClientCredentials'];

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'tokenTTL' => 2592000, //TTL 30 * 24 * 60 * 60 in seconds
        'supportedGrants' => ['AuthCode', 'RefreshToken', 'ClientCredentials']
    ];

    /**
     * @param array $config Config array
     * @return void
     */
    public function initialize(array $config)
    {
        $server = new AuthorizationServer();

        $server->setSessionStorage(new Storage\SessionStorage());
        $server->setAccessTokenStorage(new Storage\AccessTokenStorage());
        $server->setClientStorage(new Storage\ClientStorage());
        $server->setScopeStorage(new Storage\ScopeStorage());
        $server->setAuthCodeStorage(new Storage\AuthCodeStorage());
        $server->setRefreshTokenStorage(new Storage\RefreshTokenStorage());

        $supportedGrants = isset($config['supportedGrants']) ? $config['supportedGrants'] : $this->config('supportedGrants');
        foreach ($supportedGrants as $grant) {
            if (!in_array($grant, $this->_allowedGrants)) {
                throw new NotImplementedException(__('The {0} grant type is not supported by the OAuth server'));
            }

            $class_name = '\\League\\OAuth2\\Server\\Grant\\' . $grant . 'Grant';
            $server->addGrantType(new $class_name());
        }

        $server->setAccessTokenTTL($this->config('tokenTTL'));

        $this->Server = $server;
    }

    /**
     * @param string $authGrant Grant type
     * @return bool|\Cake\Network\Response|void
     */
    public function checkAuthParams($authGrant) {
        $controller = $this->_registry->getController();
        try {
            return $this->Server->getGrantType($authGrant)->checkAuthorizeParams();
        } catch (\OAuthException $e) {
            if ($e->shouldRedirect()) {
                return $controller->redirect($e->getRedirectUri());
            }

            $controller->RequestHandler->renderAs($this, 'json');
            $controller->response->statusCode($e->httpStatusCode);
            $controller->response->header($e->getHttpHeaders());
            $controller->set('response', $e);
            return false;
        }
    }
}
