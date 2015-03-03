<?php
namespace OAuthServer\Controller\Component;

use Cake\Controller\Component;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use OAuthServer\Model\Storage;

class OAuthComponent extends Component {

    /**
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    public $Server;

    public function initialize(array $config)
    {
        $server = new AuthorizationServer();

        $server->setSessionStorage(new Storage\SessionStorage());
        $server->setAccessTokenStorage(new Storage\AccessTokenStorage());
        $server->setClientStorage(new Storage\ClientStorage());
        $server->setScopeStorage(new Storage\ScopeStorage());
        $server->setAuthCodeStorage(new Storage\AuthCodeStorage());
        $server->setRefreshTokenStorage(new Storage\RefreshTokenStorage());

        $authCodeGrant = new AuthCodeGrant();
        $refreshTokenGrant = new RefreshTokenGrant();
        $server->addGrantType($authCodeGrant);
        $server->addGrantType($refreshTokenGrant);

        $server->setAccessTokenTTL(30 * 24 * 60 * 60);

        $this->Server = $server;

        parent::initialize($config);
    }

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