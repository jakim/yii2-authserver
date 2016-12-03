<?php
/**
 * Created for yii2-api-starter.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.11.2016
 */

namespace jakim\authserver\tokens;


interface TokenInterface
{
    public function generate();

    public function validate($token);
}