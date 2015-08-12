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
     * @param string $email
     * @return type
     */
    public function createToken($email)
    {
        $verification_code =  sha1(uniqid($email, true));
        return $verification_code;
    }

}
