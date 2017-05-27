<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 24.05.2017
 */

namespace jakim\authserver\grants;


class ClientCredentials extends GrantType
{
    public $clientId;
    public $clientSecret;

    public function rules()
    {
        return [
            [['clientId', 'clientSecret'], 'required'],
        ];
    }


    public function findIdentity()
    {
        /** @var \jakim\authserver\base\ClientCredentialsIdentityInterface $class */
        $class = \Yii::$app->user->identityClass;

        return $class::findIdentityByClientCredentials($this->clientId, $this->clientSecret);
    }
}