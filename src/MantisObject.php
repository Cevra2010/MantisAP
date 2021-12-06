<?php
namespace MantisAP;

use Exception;
use MantisAP\Exceptions\UnableToSaveObjectException;
use MantisAP\Interfaces\MantisObjectInterface;

/**
 *  MantisObjects ist die abstrakte Elternklasse einer jeden Mantis Objektes.
 *  Diese Klasse deligiert die jeweiligen Abfragen an die Klasse MantisRequest für das Request an die Mantis REST-Api
 */
abstract class MantisObject {

    /**
     * @var string Name des Objektes lt. Mantis-API
     */
    protected $objectName;

    /**
     * @var array Parameterliste des jeweiligen Objektes
     */
    protected $fields = [];

    /**
     * @var array Benötigte Felder zum speichern
     */
    protected $required = [];

    /**
     * @var bool Beschreibt, ob aktuell ungespeicherte Änderungen vorhanden sind
     */
    protected $dirty = false;

    /**
     * @var bool Beschreibt, ob das aktuelle Objekt jemals gespeichert wurde.
     */
    protected $stored = false;

    /**
     * @var array Beschreibt die Parameter, welche nur gelesen werden können.
     */
    protected $readonly = [];

    /**
     * Instanziiert ein neues MantisObjekt und ruft die findById Methode auf.
     *
     * @param integer $id
     * @return $this
     */
    public static function find($id) {
        $calledClass = get_called_class();
        $mantisObject = new $calledClass();
        return $mantisObject->findById($id);
    }

    /**
     * Ruft ein Parameter per Magic-Method ab und gibt dieses zurück.
     * Ist der jeweilige Parameter nicht gesetzt, wird null zurückgegeben.
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Setzt einen Parameter. Ist der Parameter im readonly-array vorhaden, wird eine Exception geworfen.
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if(in_array($name,$this->readonly) || $name == "id") {

            throw new Exception('The field "'.$name.'" is registered as readonly');
        }
        else {
            if(!isset($this->fields[$name]) || $this->fields[$name] != $value) {
                $this->fields[$name] = $value;
                $this->dirty = true;
            }
        }
    }

    /**
     * Gibt zurück, ob die aktuelle Instanz von der gespeicherten Instanz abweicht.
     *
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     *  Erzeugt eine MantisRequest instant und frage das gesuchte Objekt per REST-Api ab.
     *  Kann das Objekt nicht gefunden werden, wird null zurückgegeben.
     *  Wird das Objekt gefunden, wird die aktuelle Instanz mit den gefüllt und zurückgegeben.
     *
     * @param integer $objectId
     * @return $this|null
     */
    protected function findById($objectId) {

        // Mantis REST-Api request erzeugen und ausführen
        $request = new MantisRequest();
        $request->setMethodGet()->setObjectName($this->objectName)->setObjectId($objectId);
        $jsonResponse = $request->getResponse();

        // Prüfen ob die Antwort leer / null ist.
        if(!$jsonResponse)
        {
            return null;
        }

        $responseArray = (array)json_decode($jsonResponse);
        if(array_key_exists("code",$responseArray)) {
            throw new Exception("Error ".$responseArray["code"].": ".$responseArray['message']);
        }


        // JSON-Response in ein array umwandeln und in die Objektparameter schreiben.
        $objectResponse = (array)json_decode($jsonResponse);
        $object = $objectResponse[$this->objectName][0];

        $this->setParameters($object);

        // Instanzzustand setzen
        $this->dirty = false;
        $this->stored = true;

        return $this;
    }

    /**
     * Füllt die parameter mit dem angegebenen Array
     *
     * @param array|string $parameterArray
     */
    private function setParameters($parameterArray) {

        if(is_string($parameterArray))
        {
            $parameterArray = json_decode($parameterArray);
        }

        foreach($parameterArray as $name => $property)
        {
            $this->fields[$name] = $property;
        }
    }

    /**
     *  Ist das Objekt bereits gespeichert, wird ein Patch-Request ausgeführt.
     *  Ist das Obejekt noch nicht gespeichert wird Post-Request ausgeführt und das Objekt im Anschluss neu instanziert,
     *  da nun eine id für das Objekt verfügbar ist.
     *
     * @return $this
     */
    public function save() {
        foreach($this->required as $required) {
            if(!isset($this->fields[$required])) {
                throw new UnableToSaveObjectException("The field '".$required."' ist required to save an Object.");
            }
        }

        if($this->stored)
        {
            $mantisRequest = new MantisRequest();
            $mantisRequest->setObjectName($this->objectName)->setMethodPatch();
            $mantisRequest->setData($this->fields);
            $mantisRequest->setObjectId($this->fields['id']);
            $response = $mantisRequest->getResponse();
            if($response) {
                $this->stored = true;
                $this->dirty = false;
            }
            else {
                throw new UnableToSaveObjectException("Unable to save Object.");
            }
        }
        else
        {
            $mantisRequest = new MantisRequest();
            $mantisRequest->setObjectName($this->objectName)->setMethodPost();
            $mantisRequest->setData($this->fields);
            if($response = $mantisRequest->getResponse()) {
                if(!strpos($response,"Slim Application Error")) {
                    $this->setParameters($response);
                    $this->stored = true;
                    $this->dirty = false;
                }
                else {
                    throw new UnableToSaveObjectException("Unable to save Object.");
                }
            }
            else {
                throw new UnableToSaveObjectException("Unable to save Object.");
            }
        }

        return $this;
    }

    protected function getAll() {
        $calledClass = get_called_class();
        $mantisRequest = new MantisRequest();
        $mantisRequest->setMethodGet()->setObjectName($this->objectName);
        $response = $mantisRequest->getResponse();

        if($response) {
            if(is_string($response)) {
                $arrayResponse = (array)json_decode($response);
                if(count($arrayResponse[$this->objectName])) {
                    $objectsCollection = [];
                    foreach($arrayResponse[$this->objectName] as $project)
                    {
                        $object = new $calledClass();
                        $object->fillByApi($project);
                        $objectsCollection[] = $object;
                    }

                    return $objectsCollection;
                }
                return null;
            }
        }

        return false;
    }

    protected function fillByApi($apiObject) {
        $this->setParameters((array)$apiObject);
    }

    public function delete() {
        $mantisRequest = new MantisRequest();
        $mantisRequest->setMethodDelete()->setObjectName($this->objectName);
        $mantisRequest->setObjectId($this->fields['id']);
        $response = $mantisRequest->getResponse();
        if(!$response) {
            return false;
        }
        return true;

    }

    public static function all() {
        $calledClass = get_called_class();
        $mantisObject = new $calledClass();
        return $mantisObject->getAll();
    }
}