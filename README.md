Introduction
============

The LoginTC PHP client is a complete LoginTC [REST API][rest-api] client to
manage LoginTC organizations, users, domains, tokens and to create login
sessions.

This package allows a developer to quickly add multi-factor authentication to their web or portal login forms.

Installation
============

Get the code:

    git clone https://github.com/logintc/logintc-php.git
    
Example
=======

```php
<?php

require_once('LoginTC.php');

try {

    /*
     * instantiate the LoginTC client
     */

    $api_key = 'YOUR_API_KEY';

    $logintc = new LoginTC($api_key);

    /*
     * create a session
     */

    $domain_id = 'YOUR_DOMAIN_ID';
    $username = 'usertest';
    
    $domainAttribute = new DomainAttribute("Disclaimer", "Unauthorized access or use of this site (and any connected systems data) is prohibited");
    $attributes = array($domainAttribute);

    $session = $logintc->createSessionWithUsername($domain_id, $username, $attributes);

    /*
     * poll the state of the session
     */

    $t = time();
    $timeout = 45;
    $response = null;
    while ((time() - $t) < $timeout) {

        $polled_session = $logintc->getSession($domain_id, $session->getId());

        if ($polled_session->getState() != 'pending') {
            break;
        }

        sleep(1); // wait 1s
    }

    /*
     * check final state of the session
     */

    if ($polled_session->getState() === 'denied') {
        // denied or timeout
    }

    if ($polled_session->getState() === 'approved') {
        // user authenticated!
        // log user in here
    }

} catch (LoginTCException $exception) {
    die($exception->getMessage());
}


?>
```

Documentation
=============

See <https://www.logintc.com/docs>

Help
====

Email: <support@cyphercor.com>

<https://www.logintc.com>

[rest-api]: https://www.logintc.com/docs/rest-api
