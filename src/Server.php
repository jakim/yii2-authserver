<?php
/**
 * Created for yii2-authserver.
 * User: jakim <pawel@jakimowski.info>
 * Date: 15.11.2016
 */

namespace jakim\authserver;


use jakim\authserver\base\BaseIdentityInterface;
use jakim\authserver\base\UserIdentityInterface;
use jakim\authserver\grants\GrantType;
use jakim\authserver\grants\PasswordCredentials;
use jakim\authserver\grants\RefreshToken;
use jakim\authserver\response\ErrorResponse;
use jakim\authserver\response\TokenResponse;
use jakim\authserver\tokens\Token;
use jakim\authserver\tokens\TokenInterface;
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
    public $accessTokenClass = Token::class;

    /**
     * In seconds, default: 1 month
     *
     * @var integer
     */
    public $refreshTokenTtl = 60 * 60 * 24 * 30;
    public $refreshTokenClass = Token::class;

    public $accessTokenType = 'Bearer';

    public $grantTypes = [
        'password' => PasswordCredentials::class,
        'refresh_token' => RefreshToken::class,
    ];

    /**
     * @var Request
     */
    public $request;

    protected $error;

    /**
     * @var UserIdentityInterface
     */
    protected $identity;

    public function init()
    {
        parent::init();
        $this->request = \Yii::$app->request;
    }

    /**
     * @return \jakim\authserver\response\TokenResponse|null
     * @throws \yii\web\ServerErrorHttpException
     *
     * @see https://tools.ietf.org/html/rfc6749#section-4.3
     */
    public function getResponse()
    {
        $grant = $this->resolveGrant();
        if ($grant && $grant->load($this->request->post(), '') && $grant->validate()) {
            /** @var UserIdentityInterface $identity */
            if (($identity = $grant->findIdentity()) === null) {
                \Yii::error("Invalid grant: " . get_class($grant), __METHOD__);
                $this->error = 'invalid_grant';

            } else {
                $identity->setAccessToken($this->generateAccessToken());
                $identity->setRefreshToken($this->generateRefreshToken());
                if (!$identity->save()) {
                    \Yii::error('User model error: ' . print_r($identity->getErrors(), true), __METHOD__);
                    throw new ServerErrorHttpException();
                }
                $this->identity = $identity;

                return $this->prepareResponse($identity);
            }
        } elseif ($grant && $grant->hasErrors()) {
            \Yii::error("Invalid request: " . print_r($grant->getErrors(), true), __METHOD__);
            $this->error = 'invalid_request';
        }

        return null;
    }

    /**
     * @return \jakim\authserver\base\UserIdentityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return \jakim\authserver\response\ErrorResponse|null
     *
     * @see https://tools.ietf.org/html/rfc6749#section-5.2
     */
    public function getError()
    {
        $this->setResponseHeaders();
        \Yii::$app->response->setStatusCode(400);

        return $this->error ? new ErrorResponse(['error' => $this->error]) : null;
    }

    public function validateAccessToken($token)
    {
        /** @var TokenInterface $generator */
        $generator = new $this->accessTokenClass($this->accessTokenTtl);

        return $generator->validate($token);
    }

    /**
     * @return \jakim\authserver\grants\GrantType|null
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
        $token = \Yii::createObject($this->accessTokenClass, [$this->accessTokenTtl]);

        return $token->generate();
    }

    protected function generateRefreshToken()
    {
        /** @var Token $token */
        $token = \Yii::createObject($this->refreshTokenClass, [$this->refreshTokenTtl]);

        return $token->generate();
    }

    /**
     * @param \jakim\authserver\base\BaseIdentityInterface $identity
     * @return \jakim\authserver\response\TokenResponse
     */
    protected function prepareResponse(BaseIdentityInterface $identity)
    {
        $this->setResponseHeaders();

        return new TokenResponse([
            'access_token' => $identity->getAccessToken(),
            'token_type' => $this->accessTokenType,
            'expires_in' => $this->accessTokenTtl,
            'refresh_token' => $identity->getRefreshToken(),
        ]);

    }

    protected function setResponseHeaders()
    {
        $headers = \Yii::$app->response->headers;
        $headers->set('Cache-Control', 'no-store');
        $headers->set('Pragma', 'no-cache');
    }
}