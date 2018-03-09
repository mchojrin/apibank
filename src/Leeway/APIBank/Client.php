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
    private function getEndPoint(): string
    {
        return $this->endPoint;
    }

    /**
     * @param string $endPoint
     * @return Client
     */
    private function setEndPoint(string $endPoint): Client
    {
        $this->endPoint = $endPoint;
        return $this;
    }

    /**
     * @return mixed
     */
    private function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return Client
     */
    private function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return GuzzleClient
     */
    private function getHttpClient(): GuzzleClient
    {
        return $this->httpClient;
    }

    /**
     * @param GuzzleClient $httpClient
     * @return Client
     */
    private function setHttpClient(GuzzleClient $httpClient): Client
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    private $token = null;
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

    /**
     * @return bool
     */
    public function isLogged(): bool
    {
        return !is_null( $this->getToken() );
    }

    /**
     *
     */
    public function logout(): void
    {
        $this->setToken( null );
    }

    /**
     * @return array
     */
    private function getViews() : array
    {
        $r = $this
            ->getHttpClient()
            ->get(
                'banks/322/accounts',
                [
                    'headers' => [
                        'Authorization' => 'JWT '.$this->getToken(),
                    ]
                ]
            );

        if ( $r->getStatusCode() == 200 ) {
            return json_decode( $r->getBody(), true )[0]['views_available'];
        } else {

            throw new Exception( $r->getBody(), $r->getStatusCode() );
        }
    }

    /**
     * @return array
     */
    private function getAccounts( string $view ) : array
    {
        $r = $this
            ->getHttpClient()
            ->get(
                'banks/322/accounts/'.$view,
                [
                    'headers' => [
                        'Authorization' => 'JWT '.$this->getToken(),
                    ]
                ]
            );

        if ( $r->getStatusCode() == 200 ) {

            return json_decode( $r->getBody(), true );
        } else {

            throw new Exception( $r->getBody(), $r->getStatusCode() );
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call( string $name, array $arguments )
    {
        if ( is_callable( [ $this, $name  ] ) ) {
            if ( $this->isLogged() ) {

                return call_user_func_array( [ $this, $name ], $arguments );
            } else {

                throw new Exception('You must be logged in before calling '.$name);
            }
        } else {

            throw new Exception('Unknown method '.$name);
        }
    }
}