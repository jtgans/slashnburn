<?PHP
require_once("jtlib/XMLRPC.class.php");

class BloggerAPI extends XMLRPC
{
    function BloggerAPI()
    {
        $this->_methods = array("blogger.getUsersBlogs" => array(&$this, "_getUsersBlogs"),
                                "blogger.newPost" => array(&$this, "_newPost")
                                );
        
        $this->XMLRPC();
        $this->_handleRequest();
    }

    function _newPost($method, $params)
    {
        global $_CONFIG;
        global $_DB;

        // 0 -> appkey
        // 1 -> blogid
        // 2 -> username
        // 3 -> password
        // 4 -> content
        // 5 -> publish

        $fp = fopen($_CONFIG['global']['base_dir'] ."/output.txt", "w");
        fputs($fp, "Supplied information:\n");
        ob_start();
        print_r($params);
        fputs($fp, ob_get_clean());
        fputs($fp, "Looking for [". $_CONFIG['global']['username'] ."] [". $_CONFIG['global']['password'] ."]\n");
        fclose($fp);

        if (($params[2] == $_CONFIG['global']['username']) &&
            ($params[3] == $_CONFIG['global']['password'])) {
            $content = html_entity_decode($params[4]);
            $title = substr($params[4], 0, strpos($params[4], "<p>"));
            $body = substr($params[4], strpos($params[4], "<p>"));

            $query  = "INSERT INTO blog (timestamp, title, body) ";
            $query .= "VALUES (";
            $query .= time() .", ";
            $query .= "'". addslashes($title) ."', ";
            $query .= "'". addslashes($body) ."'";
            $query .= ")";
            $_DB->query($query);

            return true;
        } else {
            return false;
        }
    }
    
    function _getUsersBlogs($method, $params)
    {
        global $_CONFIG;
        
        if (($params[1] == $_CONFIG['global']['username']) &&
            ($params[2] == $_CONFIG['global']['password'])) {
            $response = array(array("url"      => $_CONFIG['global']['base_url'],
                                    "blogid"   => "1",
                                    "blogName" => $_CONFIG['global']['site_title']));
            
        } else {
            $response = array("faultCode" => 4,
                              "faultString" => "User authentication failed: ". $params[1]);
        }
        
        return $response;
    }
}
