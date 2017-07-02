<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 24.05.2017
 */

namespace jakim\authserver\grants;


class ClientCredentials extends GrantType
{
    public $client_id;
    public $client_secret;

    public function rules()
    {
        return [
            [['client_id', 'client_secret'], 'required'],
        ];
    }


    public function findIdentity()
    {
        /** @var \jakim\authserver\base\ClientCredentialsIdentityInterface $class */
        $class = \Yii::$app->user->identityClass;

        return $class::findIdentityByClientCredentials($this->client_id, $this->client_secret);
    }
}