<?php

class SessionState
{
    const PENDING = 0;
    const APPROVED = 1;
    const DENIED = 2;
}

/**
 * A session is an authentication request sent to a user. Creating a session
 * initiates a push notification to the user's mobile device.
 */
class Session {

    private $id;
    private $state;

    /**
     * @param id The session's identifier.
     * @param state The state of the session.
     */
    public function __construct($id, $state) {
        $this->id = $id;
        $this->state = $state;
    }

    /**
     * @param std class object
     */
    public static function fromObject($object) {
        if (!isset($object->id)) {
            return null;
        }

        if (!isset($object->state)) {
            return null;
        }

        return new Session($object->id, $object->state);
    }

    public function getId() {
        return $this->id;
    }


    public function getState() {
        return $this->state;
    }


}


?>