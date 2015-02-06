<?php

class TokenState {
    /**
     * A code to load the token has been issued to a user but it has not yet been loaded.
     */
    const PENDING = 0;
    
    /**
     * A code has been used to load the token.
     */
    const ACTIVE = 1;
}

/**
 * A token object is a LoginTC credential tied to a domain and user pair. The LoginTC credential
 * lives on the LoginTC mobile app on the user's mobile device.
 */
class Token {
    private $state;
    private $code;

    /**
     *
     * @param state The state that the token is in.
     * @param code The code to load the token.
     */
    public function __construct($state, $code) {
        $this->state = $state;
        $this->code = $code;
    }

    /**
     *
     * @param std class object
     */
    public static function fromObject($object) {
        if (!isset($object->state)) {
            return null;
        }
        
        if (!isset($object->code)) {
            return null;
        }
        
        return new Token($object->state, $object->code);
    }

    public function getState() {
        return $this->state;
    }

    public function getCode() {
        return $this->code;
    }
}
