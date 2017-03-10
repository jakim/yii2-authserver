<?php

namespace jakim\authserver\actions;


use jakim\authserver\Server;
use yii\di\Instance;

/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 10.03.2017
 */
class TokenAction extends \yii\base\Action
{
    /**
     * Component ID.
     *
     * @var string
     */
    public $authServer = 'authServer';

    public function run()
    {
        /** @var Server $server */
        $server = Instance::ensure($this->authServer, Server::class);

        if (($response = $server->getResponse()) === null) {

            return $server->getError();
        }

        return $response;
    }
}