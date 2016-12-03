<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.11.2016
 */

namespace jakim\authserver\response;


use yii\base\Model;

class TokenResponse extends Model
{
    public $access_token;
    public $token_type;
    public $expires_in;
    public $refresh_token;

    public function rules()
    {
        return [
            [['access_token', 'token_type', 'expires_in', 'refresh_token'], 'safe'],
        ];
    }


}