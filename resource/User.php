<?php

/**
 * A user object represents a person or an account. A user may belong to many
 * domains and may have many tokens on many devices. A user has a 40-character
 * hexadecimal unique identifier and an optional name and email address. A
 * LoginTC user object generally corresponds one-to-one with your application's
 * user object.
 */
class User {
    private $id;
    private $username;
    private $email;
    private $name;
    private $domains;

    /**
     *
     * @param username A unique 1-128 character username.
     * @param name The user's real name (or optionally username).
     * @param email The user's email address.
     * @param domains The user's domain memberships.
     */
    public function __construct($username, $name, $email, $domains) {
        $this->id = null;
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->domains = $domains;
    }

    /**
     *
     * @param id The user's identifier.
     * @param username A unique 1-128 character username.
     * @param email The user's email address.
     * @param name The user's real name (or optionally username).
     * @param domains The user's domain memberships.
     */
    public static function withId($id, $username, $name, $email, $domains) {
        $user = new User($username, $name, $email, $domains);
        $user->setId($id);
        
        return $user;
    }

    /**
     *
     * @param std class object
     */
    public static function fromObject($object) {
        if (!isset($object->id)) {
            return null;
        }
        
        if (!isset($object->username)) {
            return null;
        }
        
        if (!isset($object->name)) {
            return null;
        }
        
        if (!isset($object->email)) {
            return null;
        }
        
        if (!isset($object->domains)) {
            return null;
        }
        
        return User::withId($object->id, $object->username, $object->name, $object->email, $object->domains);
    }

    protected function setId($id) {
        $this->id = $id;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getName() {
        return $this->name;
    }
}
