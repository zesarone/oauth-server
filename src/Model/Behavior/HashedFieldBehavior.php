<?php
namespace OAuthServer\Model\Behavior;

use Cake\Database\ExpressionInterface;
use Cake\Event\Event;
use Cake\Log\Log;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\Utility\Security;

/**
 * Enable automatic field hashing when in Model::save() Model::find()
 */
class HashedFieldBehavior extends Behavior
{

    /**
     * Behavior defaults
     */
    protected $_defaultConfig = [
        'fields' => [],
        'priority' => 10000
    ];


    /**
     * Hash field when present in model data (POSTed data)
     */
    public function beforeSave(Event $event, Entity $entity)
    {
        $setting = $this->config();
        foreach ((array)$setting['fields'] as $field) {
            if ($entity->dirty($field)) {
                $data = $entity->{$field};
                $entity->{$field} = Security::hash($data, null, true);
            }
        }
        return true;
    }

    /**
     * Hash condition when it contains a field specified in setting
     */
    public function beforeFind(Event $event, Query $query)
    {
        $fields = $this->config('fields');
        /*
         * Traverses through the query 'where' part looking for comparisons that are in the field list.
         * Those are then hashed.
         */
        $query->traverse(function ($where) use ($fields, $query) {
            if ($where instanceof \Cake\Database\Expression\QueryExpression) {
                $where
                    ->traverse(function(ExpressionInterface $expression) use ($fields, $query) {
                        if ($expression instanceof \Cake\Database\Expression\Comparison) {
                            $field = $expression->getField();
                            $single_field = str_replace($query->repository()->alias() . '.', '', $field);
                            if (in_array($field, $fields) || in_array($single_field, $fields)) {
                                $value = $expression->getValue();
                                $expression->setValue(Security::hash($value, null, true));
                            }
                        }
                    });
            }
        }, ['where']);
        return $query;
    }
}
