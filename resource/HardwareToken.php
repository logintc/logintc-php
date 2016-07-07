<?php

/**
 * A hardware token is a physical authentication method. A hardware token must
 * implement RFC 6238 (TOTP). Each user can have one hardware token. In order
 * for a user to login using their hardware token, the domain they are accessing
 * must have hardware token authentication enabled.
 */
class HardwareToken {
    private $id;
    private $alias;
    private $serialNumber;
    private $type;
    private $timeStep;
    private $syncState;
    private $user;

    /**
     * @param id The hardware token's identifier.
     * @param alias A short-hand mutable name
     * @param serialNumber The serial number of the hardware token
     * @param type Can be either TOTP6 or TOTP8
     * @param timeStep The number of seconds for the time step
     * @param syncState The state of the hardware token
     * @param user The user's identifier.
     */
    public function __construct($id, $alias, $serialNumber, $type, $timeStep, $syncState, $user) {
        $this->id = $id;
        $this->alias = $alias;
        $this->serialNumber = $serialNumber;
        $this->type = $type;
        $this->timeStep = $timeStep;
        $this->syncState = $syncState;
        $this->user = $user;
    }

    /**
     *
     * @param std class object
     */
    public static function fromObject($object) {
        if (!isset($object->id)) {
            return null;
        }

        if (!isset($object->alias)) {
            return null;
        }

        if (!isset($object->serialNumber)) {
            return null;
        }

        if (!isset($object->type)) {
            return null;
        }

        if (!isset($object->timeStep)) {
            return null;
        }

        if (!isset($object->syncState)) {
            return null;
        }

        if (!isset($object->user)) {
            return null;
        }


        return new HardwareToken($object->id, $object->alias, $object->serialNumber, $object->type, $object->timeStep, $object->syncState, $object->user);
    }

    public function getId() {
        return $this->id;
    }

    public function getAlias() {
        return $this->alias;
    }

    public function getSerialNumber() {
        return $this->serialNumber;
    }

    public function getType() {
        return $this->type;
    }

    public function getTimeStep() {
        return $this->timeStep;
    }

    public function getSyncState() {
        return $this->syncState;
    }

    public function getUser() {
        return $this->user;
    }
}
