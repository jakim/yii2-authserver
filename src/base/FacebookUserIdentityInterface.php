<?php

namespace jakim\authserver\base;


interface FacebookUserIdentityInterface extends UserIdentityInterface
{
    /**
     * @param $user
     * @return \jakim\authserver\base\BaseIdentityInterface
     */
    public static function findIdentityByFacebookGraphUser($user);
}