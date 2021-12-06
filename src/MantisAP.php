<?php
namespace MantisAP;

/**
 *
 */
class MantisAP {

    /**
     * @var MantisAP Die per statische Methode instanziierte MantisAP Instanz.
     */
    public static $instace;

    /**
     * @var string Die URL zur Mantis REST-Api
     */
    protected $url;

    /**
     * @var string Mantis Benutzer-Token
     */
    protected $token;

    /**
     * Setzten die URL der MantisBT-Instanz und den dazugehörigen Token.
     *
     * @param string $url
     * @param string $token
     */
    public function __construct(string $url, string $token)
    {
        self::$instace = $this;
        $this->url = $url;
        $this->token = $token;
        return $this;
    }

    /**
     * @return string Gibt den Token zurück.
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string Gibt die URL zurück.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return MantisAP Gibt die MantisAP Instanz zurück.
     */
    public static function getInstace()
    {
        return self::$instace;
    }
}