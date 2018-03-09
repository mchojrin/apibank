<?php
/**
 * Created by PhpStorm.
 * User: mauro
 * Date: 3/8/18
 * Time: 8:56 PM
 */

namespace Leeway\APIBank;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions as RequestOptions;

class Client
{
    private $endPoint;

    /**
     * @return string
     */
    public function getEndPoint(): string
    {
        return $this->endPoint;
    }

    /**
     * @param string $endPoint
     * @return Client
     */
    public function setEndPoint(string $endPoint): Client
    {
        $this->endPoint = $endPoint;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return Client
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return GuzzleClient
     */
    public function getHttpClient(): GuzzleClient
    {
        return $this->httpClient;
    }

    /**
     * @param GuzzleClient $httpClient
     * @return Client
     */
    public function setHttpClient(GuzzleClient $httpClient): Client
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    private $token;
    private $httpClient;

    public function __construct( $sandBox = false )
    {
        $this->setEndPoint( $sandBox ? 'https://apibank.pcnt.io/v1/' : 'https://apibank.pcnt.io/v1/' );
        $this->httpClient = new GuzzleClient(
            [
                'base_uri' => $this->getEndPoint(),
            ]
        );
    }

    /**
     * @param string $username
     * @param string $password
     */
    public function login( string $username, string $password )
    {
        $r = $this->getHttpClient()
            ->post(
                'login/jwt',
                [
                    RequestOptions::JSON => [
                        'username' => $username,
                        'password' => $password,
                    ]
                ]
            )
            ;

        if ( $r->getStatusCode() == 200 ) {
            $this->setToken( json_decode( $r->getBody() )->token );
        } else {

            throw new Exception( $r->getBody(), $r->getStatusCode() );
        }
    }
}