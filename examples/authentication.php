<?php

require_once('LoginTC.php');
require_once('resource/Session.php');

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

    $session = $logintc->createSessionWithUsername($domain_id, $username);

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