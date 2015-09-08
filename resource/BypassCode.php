<?php

/**
 * A bypass code can be used as an alternative authentication method in the
 * event users misplace their 2nd factor device. A bypass code is a
 * user-specific 9 digit numerical code. Each user can have up to 5 different
 * codes. In order for a user to login using their bypass codes, the domain they
 * are accessing must have bypass code authentication enabled.
 */
class BypassCode {
    private $id;
    private $code;
    private $dtExpiry;
    private $user;
    private $usesAllowed;
    private $usesRemaining;

    /**
     *
     * @param id The bypass code's identifier.
     * @param code The 9 digit bypass code
     * @param dtExpiry The date which the bypass code expires
     * @param user The user's identifier.
     * @param usesAllowed The number of uses originally allowed
     * @param usesRemaining The number of uses remaining
     */
    public function __construct($id, $code, $dtExpiry, $user, $usesAllowed, $usesRemaining) {
        $this->id = $id;
        $this->code = $code;
        $this->dtExpiry = $dtExpiry;
        $this->user = $user;
        $this->usesAllowed = $usesAllowed;
        $this->usesRemaining = $usesRemaining;
    }

    /**
     *
     * @param std class object
     */
    public static function fromObject($object) {
        if (!isset($object->id)) {
            return null;
        }

        if (!isset($object->code)) {
            return null;
        }

        if (!isset($object->dtExpiry)) {
            return null;
        }

        if (!isset($object->user)) {
            return null;
        }

        if (!isset($object->usesAllowed)) {
            return null;
        }

        if (!isset($object->usesRemaining)) {
            return null;
        }
        

        return new BypassCode($object->id, $object->code, $object->dtExpiry, $object->user, $object->usesAllowed, $object->usesRemaining);
    }

    public function getId() {
        return $this->id;
    }

    public function getCode() {
        return $this->code;
    }

    public function getDtExpiry() {
        return $this->dtExpiry;
    }

    public function getUser() {
        return $this->user;
    }

    public function getUsesAllowed() {
        return $this->usesAllowed;
    }

    public function getUsesRemaining() {
        return $this->usesRemaining;
    }
}
