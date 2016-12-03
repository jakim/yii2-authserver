<?php

namespace jakim\authserver\base;


interface FacebookUserIdentityInterface
{
    public static function findByFacebookGraphUser($user);
}