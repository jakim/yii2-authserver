<?php
/**
 * Created for yii2-api-starter.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver\tokens;


class Token implements TokenInterface
{
    public $ttl;

    public function __construct($ttl)
    {
        $this->ttl = $ttl;
    }

    public function generate()
    {
        $token = \Yii::$app->security->generateRandomString();
        return sprintf('%s_%s', $token, time() + $this->ttl);
    }

    public function validate($token)
    {
        $pos = strrpos($token, '_');
        $expires = substr($token, $pos + 1);
        if ($expires > time()) {
            return true;
        }
        return false;
    }
}