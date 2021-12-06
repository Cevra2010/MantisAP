<?php
namespace MantisAP;
use MantisAP\Objects\MantisProject;

class MantisAP {

    public static $instace;

    protected $url;
    protected $token;

    public function __construct(string $url, string $token)
    {
        self::$instace = $this;
        $this->url = $url;
        $this->token = $token;
        return $this;
    }

    public function project() {
        return new MantisProject();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return MantisAP
     */
    public static function getInstace()
    {
        return self::$instace;
    }
}