<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity.
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];

    /**
     * Adding Password Hashing
     * 
     * @param type $value
     * @return type
     */
    protected function _setPassword($password)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->hash($password);
    }

}
