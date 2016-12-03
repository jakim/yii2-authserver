<?php
/**
 * Created for yii2-api-starter.
 * User: jakim <pawel@jakimowski.info>
 * Date: 16.11.2016
 */

namespace jakim\authserver\filters;


use jakim\authserver\Server;
use yii\di\Instance;

class HttpBearerAuth extends \yii\filters\auth\HttpBearerAuth
{
    /**
     * Name of server component.
     * @var string
     */
    public $authServer = 'authServer';

    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        if ($authHeader !== null && preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches)) {

            /** @var Server $server */
            $server = Instance::ensure($this->authServer, Server::class);
            if (!$server->validateAccessToken($matches[1])) {
                \Yii::error('Invalid access token', __METHOD__);
                return null;
            }

            $identity = $user->loginByAccessToken($matches[1], get_class($this));
            if ($identity === null) {
                $this->handleFailure($response);
            }
            return $identity;
        }

        return null;
    }

}