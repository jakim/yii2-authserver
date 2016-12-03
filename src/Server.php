<?php
/**
 * Created for yii2-api-starter.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver;


use jakim\authserver\grants\GrantType;
use jakim\authserver\grants\PasswordCredentials;
use jakim\authserver\grants\RefreshToken;
use jakim\authserver\response\TokenResponse;
use jakim\authserver\tokens\Token;
use jakim\authserver\tokens\TokenInterface;
use app\modules\api\v1\models\User;
use yii\base\Component;
use yii\web\Request;
use yii\web\ServerErrorHttpException;

class Server extends Component
{
    /**
     * In seconds, default: 2h
     *
     * @var integer
     */
    public $accessTokenTtl = 60 * 60 * 2;

    /**
     * In seconds, default: 1 month
     *
     * @var integer
     */
    public $refreshTokenTtl = 60 * 60 * 24 * 30;

    public $accessTokenType = 'bearer';

    public $grantTypes = [
        'password' => PasswordCredentials::class,
        'refresh_token' => RefreshToken::class,
    ];

    public $accessTokenClass = Token::class;
    public $refreshTokenClass = Token::class;

    /**
     * @var Request
     */
    public $request;

    protected $error;

    public function init()
    {
        parent::init();
        $this->request = \Yii::$app->request;
    }


    /**
     * @return TokenResponse|null
     * @throws ServerErrorHttpException
     */
    public function getResponse()
    {
        $grant = $this->resolveGrant();
        if ($grant && $grant->load($this->request->post(), '') && $grant->validate()) {
            /** @var User $user */
            if (($user = $grant->findUser()) === null) {
                \Yii::error("Invalid grant: " . get_class($grant), __METHOD__);
                $this->error = 'invalid_grant';

            } else {
                $user->access_token = $this->generateAccessToken();
                $user->refresh_token = $this->generateRefreshToken();
                if (!$user->save()) {
                    \Yii::error('User model error: ' . print_r($user->getErrors(), true), __METHOD__);
                    throw new ServerErrorHttpException();
                }

                return $this->prepareResponse($user);
            }
        } elseif ($grant && $grant->hasErrors()) {
            \Yii::error("Invalid request: " . print_r($grant->getErrors(), true), __METHOD__);
            $this->error = 'invalid_request';
        }

        return null;
    }

    /**
     * @return array
     *
     * @see https://tools.ietf.org/html/rfc6749#section-5.2
     */
    public function getError()
    {
        return $this->error ? ['error' => $this->error] : null;
    }

    public function validateAccessToken($token)
    {
        /** @var TokenInterface $generator */
        $generator = new $this->accessTokenClass($this->accessTokenTtl);

        return $generator->validate($token);
    }

    /**
     * @return GrantType|null
     */
    protected function resolveGrant()
    {
        $grantType = $this->request->post('grant_type');
        if (!isset($this->grantTypes[$grantType])) {
            \Yii::error("Unsupported grant type '{$grantType}'", __METHOD__);
            $this->error = 'unsupported_grant_type';

            return null;
        }
        $class = $this->grantTypes[$grantType];
        /** @var GrantType $grant */
        $grant = \Yii::createObject($class);

        return $grant;
    }

    protected function generateAccessToken()
    {
        /** @var Token $token */
        $token = new $this->accessTokenClass($this->accessTokenTtl);

        return $token->generate();
    }

    protected function generateRefreshToken()
    {
        /** @var Token $token */
        $token = new $this->refreshTokenClass($this->refreshTokenTtl);

        return $token->generate();
    }

    /**
     * @param $user
     * @return TokenResponse
     */
    protected function prepareResponse($user)
    {
        return new TokenResponse([
            'access_token' => $user->access_token,
            'token_type' => $this->accessTokenType,
            'expires_in' => $this->accessTokenTtl,
            'refresh_token' => $user->refresh_token,
        ]);

    }
}