<?php

namespace jakim\authserver\base;


interface FacebookUserIdentityInterface extends UserIdentityInterface
{
    /**
     * @param $user
     * @return UserIdentityInterface
     */
    public static function findIdentityByFacebookGraphUser($user);
}