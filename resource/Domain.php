<?php

/**
 * A domain object represents a service (e.g. VPN or website) and contains a
 * collection of users and token unlocking policies (e.g. key, passcode, minimum
 * lengths). A domain id consists of a unique 40-character hexadecimal unique
 * identifier.
 */
class Domain {
    private $id;
    private $name;
    private $type;
    private $keyType;

    /**
     *
     * @param id The domain 40-character hexadecimal unique identifier.
     * @param name The domain real name
     * @param type The domain type
     * @param keyType The domain keyType
     */
    public function __construct($id, $name, $type, $keyType) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->keyType = $keyType;
    }

    /**
     *
     * @param std class object
     */
    public static function fromObject($object) {
        if (!isset($object->id)) {
            return null;
        }
        if (!isset($object->name)) {
            return null;
        }
        if (!isset($object->type)) {
            return null;
        }
        if (!isset($object->keyType)) {
            return null;
        }
        return new Domain($object->id, $object->name, $object->type, $object->keyType);
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setKeyType($keyType) {
        $this->keyType = $keyType;
    }

    public function getKeyType() {
        return $this->keyType;
    }
}