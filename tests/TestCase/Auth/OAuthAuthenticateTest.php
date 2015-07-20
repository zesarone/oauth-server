<?php
namespace OAuthServer\Test\TestCase\Auth;

use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use OAuthServer\Auth\OAuthAuthenticate;

class OAuthAuthenticateTest extends TestCase
{

    /**
     * setup
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Collection = $this->getMock('Cake\Controller\ComponentRegistry');
        $this->auth = new OAuthAuthenticate($this->Collection, [
            'userModel' => 'Users'
        ]);
        TableRegistry::clear();
        $this->response = $this->getMock('Cake\Network\Response');
    }

    public function testAuthenticate()
    {
        $request = new Request('posts/index');
        $request->data = [];
        $this->assertFalse($this->auth->authenticate($request, $this->response));
    }
}
