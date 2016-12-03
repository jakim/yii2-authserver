<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.11.2016
 */

namespace jakim\authserver\tokens;


interface TokenInterface
{
    public function generate();

    public function validate($token);
}