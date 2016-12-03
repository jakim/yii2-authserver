<?php
/**
 * Created for fitfck.
 * User: jakim <pawel@jakimowski.info>
 * Date: 30.11.2016
 */

namespace jakim\authserver\grants;


use jakim\authserver\base\FacebookUserIdentityInterface;
use Facebook\Facebook;

class FacebookToken extends GrantType
{
    public $app_id;
    public $app_secret;
    public $default_graph_version = 'v2.8';
    public $fields = 'name,picture.type(large){url}';

    public $facebook_token;

    public function rules()
    {
        return [
            ['facebook_token', 'required'],
        ];
    }

    public function findUser()
    {
        $graphUser = $this->getFacebookUser();
        /** @var FacebookUserIdentityInterface $class */
        $class = \Yii::$app->user->identityClass;
        return $class::findByFacebookGraphUser($graphUser);

    }

    /**
     * @return \Facebook\GraphNodes\GraphUser
     */
    protected function getFacebookUser()
    {
        $facebook = new Facebook([
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
            'default_graph_version' => $this->default_graph_version,
        ]);

        $url = 'me' . ($this->fields ? "?fields={$this->fields}" : '');
        $response = $facebook->get($url, $this->facebook_token);

        return $response->getGraphUser();
    }
}