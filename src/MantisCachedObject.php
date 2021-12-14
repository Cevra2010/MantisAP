<?php
namespace MantisAP;

/**
 *
 */
class MantisCachedObject {

    /**
     * @var string Objekt Name
     */
    private $objectName;

    /**
     * @var MantisObject
     */
    private $object;

    /**
     * Erzeugt ein neues Cache Objekt
     *
     * @param $objectName
     * @param MantisObject $object
     */
    public function __construct($objectName, MantisObject $object)
    {
        $this->objectName = $objectName;
        $this->object = $object;
    }

    /**
     * Gibt das MantisObjekt aus dem Cache zurück
     *
     * @return MantisObject
     */
    public function getObject() {
        return $this->object;
    }

    /**
     * Gibt den Objekt Namen zurück.
     *
     * @return mixed
     */
    public function getObjectName() {
        return $this->objectName;
    }

    /**
     * Gibt die Objekt Id zurück
     *
     * @return mixed|null
     */
    public function getObjectId() {
        return $this->object->id;
    }
}