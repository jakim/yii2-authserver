<?php
/**
 * Created for yii2-api-starter.
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


    public function findUser()
    {
        /** @var UserIdentityInterface $class */
        $class = \Yii::$app->user->identityClass;
        return $class::findByCredentials($this->username, $this->password);
    }
}