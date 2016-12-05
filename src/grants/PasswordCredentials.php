<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver\grants;


use jakim\authserver\base\UserIdentityInterface;

class PasswordCredentials extends GrantType
{
    public $username;
    public $password;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
        ];
    }


    public function findIdentity()
    {
        /** @var UserIdentityInterface $class */
        $class = \Yii::$app->user->identityClass;

        return $class::findIdentityByCredentials($this->username, $this->password);
    }
}