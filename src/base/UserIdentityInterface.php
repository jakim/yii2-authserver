<?php
/**
 * Created for yii2-api-starter.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver\base;


interface UserIdentityInterface
{
    public static function findByCredentials($username, $password);

    public static function findByRefreshToken($refreshToken);
}