<?php

/**
 * The Organization Name is a name that identifies the firm, group, or
 * institution to which the services you wish to protect belong. It is the name
 * of the company with which your domains and users will be associated. Example:
 * Acme Corporation.
 */
class Organization {
    private $name;

    /**
     *
     * @param name Name of the organization
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     *
     * @param std class object
     */
    public static function fromObject($object) {
        if (!isset($object->name)) {
            
            return null;
        }
        
        return new Organization($object->name);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }
}
