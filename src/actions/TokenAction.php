<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 10.03.2017
 */

namespace jakim\authserver\actions;


use jakim\authserver\Server;
use yii\base\Action;
use yii\di\Instance;

class TokenAction extends Action
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