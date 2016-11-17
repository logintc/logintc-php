<?php
if (!function_exists('curl_init')) {
    throw new Exception('LoginTC requires the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('LoginTC  needs the JSON PHP extension.');
}

require_once 'AdminRestClient.php';
require_once 'resource/DomainAttribute.php';
require_once 'resource/User.php';
require_once 'resource/Token.php';
require_once 'resource/Session.php';
require_once 'resource/Organization.php';
require_once 'resource/Domain.php';
require_once 'resource/BypassCode.php';
require_once 'resource/HardwareToken.php';

/**
 * A generic LoginTC client exception.
 */
class LoginTCException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }
}

/**
 * Exception for failures because of API.
 */
class ApiLoginTCException extends LoginTCException {
    private $error_code;
    private $error_message;

    function __construct($error_code, $error_message) {
        $this->error_code = $error_code;
        $this->error_message = $error_message;

        parent::__construct($this->error_code . ': ' . $error_message);
    }

    function getErrorCode() {
        return $this->error_code;
    }

    function getErrorMessage() {
        return $this->error_message;
    }
}

/**
 * LoginTC Admin client to manage LoginTC users, domains, tokens and sessions.
 */
class LoginTC {

    /**
     * Client name used for user agent.
     */
    const NAME = 'LoginTC-PHP';

    /**
     * Client version used for user agent.
     */
    const VERSION = '1.2.4';

    /**
     * The default LoginTC Admin.
     */
    const DEFAULT_HOST = 'cloud.logintc.com';

    /**
     * The LoginTC Admin HTTP client.
     */
    protected $adminRestClient;

    public function __construct($api_key, $host = self::DEFAULT_HOST) {
        $user_agent = self::NAME . '/' . self::VERSION;

        if (!preg_match('/^https:\/\//', $host)) {
            $host = "https://" . $host;
        }

        $this->adminRestClient = new AdminRestClient($host, $api_key, $user_agent);
    }

    /**
     * Get user info.
     *
     * @param userId The user's identifier.
     * @return The requested user.
     * @throws LoginTCException
     */
    public function getUser($user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/users/' . $user_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return User::fromObject($response);
    }

    /**
     * Get user info.
     *
     * @param username The user's username.
     * @return The requested user.
     * @throws LoginTCException
     */
    public function getUserByUsername($username) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/users?username=' . $username));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return User::fromObject($response);
    }

    /**
     * Get users info.
     *
     * @param page number. (optional)
     * @return The requested users.
     * @throws LoginTCException
     */
    public function getUsers($page = 1) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/users?page=' . $page));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        foreach ($response as $user) {
            $users[] = User::fromObject($user);
        }
        return $users;
    }

    /**
     * Create a new user.
     *
     * @param username The new user's username.
     * @param email The new user's email address.
     * @param name The new user's real name.
     * @return The newly created user.
     * @throws LoginTCException
     */
    public function createUser($username, $name, $email) {
        $body = json_encode(array(
                'username' => $username,
                'name' => $name,
                'email' => $email
        ));

        try {
            $response = $this->jsonResponse($this->adminRestClient->post('/api/users', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return User::fromObject($response);
    }

    /**
     * Update a user.
     *
     * @param userId The target user's identifier.
     * @param email The user's new email address. Use null if no change.
     * @param name The user's new name. Use null if no change.
     * @return The updated user.
     * @throws LoginTCException
     */
    public function updateUser($user_id, $name, $email) {
        $body = json_encode(array(
                'name' => $name,
                'email' => $email
        ));

        try {
            $response = $this->jsonResponse($this->adminRestClient->put('/api/users/' . $user_id, $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return User::fromObject($response);
    }

    /**
     * Delete a user.
     *
     * @param userId The target user's identifier.
     * @throws LoginTCException
     */
    public function deleteUser($user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/users/' . $user_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    /**
     * Add a user to a domain.
     *
     * @param domainId The target domain identifier.
     * @param userId The target user identifier.
     * @throws LoginTCException
     */
    public function addDomainUser($domain_id, $user_id) {
        $body = '';

        try {
            $response = $this->jsonResponse($this->adminRestClient->put('/api/domains/' . $domain_id . '/users/' . $user_id, $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    /**
     * Set a domain's users. If the provided users do not yet exist then they will be created in the
     * Organization. Existing organization users will be added to the domain. The existing domain
     * users that are not present in the users parameter will be removed from the domain and their
     * tokens will be revoked.
     *
     * @param domainId The target domain identifier.
     * @param users A list of users that should belong to the domain.
     * @throws LoginTCException
     */
    public function setDomainUsers($domain_id, $users) {
        $user_array = array();
        foreach ($users as $user) {
            array_push($user_array, array(
                    'username' => $user->getUsername(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail()
            ));
        }
        $body = json_encode($user_array);

        try {
            $response = $this->jsonResponse($this->adminRestClient->put('/api/domains/' . $domain_id . '/users', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    /**
     * Remove user from domain
     *
     * @param domainId The target domain identifier.
     * @param userId The target user identifier.
     * @throws LoginTCException
     */
    public function removeDomainUser($domain_id, $user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/domains/' . $domain_id . '/users/' . $user_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    /**
     * Create a user token if one does not exist or if it has been revoked. Does nothing if the
     * token is already active or not yet loaded.
     *
     * @param domainId The target domain identifier.
     * @param userId The target user identifier.
     * @return The newly created token.
     * @throws LoginTCException
     */
    public function createUserToken($domain_id, $user_id) {
        $body = '';

        try {
            $response = $this->jsonResponse($this->adminRestClient->put('/api/domains/' . $domain_id . '/users/' . $user_id . '/token', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return Token::fromObject($response);
    }

    /**
     * Gets a user's token information. Throws a LoginTCException if a token does not exist or has
     * been revoked.
     *
     * @param domainId The target domain identifier.
     * @param userId The target user identifier.
     * @return The requested session.
     * @throws LoginTCException
     */
    public function getUserToken($domain_id, $user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/domains/' . $domain_id . '/users/' . $user_id . '/token', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return Token::fromObject($response);
    }

    /**
     * Delete (i.e. revoke) a user's token.
     *
     * @param domainId The target domain identifier.
     * @param userId The target user identifier.
     * @throws LoginTCException
     */
    public function deleteUserToken($domain_id, $user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/domains/' . $domain_id . '/users/' . $user_id . '/token'));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    /**
     * Create a LoginTC request.
     *
     * @param domainId The target domain identifier.
     * @param userId The target user identifier.
     * @param attributes Map of attributes to be included in the LoginTC request. Null is permitted
     *            for no attributes.
     * @return Newly created session.
     * @throws NoTokenLoginTCException
     * @throws LoginTCException
     */
    public function createSession($domain_id, $user_id, $attributes = array(), $ip_address = NULL, $bypass_code = NULL, $otp = NULL) {
        $body_arr = array(
                        'user' => array(
                            'id' => $user_id
        ),
                        'attributes' => $attributes
        );

        if (!is_null($ip_address)) {
            $body_arr['ipAddress'] = $ip_address;
        }

        if (!is_null($bypass_code)) {
            $body_arr['bypasscode'] = $bypass_code;
        } else if (!is_null($otp)) {
            $body_arr['otp'] = $otp;
        }

        $body = json_encode($body_arr);

        try {
            $response = $this->jsonResponse($this->adminRestClient->post('/api/domains/' . $domain_id . '/sessions', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return Session::fromObject($response);
    }

    /**
     * Create a LoginTC request.
     *
     * @param domainId The target domain identifier.
     * @param username The target user username.
     * @param attributes Array of text based request attributes.
     * @param ip_address IP Address of originating request.
     * @param bypass_code Optional Bypass Code.
     * @param otp Optional OTP.
     * @return Newly created session.
     * @throws NoTokenLoginTCException
     * @throws LoginTCException
     */
    public function createSessionWithUsername($domain_id, $username, $attributes = array(), $ip_address = NULL, $bypass_code = NULL, $otp = NULL) {
        $body_arr = array(
                        'user' => array(
                            'username' => $username
        ),
                        'attributes' => $attributes
        );

        if (!is_null($ip_address)) {
            $body_arr['ipAddress'] = $ip_address;
        }

        if (!is_null($bypass_code)) {
            $body_arr['bypasscode'] = $bypass_code;
        } else if(!is_null($otp)) {
            $body_arr['otp'] = $otp;
        }

        $body = json_encode($body_arr);

        try {
            $response = $this->jsonResponse($this->adminRestClient->post('/api/domains/' . $domain_id . '/sessions', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return Session::fromObject($response);
    }

    /**
     * Get a session's information.
     *
     * @param domainId The target domain identifier.
     * @param sessionId The target session identifier.
     * @return The requested session.
     * @throws LoginTCException
     */
    public function getSession($domain_id, $session_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/domains/' . $domain_id . '/sessions/' . $session_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return Session::fromObject($response);
    }

    /**
     * Delete (i.e. cancel) a session.
     *
     * @param domainId The target domain identifier.
     * @param sessionId The target session identifier.
     * @throws LoginTCException
     */
    public function deleteSession($domain_id, $session_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/domains/' . $domain_id . '/sessions/' . $session_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    /**
     * Get Ping status.
     *
     * @return Status (true if OK).
     * @throws LoginTCException
     */
    public function getPing() {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/ping'));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        if ($response->status == 'OK') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getOrganization() {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/organization'));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        return Organization::fromObject($response);
    }

    public function getDomain($domain_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/domains/' . $domain_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        return Domain::fromObject($response);
    }

    public function getDomainImage($domain_id) {
        try {
            $response = $this->adminRestClient->get_bytes('/api/domains/' . $domain_id . '/image', 'image/png');
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        return $response;
    }

    public function getDomainUser($domain_id, $user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/domains/' . $domain_id . '/users/' . $user_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        return User::fromObject($response);
    }

    public function getDomainUsers($domain_id, $page = 1) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/domains/' . $domain_id . '/users?page=' . $page));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        foreach ($response as $user) {
            $users[] = User::fromObject($user);
        }
        return $users;
    }

    public function getBypassCode($bypass_code_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/bypasscodes/' . $bypass_code_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return BypassCode::fromObject($response);
    }

    public function getBypassCodes($user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/users/' . $user_id . '/bypasscodes'));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        foreach ($response as $bypass_code) {
            $bypass_codes[] = BypassCode::fromObject($bypass_code);
        }
        return $bypass_codes;
    }

    public function createBypassCode($user_id, $uses_allowed = 1, $expiration_time = 0) {
        $body = json_encode(array(
                'usesAllowed' => $uses_allowed,
                'expirationTime' => $expiration_time
        ));

        try {
            $response = $this->jsonResponse($this->adminRestClient->post('/api/users/' . $user_id . '/bypasscodes', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return BypassCode::fromObject($response);
    }

    public function deleteBypassCode($bypass_code_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/bypasscodes/' . $bypass_code_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    public function deleteBypassCodes($user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/users/' . $user_id . '/bypasscodes'));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    public function getHardwareToken($hardware_token_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/hardware/' . $hardware_token_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return HardwareToken::fromObject($response);
    }

    public function getUserHardwareToken($user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/users/' . $user_id . '/hardware'));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return HardwareToken::fromObject($response);
    }

    public function getHardwareTokens($page = 1) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->get('/api/hardware?page=' . $page));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
        foreach ($response as $hardware_token) {
            $hardware_tokens[] = HardwareToken::fromObject($hardware_token);
        }
        return $hardware_tokens;
    }

    public function createHardwareToken($alias, $serialNumber, $type, $timeStep, $seed) {
        $body_arr = array(
                'serialNumber' => $serialNumber,
                'type' => $type,
                'timeStep' => $timeStep,
                'seed' => $seed
        );

        if (!is_null($alias)) {
            $body_arr['alias'] = $alias;
        }

        $body = json_encode($body_arr);

        try {
            $response = $this->jsonResponse($this->adminRestClient->post('/api/hardware', $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return HardwareToken::fromObject($response);
    }

    public function updateHardwareToken($hardware_token_id, $alias) {
        $body = json_encode(array(
                'alias' => $alias
        ));

        try {
            $response = $this->jsonResponse($this->adminRestClient->put('/api/hardware/' . $hardware_token_id, $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }

        return HardwareToken::fromObject($response);
    }

    public function deleteHardwareToken($hardware_token_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/hardware/' . $hardware_token_id));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    public function associateHardwareToken($user_id, $hardware_token_id) {
        $body = '';

        try {
            $response = $this->jsonResponse($this->adminRestClient->put('/api/users/' . $user_id . '/hardware/' . $hardware_token_id, $body));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    public function disassociateHardwareToken($user_id) {
        try {
            $response = $this->jsonResponse($this->adminRestClient->delete('/api/users/' . $user_id . '/hardware'));
        } catch (Exception $e) {
            throw $this->createException($e);
        }
    }

    protected function jsonResponse($server_output) {
        $response = json_decode($server_output);

        return $response;
    }

    private function createException($exception) {
        if (is_a($exception, 'RestAdminRestClientException')) {
            $response = json_decode($exception->getBody());

            if (isset($response->errors) != null && count($response->errors) > 0) {
                $error = $response->errors[0];
                return new ApiLoginTCException($error->code, $error->message);
            }
        }
        return new LoginTCException($exception->getMessage());
    }
}
