<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Wallet Entity.
 */
class Wallet extends Entity
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

    /*
     * Check exist of wallet.
     * return bolean
     */

    public function checkCreatedWallet($time)
    {
        if (($time->month < $this->created->month) && ($time->year <= $this->created->year)) {
            return false;
        } else {
            return true;
        }
    }

}
