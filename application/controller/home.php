<?php

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 */
class Home extends Controller
{
    // environment variables
    private $env;

    /**
     * PAGE: index
     */
    public function index()
    {
        $path = str_replace("application\controller", "", __DIR__).".env";
        $envFile = fopen($path, "r") or die("Unable to open file!");
        $this->env = json_decode(fread($envFile,filesize($path)));
        fclose($envFile);

        define('OAUTH2_CLIENT_ID', $this->env->clientID);
        define('OAUTH2_CLIENT_SECRET', $this->env->clientSecret);

        $authorizeURL = 'https://github.com/login/oauth/authorize';
        $tokenURL = 'https://github.com/login/oauth/access_token';
        $apiURLBase = 'https://api.github.com/';

        session_start();

        // Start the login process by sending the user to Github's authorization page
        if(isset($_GET['action']) && $_GET['action'] == 'login') {
            // Generate a random hash and store in the session for security
            $_SESSION['state'] = hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
            unset($_SESSION['access_token']);

            $params = array(
                'client_id' => OAUTH2_CLIENT_ID,
                'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
                'scope' => 'user',
                'state' => $_SESSION['state']
            );

            // Redirect the user to Github's authorization page
            header('Location: ' . $authorizeURL . '?' . http_build_query($params));
            die();
        }

        // When Github redirects the user back here, there will be a "code" and "state" parameter in the query string
        if(isset($_GET['code'])) {
            // Verify the state matches our stored state
            if(!$_GET['state'] || $_SESSION['state'] != $_GET['state']) {
                header('Location: ' . $_SERVER['PHP_SELF']);
                die();
            }

            // Exchange the auth code for a token
            $token = apiRequest($tokenURL, array(
                'client_id' => OAUTH2_CLIENT_ID,
                'client_secret' => OAUTH2_CLIENT_SECRET,
                'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
                'state' => $_SESSION['state'],
                'code' => $_GET['code']
            ));
            $_SESSION['access_token'] = $token->access_token;

            // header('Location: ' . $_SERVER['PHP_SELF']);
            // load views
        }

        if($this->session('access_token')) {
            $user = apiRequest($apiURLBase . 'user');
            require APP . 'view/_templates/header.php';
            require APP . 'view/home/index.php';
            require APP . 'view/_templates/footer.php';
        } else {
            echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">';
            echo '<div class="container"><h1>Not logged in</h1>';
            echo '<p><a href="?action=login" class="btn btn-info">Log In</a></p></div></body></html>';
        }
    }

    function apiRequest($url, $post=FALSE, $headers=array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      
        if($post)
          curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
      
        $headers[] = 'Accept: application/json';
      
        if($this->session('access_token'))
          $headers[] = 'Authorization: Bearer ' . $this->session('access_token');
      
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      
        $response = curl_exec($ch);
        return json_decode($response);
    }
    
    function session($key, $default=NULL) {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }
}
