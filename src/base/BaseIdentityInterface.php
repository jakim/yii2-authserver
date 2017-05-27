<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 27.05.2017
 */

namespace jakim\authserver\base;


interface BaseIdentityInterface
{
    public function setAccessToken($token);

    public function getAccessToken();

    public function setRefreshToken($token);

    public function getRefreshToken();

    /**
     * @see Server line ~84
     */
    public function save();

    public function getErrors();
}