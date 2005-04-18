<?PHP

require_once("DataObject.class.php");

class KnowledgeBase extends DataObject
{
    function KnowledgeBase()
    {
        $this->fields = array("query" => "post,string",
                              "title" => "post,string",
                              "body"  => "post,string",
                              "kbid"  => "post,string");
        $this->required = array();
        $this->table = "knowledgebase";
    
        parent::DataObject();
    }

    function handleMode()
    {
        parent::handleMode();

        if ($this->mode == "search") {
            $this->searchKB();
        }
    }
    
    function searchKB()
    {
        $query  = "SELECT kbid, title, body, MATCH(title,body) ";
        $query .= "AGAINST ('". $this->values['query'] ."') as relevance ";
        $query .= "FROM knowledgebase ";
        $query .= "WHERE MATCH(title,body) AGAINST ('". $this->values['query'] ."') ";

        //  $query  = "SELECT kbid FROM knowledgebase WHERE body LIKE '%". $this->values['query'] ."%'";
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("KnowledgeBase", __LINE__, "Unable to search DB! ($query)");
            exit;
        }
    
        $this->searchResults = $this->db->getResultsArray();
        if (!is_array($this->searchResults)) {
            // MATCH() returned junk for results, so let's do our own keyword search (UGH!)
            $query  = "SELECT kbid, title, body, -1 AS relevance ";
            $query .= "FROM knowledgebase ";
            $query .= "WHERE ";
        
            $and = "";
            $tmp = explode(" ", $this->values['query']);
            foreach ($tmp AS $val) {
                $query .= "$and body LIKE '%". $val ."%' OR title LIKE '%". $val ."%' ";
            }
        
            if (!$this->db->query($query)) {
                Utils::errorMsg("KnowledgeBase", __LINE__, "Unable to search DB! ($query)");
                exit;
            }
        
            $this->searchResults = $this->db->getResultsArray();
        }
    }
    
    function insertIntoDB()
    {
        $query  = "INSERT INTO knowledgebase VALUES (";
        $query .= "NULL, ";
        $query .= time() .", ";
        $query .= "'". $this->values['title']   ."', ";
        $query .= "'". $this->values['body']    ."')";
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("KnowledgeBase", __LINE__, "Unable to insert data into DB! ($query)");
            exit;
        }
    }

    function updateDB()
    {
        $query  = "UPDATE knowledgebase SET ";
        $query .= "timestamp=". time() .", ";
        $query .= "title='". $this->values['title'] ."', ";
        $query .= "body='". $this->values['body'] ."' ";
        $query .= "WHERE kbid=". $this->values['kbid'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("KnowledgeBase", __LINE__, "Unable to update DB! ($query)");
            exit;
        }
    }
    
    function loadFromDB()
    {
        $query = "SELECT * FROM knowledgebase WHERE kbid=". $this->values['kbid'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("KnowledgeBase", __LINE__, "Unable to select from DB! ($query)");
            exit;
        }
    
        $this->values = $this->db->getResultsRow();
    
        if (!$this->values) {
            Utils::errorMsg("KnowledgeBase", __LINE__, "No such row with id ". $this->values['kbid']);
            exit;
        }
    }

    function deleteFromDB()
    {
        $query = "DELETE FROM knowledgebase WHERE kbid=". $this->values['kbid'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("KnowledgeBase", __LINE__, "Unable to delete from DB! ($query)");
            exit;
        }
    }
}
