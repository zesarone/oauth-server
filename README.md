# OAuth2 Server for CakePHP 3

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://img.shields.io/travis/uafrica/oauth-server/master.svg?style=flat-square)](https://travis-ci.org/uafrica/oauth-server)

A plugin for implementing an OAuth2 server in CakePHP 3. Built on top of the [PHP League's OAuth2 Server](http://oauth2.thephpleague.com/).

## Installation

Installation is done using composer. Run:

```bash
$ composer require uafrica/oauth-server
```

Once composer has installed the package, the plugin needs to be activated by running:

```bash
$ bin/cake plugin load OAuthServer --routes
$ bin/cake plugin load Crud
$ bin/cake plugin load CrudView
$ bin/cake plugin load BootstrapUI
```

Finally the database migrations need to be run.

```bash
$ bin/cake migrations migrate --plugin OAuthServer
```

## Configuration

It is assumed that you already have working Form based authentication using the built in CakePHP 3 authentication component.
If you do not, please read [the authentication chapter](http://book.cakephp.org/3.0/en/controllers/components/authentication.html).

Set OAuthServer as an authentication adaptor.

In your `AppController::beforeFilter()` method, add (or modify)

```php
$this->Auth->config('authenticate', [
    'Form',
    'OAuthServer'
]);
```

Change your login method to look as follows:

```php
public function login()
{
    if ($this->request->is('post')) {
        $user = $this->Auth->identify();
        if ($user) {
            $this->Auth->setUser($user);
            $redirect_uri = $this->Auth->redirectUrl();
            if ($this->request->query['redir'] === 'oauth') {
                $redirect_uri = [
                    'plugin' => 'OAuthServer',
                    'controller' => 'OAuth',
                    'action' => 'authorize',
                    '?' => $this->request->query
                ];
            }
            return $this->redirect($redirect_uri);
        } else {
            $this->Flash->error(
                __('Username or password is incorrect'),
                'default',
                [],
                'auth'
            );
        }
    }
}
```

Alternatively, if you are using the [Friends Of Cake CRUD plugin](https://github.com/friendsofcake/crud), add

```php
'login' => [
    'className' => 'OAuthServer.Login'
]
```

to your CRUD actions config.

## Usage

Visit `example.com/oauth/clients` to create OAuth clients, and `example.com/oauth/scopes` to create OAuth scopes.

The base OAuth2 path with `example.com/oauth`

## Customisation

The OAuth2 Server can be customised, the look for the various pages can be changed by creating templates in `Template/Plugin/OAuthServer/OAuth`

The server also fires a number of events that can be used to inject values into the process. The current events fired are:

* `OAuthServer.beforeAuthorize` - On rendering of the approval page for the user.
* `OAuthServer.afterAuthorize` - On the user authorising the client
* `OAuthServer.afterDeny` - On the user denying the client
* `OAuthServer.getUser` - On loading user details for authentication requests.