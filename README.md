## The main purpose of this package is simplify the authentication process in restapi for mobile apps

[![Latest Stable Version](https://poser.pugx.org/jakim-pj/yii2-authserver/v/stable)](https://packagist.org/packages/jakim-pj/yii2-authserver) [![Total Downloads](https://poser.pugx.org/jakim-pj/yii2-authserver/downloads)](https://packagist.org/packages/jakim-pj/yii2-authserver) [![Latest Unstable Version](https://poser.pugx.org/jakim-pj/yii2-authserver/v/unstable)](https://packagist.org/packages/jakim-pj/yii2-authserver) [![License](https://poser.pugx.org/jakim-pj/yii2-authserver/license)](https://packagist.org/packages/jakim-pj/yii2-authserver)

### Authentication server is compatible with OAuth 2.0

### Success response [RFC 6749](https://tools.ietf.org/html/rfc6749#section-5.1)

```http
HTTP/1.1 200 OK
Server: nginx/1.6.2
Date: Wed, 23 Nov 2016 15:35:13 GMT
Content-Type: application/json; charset=UTF-8
```
```json
{
  "access_token": "4U0B6zMngrDuiNPyTErzsZ35gBVexoxC_1479923192",
  "token_type": "bearer",
  "expires_in": 7200,
  "refresh_token": "e-KaqLwjAgWrpp5A8c1zISfeK4dOEZex_1482507992"
}
```

### Error response [RFC 6749](https://tools.ietf.org/html/rfc6749#section-5.2)
```http
HTTP/1.1 400 Bad Request
Content-Type: application/json;charset=UTF-8
```
```json
{
  "error":"invalid_request"
}
```

### Errors
The authorization server responds with an HTTP 400 (Bad Request) status code 
and includes the following parameters with the response:
- **invalid_request**
The request is missing a required parameter, other than grant type.
- **invalid_grant**
The provided authorization grant (e.g., authorization code, resource owner credentials or refresh token) is invalid, expired, revoked.
- **unsupported_grant_type**
The authorization grant type is not supported by the authorization server.

### Installation

 1 . Configure component in `config/web.php`

Example:
```php
'components' => [
    'authServer' => [
        'class' => \jakim\authserver\Server::class,
        'grantTypes' => [
            'password' => \jakim\authserver\grants\PasswordCredentials::class,
            'refresh_token' => \jakim\authserver\grants\RefreshToken::class,
            'facebook_token' => [
                'class' => \jakim\authserver\grants\FacebookToken::class,
                'app_id' => $params['facebook.app_id'],
                'app_secret' => $params['facebook.app_secret'],
                'fields' => 'birthday,email,name,about,gender,picture.type(large){url}',
            ],
        ],
    ],
],
```

 2 . Implement identity interfaces (typically in `User` model):
- `jakim\authserver\base\UserIdentityInterface` for **password grant** and **refresh token grant**
- `jakim\authserver\base\FacebookUserIdentityInterface` for **facebook token grant**

Example:
```php
public static function findIdentityByCredentials($username, $password)
{
    $security = \Yii::$app->security;
    $model = static::findOne(['email' => $username]);
    if ($model && $security->validatePassword($password, $model->password)) {
        return $model;
    }

    return null;
}

public static function findIdentityByRefreshToken($refreshToken)
{
    return static::findOne(['refresh_token' => $refreshToken]);
}

public static function findIdentityByFacebookGraphUser($user)
{
    /** @var GraphUser $user */
    $model = static::findOne(['facebook_id' => $user->getId()]);
    if ($model === null) {
        $model = static::findOne(['email' => $user->getEmail()]);
    }

    // auto create user from facebook
    if ($model === null) {
        /** @var User $model */
        $model = UserFactory::newFromFacebookGraphUser($user);
        if (!$model->save()) {
            \Yii::error('Unable to create new user from facebook: ' . print_r($model->getErrors(), true), __METHOD__);

            return null;
        }
    } else {
        $model = UserFactory::updateFromFacebookGraphUser($model, $user);
        if (!$model->save()) {
            \Yii::error('Unable to update user from facebook: ' . print_r($model->getErrors(), true), __METHOD__);

            return null;
        }
    }

    return $model;
}

public function setAccessToken($token)
{
    $this->access_token = $token;
}

public function getAccessToken()
{
    return $this->access_token;
}

public function setRefreshToken($token)
{
    $this->refresh_token = $token;
}

public function getRefreshToken()
{
    return $this->refresh_token;
}
```
 3 . Create `token` action in auth controller

Example - custom action:

```php
public function actionToken()
{
    /** @var Server $server */
    $server = Instance::ensure('authServer', Server::class);

    if (($response = $server->getResponse()) === null) {

        return $server->getError();
    }

    return $response;
}
```

Example - predefined action class:

```php
    public function actions()
    {
        return [
            'token' => TokenAction::class,
        ];
    }
```

API Usage example:

#### Arguments for password grant type

Property|Type|Required|Description
--------|----|--------|-----------
username|varchar(255)|Yes|Email
password|varchar(255)|Yes|Password
grant_type|varchar(255)|Yes|Value always: `password`

#### Arguments for password grant type

Property|Type|Required|Description
--------|----|--------|-----------
refresh_token|varchar(255)|Yes|Refresh Token
grant_type|varchar(255)|Yes|Value always: `refresh_token`

#### Arguments for facebook grant type

Property|Type|Required|Description
--------|----|--------|-----------
facebook_token|varchar(255)|Yes|Facebook Token
grant_type|varchar(255)|Yes|Value always: `facebook_token`


 4 . Use custom auth filter `jakim\authserver\filters\HttpBearerAuth` (optionally)
