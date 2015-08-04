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
   
    /**
     * Create veriction_code method
     * 
     * @param type $verication_code
     * @return type
     */
    public function createValicationCode()
    {
        $verication_code =  md5(uniqid("yourrandomstringyouwanttoaddhere", true));
        return $verication_code;
    }

}
