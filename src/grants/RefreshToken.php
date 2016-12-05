<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver\grants;


use jakim\authserver\base\UserIdentityInterface;

class RefreshToken extends GrantType
{
    public $refresh_token;

    public function rules()
    {
        return [
            ['refresh_token', 'required'],
        ];
    }

    public function findIdentity()
    {
        /** @var UserIdentityInterface $class */
        $class = \Yii::$app->user->identityClass;

        return $class::findIdentityByRefreshToken($this->refresh_token);
    }
}