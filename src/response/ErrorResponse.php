<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 03.12.2016
 */

namespace jakim\authserver\response;


use yii\base\Model;

class ErrorResponse extends Model
{
    public $error;

    public function rules()
    {
        return [
            ['error', 'safe'],
        ];
    }

}