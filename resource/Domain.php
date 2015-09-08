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
    private $maxAllowedRetries;
    private $requestTimeout;
    private $activationCodeExpiration;
    private $requestPollingEnabled;
    private $bypassEnabled;

    /**
     *
     * @param id The domain 40-character hexadecimal unique identifier.
     * @param name The domain real name
     * @param type The domain type
     * @param keyType The domain keyType
     * @param maxAllowedRetries Number of invalid retries allowed before token is revoked
     * @param requestTimeout Timeout of a request in seconds
     * @param activationCodeExpiration Activation code expiration in days
     * @param requestPollingEnabled Whether request polling is enabled
     * @param bypassEnabled Whether bypass codes are enabled
     */
    public function __construct($id, $name, $type, $keyType, $maxAllowedRetries, $requestTimeout, $activationCodeExpiration, $requestPollingEnabled, $bypassEnabled) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->keyType = $keyType;
        $this->maxAllowedRetries = $maxAllowedRetries;
        $this->requestTimeout = $requestTimeout;
        $this->activationCodeExpiration = $activationCodeExpiration;
        $this->requestPollingEnabled = $requestPollingEnabled;
        $this->bypassEnabled = $bypassEnabled;
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
        if (!isset($object->maxAllowedRetries)) {
            return null;
        }
        if (!isset($object->requestTimeout)) {
            return null;
        }
        if (!isset($object->activationCodeExpiration)) {
            return null;
        }
        if (!isset($object->requestPollingEnabled)) {
            return null;
        }
        if (!isset($object->bypassEnabled)) {
            return null;
        }
        return new Domain($object->id, $object->name, $object->type, $object->keyType, $object->maxAllowedRetries, $object->requestTimeout, $object->activationCodeExpiration, $object->requestPollingEnabled, $object->bypassEnabled);
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

    public function getMaxAllowedRetries() {
        return $this->maxAllowedRetries;
    }

    public function getRequestTimeout() {
        return $this->requestTimeout;
    }

    public function getActivationCodeExpiration() {
        return $this->activationCodeExpiration;
    }

    public function getRequestPollingEnabled() {
        return $this->requestPollingEnabled;
    }

    public function getBypassEnabled() {
        return $this->bypassEnabled;
    }
}