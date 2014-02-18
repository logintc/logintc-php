<?php

/**
 * Exception thrown out of AdminClient.
 */
class AdminRestClientException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }
}

/**
 * Exception caused by internal non-REST related exception.
 */
class InternalAdminRestClientException extends AdminRestClientException {

    public function __construct($message) {
        parent::__construct($message);
    }
}

/**
 * Exception thrown out of AdminClient.
 */
class RestAdminRestClientException extends AdminRestClientException {

    private $status_code;
    private $body;

    public function __construct($status_code, $body) {
        $this->status_code = $status_code;
        $this->body = $body;

        parent::__construct('API returned status code ' . $this->status_code);
    }

    public function getStatusCode() {
        return $this->status_code;
    }

    public function getBody() {
        return $this->body;
    }

}

/**
 * HTTP REST client for LoginTC Admin.
 */
class AdminRestClient {

    const CONTENT_TYPE = 'application/vnd.logintc.v1+json';

    private $url;
    private $api_key;
    private $user_agent;

    public function __construct($url, $api_key, $user_agent) {
        $this->url = $url;
        $this->api_key = $api_key;
        $this->user_agent = $user_agent;
    }

    public function get($path) {
        $get_url = $this->url . $path;

        $ch = curl_init();

        return $this->execute_curl($this->url . $path, $ch);

    }

    public function post($path, $body) {
        $post_url = $this->url . $path;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_POST, 1);

        if (!is_null($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        return $this->execute_curl($post_url, $ch);
    }

    public function put($path, $body) {
        $put_url = $this->url . $path;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        if (!is_null($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        return $this->execute_curl($put_url, $ch);
    }

    public function delete($path) {
        $delete_url = $this->url . $path;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->execute_curl($delete_url, $ch);
    }

    private function execute_curl($url, $ch) {
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $http_headers = array();
        array_push($http_headers, 'User-Agent: ' . $this->user_agent);
        array_push($http_headers, 'Authorization: LoginTC key="' . $this->api_key . '"');

        if (!is_null(curl_getinfo($ch, CURLOPT_POSTFIELDS))) {
            array_push($http_headers, 'Content-Type: ' . self::CONTENT_TYPE);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);

        // receive server response ...
        $server_output = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // error happened with curl
        if (empty($http_code)) {
            $curl_error = curl_error($ch);
            curl_close($ch);

            throw new InternalAdminRestClientException($curl_error);
        }

        curl_close($ch);

        switch ($http_code) {
            case 200: // OK
            case 201: // Created
            case 202: // Accepted
                break;
            case 400: // Bad Request
            case 401: // Unauthorized
            case 403: // Forbidden
            case 404: // Not Found
            case 405: // Method Not Allowed
            case 406: // Not Acceptable
            case 410: // Gone
            case 429: // Too Many Requests
            case 500: // Internal Server Error
            case 501: // Not Implemented
            case 502: // Bad Gateway
            case 503: // Service Unavailable
            case 504: // Gateway Timeout
            default:
                throw new RestAdminRestClientException($http_code, $server_output);
        }

        return $server_output;
    }

}
