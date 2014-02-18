<?php

/**
 * A domain attribute object represents an key, value pair presented during
 * the decide phase of an auth request.
 */
class DomainAttribute {

    public $key;
    public $value;

    /**
     * @param title A 1-40 character string which represents the domain attribute title.
     * @param content A 1-250 character string which represents the domain attribute content.
     */
    public function  __construct($title, $content) {
        $this->key = $title;
        $this->value = $content;
    }

}
