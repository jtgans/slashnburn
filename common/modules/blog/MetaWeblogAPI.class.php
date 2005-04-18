<?PHP
require_once("jtlib/XMLRPC.class.php");

class MetaWeblogAPI extends XMLRPC
{
    function MetaWeblogAPI()
    {
        $this->_methods = array("blogger.getUsersBlogs" => array(&$this, "_getUsersBlogs"),
                                "blogger.newPost"       => array(&$this, "_newPost"),
                                
                                "metaWeblog.newPost"    => array(&$this, "_newPost"),
                                "metaWeblog.editPost"   => array(&$this, "_editPost"),
                                "metaWeblog.getPost"    => array(&$this, "_getPost"),

                                "metaWeblog.newMediaObject" => array(&$this, "_newMediaObject"),
                                "metaWeblog.getCategories"  => array(&$this, "_getCategories"),
                                "metaWeblog.getRecentPosts" => array(&$this, "_getRecentPosts")
                                );

        $this->_db     = &Singleton::getInstance("Dolphin");
        $this->_config = &Singleton::getInstance("Config");

        if ((!$this->_db) || (!$this->_config)) {
            if (!$this->_db)     trigger_error("MetaWeblogAPI::MetaWeblogAPI: Unable to get an instance of Dolphin.", ERR_ERROR);
            if (!$this->_config) trigger_error("MetaWeblogAPI::MetaWeblogAPI: Unable to get an instance of Config.", ERR_ERROR);
            return;
        }
        
        $this->XMLRPC();
        $this->_handleRequest();
    }

    function _isValidAuth($user, $pass)
    {
        if (($user == $this->_config->getKey('admin/username')) &&
            ($pass == $this->_config->getKey('admin/password'))) {
            return true;
        } else {
            return false;
        }
    }

    function _newPost($method, $params)
    {
        // BloggerAPI      MetaWeblogAPI
        // 0 -> appkey     0 -> blogid
        // 1 -> blogid     1 -> username
        // 2 -> username   2 -> password
        // 3 -> password   3 -> content
        // 4 -> content    4 -> publish
        // 5 -> publish

        $fp = fopen("output.txt", "w");
        ob_start();
        print_r($params);
        fputs($fp, ob_get_clean());
        
        switch ($method) {
            case "blogger.newPost":
                fputs($fp, "blogger.newPost called.\n");
                if ($this->_isValidAuth($params[2], $params[3])) {
                    fputs($fp, "Good auth.\n");
                    $content = html_entity_decode($params[4]);
                    $title = substr($content, 0, strpos($content, "<p>"));
                    $body = substr($content, strpos($content, "<p>"));
                } else {
                    fputs($fp, "Invalid auth.\n");
                    fclose($fp);
                    return false;
                }
                break;

            case "metaWeblog.newPost":
                fputs($fp, "metaWeblog.newPost called.\n");
                if ($this->_isValidAuth($params[1], $params[2])) {
                    fputs($fp, "Good auth.\n");
                    $body  = html_entity_decode($params[3]['description']);
                    $title = html_entity_decode($params[3]['title']);
                } else {
                    fputs($fp, "Invalid auth.\n");
                    fclose($fp);
                    return false;
                }
                break;

            default:
                fputs($fp, "Unknown method [$method] called.\n");
                fclose($fp);
                return false;
        }

        $query  = "INSERT INTO blog (timestamp, title, body) ";
        $query .= "VALUES (";
        $query .= time() .", ";
        $query .= "'". addslashes($title) ."', ";
        $query .= "'". addslashes($body) ."'";
        $query .= ")";
            
        if (!$this->_db->query($query)) {
            fputs($fp, "Unable to insert row into database.");
            fclose($fp);
            trigger_error("MetaWeblogAPI::_newPost: Unable to insert row into database.", ERR_ERROR);
            return false;
        }

        fputs($fp, "Post successful.");
        fclose($fp);
        return true;
    }
    
    function _getUsersBlogs($method, $params)
    {
        if (($params[1] == $this->_config->getKey('admin/username')) &&
            ($params[2] == $this->_config->getKey('admin/password'))) {
            $response = array(array("url"      => $this->_config->getKey('global/base_url'),
                                    "blogid"   => "1",
                                    "blogName" => $this->_config->getKey('global/site_title')));
            
        } else {
            $response = array("faultCode" => 4,
                              "faultString" => "User authentication failed: ". $params[1]);
        }
        
        return $response;
    }
}
