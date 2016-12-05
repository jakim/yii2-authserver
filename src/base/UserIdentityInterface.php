<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver\base;


use jakim\authserver\Server;

interface UserIdentityInterface
{
    /**
     * @param $username
     * @param $password
     * @return UserIdentityInterface
     */
    public static function findIdentityByCredentials($username, $password);

    /**
     * @param $refreshToken
     * @return UserIdentityInterface
     */
    public static function findIdentityByRefreshToken($refreshToken);

    public function setAccessToken($token);

    public function getAccessToken();

    public function setRefreshToken($token);

    public function getRefreshToken();

    /**
     * @see Server line ~79
     */
    public function save();

    public function getErrors();
}