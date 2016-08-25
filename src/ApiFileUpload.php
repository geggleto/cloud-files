<?php
/**
 * Created by PhpStorm.
 * User: Glenn
 * Date: 2016-08-25
 * Time: 11:02 AM
 */

namespace Upload;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;

class ApiFileUpload
{
    /** @var string  */
    protected $token;

    /** @var string */
    protected $tenant;

    /** @var string */
    protected $endPointBase;

    /** @var Client  */
    protected $client;

    /** @var string */
    protected $endPointUri;

    /** @var  string */
    protected $container;

    /**
     * ApiFileUpload constructor.
     * @param string $token
     * @param string $tenant
     * @param int $timeout
     * @param string $endPointBase
     * @param string $endPointUri
     */
    public function __construct($token = '', $tenant = '', $timeout = 5, $endPointBase = '', $endPointUri = '')
    {
        $this->token = $token;
        $this->tenant = $tenant;
        $this->endPointUri = $endPointUri;
        $this->endPointBase = $endPointBase;


        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $endPointBase,
            // You can set any number of default request options.
            'timeout'  => $timeout,
            'expect' => false,
            'verify' => false
        ]);

    }

    /**
     * @param string $file
     * @param string $objectName
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function uploadFileNow($file = '', $objectName = '') {
        $headers = [
            'Content-Type' => \mime_content_type($file),
            'X-Auth-Token' => $this->token
        ];
        $fh = fopen($file, "r");
        $request = new Request('PUT', $this->endPointUri."/".$this->container."/".$objectName, $headers, $fh);

        return $this->client->send($request);
    }

    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }


}