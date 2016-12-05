# README #

This README would normally document whatever steps are necessary to get your application up and running.

### What is this repository for? ###

* The main purpose of this package is simplify of the authentication process.
* dev
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

* Summary of set up
* Configuration
* Dependencies
* Database configuration
* How to run tests
* Deployment instructions


## Componet configuration

```
#!php

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
                ]
            ],
        ],
```

## Usage example 


```
#!php

        /** @var Server $server */
        $server = Instance::ensure('authServer', Server::class);

        if (($response = $server->getResponse()) === null) {
            \Yii::$app->response->setStatusCode(400);

            return $server->getError();
        }

        return $response;
```

## Implement UserIdentityInterface

In User model.

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact