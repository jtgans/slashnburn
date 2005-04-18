<?PHP
require_once("classes/Module.class.php");
require_once("jtlib/DataSanitizer.class.php");
require_once("MetaWeblogAPI.class.php");

class Blog extends Module
{
    function Blog()
    {
        $this->initModule();
        $this->_nav->appendSubNav("blog", 'Archives', 'archives');
        $this->_nav->appendSubNav("blog", 'RSS Feed', 'rss');
        $this->_nav->appendSubNav("blog", 'ATOM Feed', 'atom');
    }

    function getEntries($num = false)
    {
        $query = "SELECT * FROM blog ORDER BY timestamp DESC";
        if ($num !== false) {
            $query .= " LIMIT $num";
        }

        if (!$this->_db->query($query)) {
            trigger_error("Blog::_getEntries: Unable to query database.", ERR_ERROR);
            return false;
        }

        if ($this->_db->getNumRows()) {
            foreach ($this->_db->getResultsArray() AS $key => $row) {
                $row['comments'] = $this->_getComments($row['blog_id']);
                $row['comment_count'] = count($row['comments']);
                $results[] = $row;
            }
        } else {
            trigger_error("Blog::_getEntries: No blog entries to display.", ERR_WARNING);
            $results = array();
        }

        return $results;
    }

    function getEntryByID($id)
    {
        $query = "SELECT * FROM blog WHERE blog_id=$id";
        if (!$this->_db->query($query)) {
            trigger_error("Blog::_getEntryByID: Unable to query database.", ERR_ERROR);
            return false;
        }

        $entry = $this->_db->getResultsRow();
        $entry['comments']      = $this->_getComments($id);
        $entry['comment_count'] = count($entry['comments']);
        
        return $entry;
    }
    
    function _getComments($id)
    {
        $query = "SELECT * FROM blog_comments WHERE blog_id=$id";
        if (!$this->_db->query($query)) {
            trigger_error("Blog::_getEntryByID: Unable to query database.", ERR_ERROR);
            return false;
        }

        if ($this->_db->getNumRows() > 0) {
            foreach ($this->_db->getResultsArray() AS $key => $val) {
                // $val['body'] = preg_replace(
                $comments[] = $val;
            }
        } else {
            $comments = array();
        }

        return $comments;
    }        

    function addEntry($title, $body, $summary, $allow_comments)
    {
        if (trim(striptags($summary)) == "") {
            $summary = "NULL";
        } else {
            $summary = "'". addslashes($summary) ."'";
        }
        
        $query  = "INSERT INTO blog VALUES (";
        $query .= "NULL, ";
        $query .= time() .", ";
        $query .= $allow_comments .", ";
        $query .= "'". addslashes($title) ."', ";
        $query .= "'". addslashes($body) ."'";
        $query .= "$summary";
        $query .= ")";

        if (!$this->_db->query($query)) {
            trigger_error("BlogAdmin::_addEntry: Unable to execute query.", ERR_ERROR);
            return false;
        } else {
            $query = "SELECT LAST_INSERT_ID() AS id";

            if (!$this->_db->query($query)) {
                trigger_error("BlogAdmin::_addEntry: Unable to execute query.", ERR_ERROR);
                return array(MODULE_ERROR, "Blog::addEntry: Unable to add entry to database.");
            } else {
                return $this->_db->getResultsValue("id");
            }
        }
    }

    function updateEntry($id, $title, $body, $summary, $allow_comments)
    {
        if (trim(striptags($summary)) == "") {
            $summary = "NULL";
        } else {
            $summary = "'". addslashes($summary) ."'";
        }
        
        $query  = "UPDATE blog SET ";
        $query .= "timestamp=". time() .", ";
        $query .= "allow_comments=". $allow_comments .", ";
        $query .= "title='". addslashes($title) ."', ";
        $query .= "body='". addslashes($body) ."' ";
        $query .= "summary=$summary ";
        $query .= "WHERE blog_id=". $id;

        if (!$this->_db->query($query)) {
            trigger_error("BlogAdmin::_updateEntry: Unable to execute query.", ERR_ERROR);
            return array(MODULE_ERROR, "Blog::updateEntry: Unable to update entry $id.");
        } else {
            return array(MODULE_SUCCESS, "Success");
        }
    }

    function deleteEntry($id)
    {
        $query = "DELETE FROM blog WHERE blog_id=$id";

        if (!$this->_db->query($query)) {
            trigger_error("BlogAdmin::_deleteEntry: Unable to execute query.", ERR_ERROR);
            return array(MODULE_ERROR, "Blog::deleteEntry: Unable to delete entry $id from database.");
        } else {
            return array(MODULE_SUCCESS, "Success");
        }
    }
    
    function _displaySummary()
    {
        $entries = $this->getEntries($this->_config->getKey("blog/display"));
        $this->_smarty->assign("entries", $entries);
        $this->_smarty->display("blog-summary.tmpl");
        
        return array(MODULE_SUCCESS, "Success");
    }

    function _displayArchives()
    {
        $this->_smarty->assign("entries", $this->getEntries());
        $this->_smarty->display("blog-archives.tmpl");
        
        return array(MODULE_SUCCESS, "Success");
    }

    function _displayEntry($id)
    {
        $entry = $this->getEntryByID($id);
        $this->_smarty->assign("entry", $entry);
        $this->_smarty->display("blog-entry.tmpl");
        return array(MODULE_SUCCESS, "Success");
    }

    function _displayFeed($type)
    {
        $entries = $this->getEntries($this->_config->getKey('blog/display'));
        
        foreach ($entries AS $key => $row) {
            $entries[$key]['body'] = htmlentities($row['body']);
            $entries[$key]['timestamp'] = Utils::iso8601_date($row['timestamp']);
        }

        header("Content-Type: text/xml");
        $this->_smarty->unregister_outputfilter("protect_email");
        $this->_smarty->assign("current_date", Utils::iso8601_date());
        $this->_smarty->assign("entries", $entries);

        switch ($type) {
            case "rss":
                $this->_smarty->display("blog-rss.tmpl");
                break;

            case "atom":
                $this->_smarty->display("blog-atom.tmpl");
                break;
        }
            
        return array(MODULE_SUCCESS, "Success");
    }

    function _postComment($id)
    {
        // TODO: Add form checking here!
        
        $entry = $this->getEntryByID($id);
        
        if ($entry['allow_comments']) {
            $title      = (string)($_POST['title']);
            $name       = (string)($_POST['name']);
            $email      = (string)($_POST['email']);
            $show_email = ($_POST['show_email'] == 1) ? 1 : 0;
            $body       = nl2br(strip_tags((string)($_POST['body'])));
            
            $query  = "INSERT INTO blog_comments (blog_id, timestamp, title, author, email, show_email, body) VALUES (";
            $query .= $id .", ";
            $query .= time() .", ";
            $query .= "'". addslashes($title) ."', ";
            $query .= "'". addslashes($name) ."', ";
            $query .= "'". addslashes($email) ."', ";
            $query .= $show_email .", ";
            $query .= "'". addslashes($body) ."'";
            $query .= ")";
            
            if (!$this->_db->query($query)) {
                trigger_error("Blog::_postComment: Unable to insert row into database.", ERR_ERROR);
                return array(MODULE_ERROR, "Blog::_postComment: Unable to insert row into database.");
            }
            
            return array(MODULE_SUCCESS, "Success");
        } else {
            return array(MODULE_ERROR, "Blog::_postComment: Comments are disabled for this blog entry.");
        }
    }

    function deleteComment($id)
    {
        $query = "DELETE FROM blog_comments WHERE comment_id=$id";

        if (!$this->_db->query($query)) {
            trigger_error("Blog::deleteComment: Unable to delete comment from database.", ERR_ERROR);
            return array(MODULE_ERROR, "Blog::deleteComment: Unable to delete comment from database.");
        } else {
            return array(MODULE_SUCCESS, "Success");
        }
    }
    
    function handlePage($args)
    {
        if (!isset($args[0])) {
            return $this->_displaySummary();
        } else {
            switch ($args[0]) {
                case "archives":
                    return $this->_displayArchives();

                case "rss":
                    return $this->_displayFeed("rss");

                case "atom":
                    return $this->_displayFeed("atom");

                case "api":
                    $mwapi = new MetaWeblogAPI();
                    return array(MODULE_SUCCESS, "Success");
                    
                default:
                    if (isset($_POST['submit'])) {
                        $retval = $this->_postComment((integer)($args[0]));
                        if ($retval[0] != MODULE_SUCCESS) return $retval;
                    }
                    
                    return $this->_displayEntry((integer)($args[0]));
                    break;
            }
        }

        return array(MODULE_NOTFOUND, "404 Not Found");
    }
}
