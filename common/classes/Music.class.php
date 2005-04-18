<?PHP

require_once("DataObject.class.php");

class Music extends DataObject
{
    var $root;
    
    function Music($root = "/var/www/theonelab/files/music/")
    {
        $this->fields = array("music_id"    => "post,get,integer",
                              "title"       => "post,string",
                              "genre"       => "post,string",
                              "description" => "post,string",
                              "lyrics"      => "post,string",
                              "filename"    => "post,string",
                              "filetype"    => "post,string",
                              "timestamp"   => "post,integer",
                              "file"        => "file");
        $this->required = array("music_id", "title", "genre", "description", "filetype", "file");
        $this->table = "music";

        $this->root = $root;
    
        parent::DataObject();
    }

    function insertIntoDB()
    {
        $this->handleUpload();
      
        $query  = "INSERT INTO music VALUES (";
        $query .= "NULL, ";
        $query .= "'". $this->values['title'] ."', ";
        $query .= "'". $this->values['genre'] ."', ";
        $query .= "'". $this->values['description'] ."', ";
        $query .= "'". $this->values['lyrics'] ."', ";
        $query .= "'". $this->values['file']['name'] ."', ";
        $query .= "'". $this->values['filetype'] ."', ";
        $query .= time() .")";

        if (!$this->db->query($query)) {
            Utils::errorMsg("Music", __LINE__, "Unable to insert data into DB! ($query)");
            exit;
        }
    }
    
    function updateDB()
    {
        $this->handleUpload();
    
        $query  = "UPDATE music SET ";
        $query .= "title = '". $this->values['title'] ."', ";
        $query .= "genre = '". $this->values['genre'] ."', ";
        $query .= "description = '". $this->values['description'] ."', ";
        $query .= "lyrics = '". $this->values['lyrics'] ."', ";
        $query .= "filetype = '". $this->values['filetype'] ."', ";
        $query .= "timestamp = ". time() ." ";
        $query .= "WHERE music_id = ".$this->values['music_id'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("Music", __LINE__, "Unable to update data in DB! ($query)");
            exit;
        }
    }

    function handleUpload()
    {
        if (!move_uploaded_file($this->values['file']['tmp_name'], $this->root . $this->values['file']['name'])) {
            Utils::errorMsg("Music", __LINE__, "Whoa! move_uploaded_file() returned false! Giving up!");
            exit;
        }
    }
    
    function loadFromDB()
    {
        $query = "SELECT * FROM music WHERE music_id = ".$this->values['music_id'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("Music", __LINE__, "Unable to select data from DB! ($query)");
            exit;
        }
    
        $this->values = $this->db->getResultsRow();
    }
}
