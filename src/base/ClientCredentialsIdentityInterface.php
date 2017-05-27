<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 24.05.2017
 */

namespace jakim\authserver\base;


interface ClientCredentialsIdentityInterface extends BaseIdentityInterface
{
    /**
     * @param $clientId
     * @param $clientSecret
     * @return \jakim\authserver\base\BaseIdentityInterface
     */
    public static function findIdentityByClientCredentials($clientId, $clientSecret);
}