<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver\grants;


use yii\base\Model;

abstract class GrantType extends Model
{
    abstract public function findIdentity();
}