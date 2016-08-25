<?php
/**
 * Created by PhpStorm.
 * User: Glenn
 * Date: 2016-08-25
 * Time: 9:07 AM
 */

namespace Upload;


use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class ApiToken
{
    /** @var string */
    protected $api;

    /** @var string  */
    protected $username;

    protected $endPointBase;

    protected $endPointUri;

    protected $dc;

    /**
     * ApiToken constructor.
     *
     * @param string $api
     * @param string $username
     * @param string $endPointBase
     * @param string $endPointUri
     * @param string $dc
     */
    public function __construct(
        $api = '',
        $username = '',
        $endPointBase = 'https://identity.api.rackspacecloud.com',
        $endPointUri = 'https://identity.api.rackspacecloud.com/v2.0/tokens',
        $dc = 'ORD')
    {
        $this->api = $api;
        $this->username = $username;
        $this->endPointBase = $endPointBase;
        $this->endPointUri = $endPointUri;
        $this->dc = $dc;
    }

    public function getToken($timeout = 5) {
        $headers = ['Content-Type' => 'application/json'];
        $body = json_encode([
            'auth' => [
                'RAX-KSKEY:apiKeyCredentials' => [
                    'username' => $this->username,
                    'apiKey' => $this->api
                ]
            ]
        ]);
        $request = new Request('POST', $this->endPointUri, $headers, $body);

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->endPointBase,
            // You can set any number of default request options.
            'timeout'  => $timeout,
            'expect' => false,
            'verify' => false
        ]);

        $response = $client->send($request);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) { //Success
            $rawBody = (string)$response->getBody();
            $body = json_decode($rawBody, true);

            $expires = $body['access']['token']['expires'];
            $token_id = $body['access']['token']['id'];

            $url = '';
            
            foreach ($body['access']["serviceCatalog"] as $catalog) {
                if ($catalog['name'] == 'cloudFiles') {

                    foreach ($catalog['endpoints'] as $datacenters) {
                        if ($datacenters['region'] == $this->dc) {
                            $url = $datacenters['publicURL'];
                            break;
                        }
                    }

                    break;
                }
            }

            $dt = new \DateTime($expires); // <== instance from another API
            $carbon = Carbon::instance($dt); //parse the expiration

            return [
                "token" => $token_id,
                "carbon" => $carbon,
                "endPoint" => $url
            ];

        }
    }
}