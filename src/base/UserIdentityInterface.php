<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver\base;


interface UserIdentityInterface extends BaseIdentityInterface
{
    /**
     * @param $username
     * @param $password
     * @return \jakim\authserver\base\BaseIdentityInterface
     */
    public static function findIdentityByCredentials($username, $password);

    /**
     * @param $refreshToken
     * @return \jakim\authserver\base\BaseIdentityInterface
     */
    public static function findIdentityByRefreshToken($refreshToken);

}