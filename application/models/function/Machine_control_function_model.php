<?php 
class Machine_control_function_model extends CI_Model {

    /*
        机器控制
    */

    private $app_name = 'TestApp';
    private $oauth2_client_id = 'ClkcPLZ3Tf7ncvlOlmmHHQ6cNtnazv67XDXCAWEr';
    private $oauth2_client_secret = 'l5kNwFrmUGTdNob076jgMKewDRKVGDakajIHY08otZFTIbFQNXQ5hMGFt99c7jhcrxo4Lmqx18Vce85xoM5yKR6SAf1nfS1QN8eQ7Qdy43QYQCCQLIXW9AZDU89v5cMf';

    //
    // APIs online documents
    // -----------------------------------------------
    // https://node.transock.com/api/v2/
    // -----------------------------------------------

    //
    // OAuth2 APIs
    // The special OAuth 2 endpoints only support using the x-www-form-urlencoded Content-type,
    // so as a result, none of the /api/o/* endpoints accept application/json.
    private $authorizeURL = 'https://node.transock.com/api/o/authorize/';
    private $tokenURL = 'https://node.transock.com/api/o/token/';
    private $revokeURL = 'https://node.transock.com/api/o/revoke_token/';

    // APIs tryouts
    //
    // list job templates
    // curl -s -k -u 'user:passwd' https://node.transock.com/api/v2/job_templates/ | jq '.results | .[] | .name '
    //
    private $user_apiURLBase = 'https://node.transock.com/api/v2/users/?format=json';
    private $app_apiURLBase = 'https://node.transock.com/api/v2/applications/?format=json';
    private $me_apiURLBase = 'https://node.transock.com/api/v2/me/?format=json';


    public function __construct()
    {
        function apiRequest($url, $post = FALSE, $headers = array()) {
            $ch = curl_init($url);

            // FIXME: We use a self signatured SSL cert, so we MUST ignore the SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            if($post)
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            $headers[] = 'Accept: application/json';
            if(session('access_token'))
                $headers[] = 'Authorization: Bearer ' . session('access_token');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response);
        }

        function revokeRequest($url, $username, $password, $access_token) {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $data = array('token' => "$access_token");
            $post = http_build_query($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_HEADER , TRUE);
            //curl_setopt($ch, CURLOPT_NOBODY, TRUE);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $httpcode;
        }

        function get($key, $default=NULL) {
            return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
        }

        function session($key, $default=NULL) {
            return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
        }
    }

    public function ping(){
        // Start the login process by sending the user to Github's authorization page
        if(get('action') == 'login') {
            // Generate a random hash and store in the session for security
            $_SESSION['state'] = hash('sha256', microtime(TRUE).rand().$_SERVER['REMOTE_ADDR']);
            unset($_SESSION['access_token']);
            $params = array(
                'client_id' => $oauth2_client_id,
                'redirect_uri' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'],
                'response_type' => 'token',
                'scope' => 'read write',
                'state' => $_SESSION['state']
            );
            // Redirect the user to Ansible Tower authorization page
            header('Location: ' . $authorizeURL . '?' . http_build_query($params));
            die();
        } else if (get('action') == 'logout') {
            unset($_SESSION['access_token']);
            unset($_SESSION['state']);
        } else if (get('action') == 'revoke' && session('access_token')) {
            $access_token = $_SESSION['access_token'];
            $username = $oauth2_client_id;
            $password = $oauth2_client_secret;
            $result = revokeRequest($revokeURL, $username, $password, $access_token);
            if ($result == 200) {
                echo "<p>The token: <b>$access_token</b> has been revoked sucessfully.</p>";
                unset($_SESSION['access_token']);
                unset($_SESSION['state']);
            } else {
                echo "<p>Fail to revoke the token: <b>$access_token</b>, httpcode: $result </p>";
                die();
            }
        }
        return session_status();
    }




}
?>