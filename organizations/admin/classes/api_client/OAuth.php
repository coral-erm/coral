<?php

require '../resources/api_client/vendor/autoload.php';

class OAuth {

    function __construct() {
        $config = new Configuration();
        $this->clientid = $config->ils->oauthid;
        $this->clientsecret = $config->ils->oauthsecret;
		$this->tokenURL = $config->ils->ilsApiUrl . "/oauth/token";
		$this->authURL = $config->ils->ilsApiUrl . "/oauth/authorize";
    }

    function getToken() {
		$provider = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId'                => $this->clientid,
			'clientSecret'            => $this->clientsecret,
			'urlAuthorize'            => $this->authURL,
			'urlAccessToken'          => $this->tokenURL,
			'urlResourceOwnerDetails' => ''
		]);
        if (!array_key_exists("oauthToken", $_SESSION)) {
            try {

                // Try to get an access token using the client credentials grant.
                $accessToken = $provider->getAccessToken('client_credentials');

            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

                // Failed to get the access token
                exit($e->getMessage());

            }
        } else {
            $existingAccessToken = unserialize($_SESSION['oauthToken']);
            if ($existingAccessToken->hasExpired()) {
                $refresh_token = $existingAccessToken->getRefreshToken();
                if ($refresh_token != null) {
                    $newAccessToken = $provider->getAccessToken('refresh_token', [
                        'refresh_token' => $refresh_token
                    ]);
                } else {
                    $newAccessToken = $provider->getAccessToken('client_credentials');
                }
                $accessToken = $newAccessToken;
            } else {
                $accessToken = $existingAccessToken;
            }
        }
        $_SESSION['oauthToken'] = serialize($accessToken);
		return $accessToken;
    }

}

?>

