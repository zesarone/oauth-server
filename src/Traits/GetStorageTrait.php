<?php

namespace OAuthServer\Traits;

use Cake\Core\App;
use Cake\Core\Exception\Exception;

trait GetStorageTrait
{
    protected function _resolveClassName($class)
    {
        $className = App::className($class, 'Model/Storage', 'Storage');
        if (!$className) {
            throw new Exception(sprintf('Storage class "%s" was not found.', $class));
        }
        return $className;
    }

    protected function _getStorage($name)
    {
        $config = $this->config('storages.' . $name);

        if (empty($config)) {
            throw new Exception(sprintf('Storage class "%s" has no configuration', $name));
        }

        $className = $this->_resolveClassName($config['className']);
        return new $className();
    }
}