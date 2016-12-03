<?php
/**
 * Created for yii2-api-starter.
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

    public function findUser()
    {
        /** @var UserIdentityInterface $class */
        $class = \Yii::$app->user->identityClass;
        return $class::findByRefreshToken($this->refresh_token);
    }
}