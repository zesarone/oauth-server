<?php

namespace OAuthServer\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use Cake\Utility\Text;

class Client extends Entity
{
    /**
     * Create a new, pretty (as in moderately, not beautiful - that can't be guaranteed ;-) random client secret
     *
     * @return string
     */
    public function generateSecret()
    {
        $this->client_secret = Security::hash(Text::uuid(), 'sha1', true);
        $this->_original['client_secret'] = $this->client_secret;
    }

    protected function _getParent() {
        $parent_table = TableRegistry::get($this->parent_model);
        return $parent_table->get($this->parent_id);
    }
}