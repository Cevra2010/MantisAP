<?php
namespace MantisAP;

use MantisAP\MantisCachedObject;

/**
 * Dieser Klasse verwaltet den Cache der Mantis abfragen.
 * Alle Ergebnisse werden im Cache registriert.
 * Bei erneutem Abruf werden die Daten aus dem Cache geladen und nicht erneut per API Request abgerufen.
 */
class MantisCache {

    /**
     * @var array Alle, im Cache vorhandenen Mantis Objekte.
     */
    private static $mantisCacheObjects = [];
    /**
     * @var array Alle durchlaufenen "all" abfragen.
     */
    private static $registeredAllResponses = [];

    /**
     * Fügt ein Mantis Objekt zum Cache hinzu.
     *
     * @param MantisObject $object
     */
    public static function add(MantisObject $object) {
        if(!self::exist($object,$object->id)) {
            $mantisCahe = new MantisCachedObject($object->getObjectName(),$object);
            self::$mantisCacheObjects[] = $mantisCahe;
        }
    }

    /**
     * Prüft, ob ein Mantis Objekt bereits im Cache vorhaden ist.
     *
     * @param MantisObject $mantisObject
     * @param $objectId integer der Objekt instanz
     * @return bool
     */
    public static function exist(MantisObject $mantisObject, $objectId) : bool {
        foreach(self::$mantisCacheObjects as $cachedObject) {
            if($cachedObject->getObjectName() == $mantisObject->getObjectName() && $cachedObject->getObject()->id == $objectId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Holt ein Objekt aus dem Cache
     *
     * @param $objectName MantisObject Name
     * @param $objectId integer Id der Objekt instanz
     * @return null
     */
    public static function get($objectName, $objectId)
    {
        foreach(self::$mantisCacheObjects as $cachedObject) {
            if($cachedObject->getObjectName() == $objectName && $cachedObject->getObjectId() == $objectId) {
                return $cachedObject->getObject();
            }
        }
        return null;
    }

    /**
     *  Bereinigt den Cache
     */
    public static function clear() {
        self::$mantisCacheObjects = [];
        self::$registeredAllResponses = [];
    }

    /**
     * Gibt eine all Collektion, aller Objekt des angegeben Objektnamen zurück.
     *
     * @param $objectName
     * @return array
     * @throws MantisAPException
     */
    public static function all($objectName) {
        if(!in_array($objectName,self::$registeredAllResponses)) {
            throw new MantisAPException("The all response in now able to run from Cache.");
        }


        $collection = [];
        foreach(self::$mantisCacheObjects as $cacheObject) {
            if($cacheObject->getObjectName() == $objectName)
            {
                $collection[] = $cacheObject->getObject();
            }
        }

        return $collection;
    }

    /**
     * Prüft ob eine "all" Objekt-Kollektion bereits im Cache gespeichert ist.
     *
     * @param $objectName
     * @return bool
     */
    public static function allExists($objectName) {
        if(in_array($objectName,self::$registeredAllResponses)) {
            return true;
        }
        return false;
    }


    /**
     * Registriert eine neue "all"-Kollektion im Cache unter dem angegenen Objekt-Namen.
     *
     * @param $objectName
     * @param $objectsCollection
     */
    public static function registerAllResponse($objectName, $objectsCollection) {
        self::$registeredAllResponses[] = $objectName;

        foreach ($objectsCollection as $mantisObject) {
            if(!self::exist($mantisObject,$mantisObject->id)) {
                self::add($mantisObject);
            }
        }
    }


}