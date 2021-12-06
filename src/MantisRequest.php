<?php
namespace MantisAP;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;

/**
 *
 */
class MantisRequest {

    /**
     *
     */
    CONST API_URL_SUFFIX = 'api/rest/';

    /**
     * @var MantisAP
     */
    private $mantisAPInstance;
    /**
     * @var
     */
    private $method = 'GET';
    /**
     * @var
     */
    private $objectName;
    /**
     * @var
     */
    private $objectId;

    /**
     * @var
     */
    private $errorCode;

    /**
     * @var
     */
    private $errorMessage;

    /**
     * @var array
     */
    private $data = [];

    /**
     *
     */
    public function __construct()
    {
        $this->mantisAPInstance = MantisAP::getInstance();
        return $this;
    }

    /**
     * @return $this
     */
    public function setMethodGet() : MantisRequest {
        $this->method = 'GET';
        return $this;
    }

    /**
     * @return $this
     */
    public function setMethodPost() : MantisRequest {
        $this->method = 'POST';
        return $this;
    }

    /**
     * @return $this
     */
    public function setMethodPatch() : MantisRequest {
        $this->method = 'PATCH';
        return $this;
    }

    /**
     * @return $this
     */
    public function setMethodDelete() : MantisRequest {
        $this->method = 'DELETE';
        return $this;
    }

    /**
     * @param $objectName
     * @return $this
     */
    public function setObjectName($objectName) : MantisRequest {
        $this->objectName = $objectName;
        return $this;
    }

    /**
     * @param $objectId
     * @return $this
     */
    public function setObjectId($objectId) : MantisRequest {
        $this->objectId = $objectId;
        return $this;
    }

    /**
     * @return string
     */
    private function getRequestUrl(): string
    {
        $requestUrl = $this->mantisAPInstance->getUrl();
        if(substr($requestUrl,strlen($requestUrl)-1,strlen($requestUrl)) != "/") {
            $requestUrl .= "/";
        }
        $requestUrl .= self::API_URL_SUFFIX.$this->objectName;
        if($this->objectId) {
            $requestUrl .= "/".$this->objectId;
        }

        return $requestUrl;
    }

    /**
     *
     */
    public function getResponse() {
        try {
            $client = new Client();
            if(count($this->data)) {
                $request = new Request($this->method, $this->getRequestUrl(), $this->getHeaders(),json_encode($this->data));
            }
            else {
                $request = new Request($this->method, $this->getRequestUrl(), $this->getHeaders());
            }

            try {
                $response = $client->sendRequest($request);
            }
            catch (ClientExceptionInterface $e) {
                die("MantisAP - HTTP-Client error: ".$e->getMessage());
            }

            switch ($this->method) {
                case "POST":
                case "GET";
                case "PATCH":
                    return $response->getBody()->getContents();
                case "DELETE":
                    return true;
                default:
                    return false;
            }
        }
        catch (ClientException $clientException) {
            switch($clientException->getCode()) {
                case 401:
                    $this->errorCode = 401;
                    $this->errorMessage = "Unauthorized. Please set an API token.";
                    break;
                case 403:
                    $this->errorCode = 403;
                    $this->errorMessage = 'Unauthorized. API-token cannot access this area.';
                    break;
                default:
                    $this->errorCode = $clientException->getCode();
                    $this->errorMessage = $clientException->getMessage();
            }
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * @param $data
     */
    public function setData($data) {
        $this->data = $data;
    }


    /**
     * @return array|string[]
     */
    private function getHeaders(): array
    {

        $headerArray = [
            'Authorization' => $this->mantisAPInstance->getToken(),
        ];

        if(count($this->data)) {
            $headerArray = array_merge($headerArray,[
                'Content-Type' => 'application/json',
            ]);
        }

        return $headerArray;
    }


}