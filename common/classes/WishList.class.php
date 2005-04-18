<?PHP

require_once("DataObject.class.php");

class WishList extends DataObject
{
    function WishList()
    {
        $this->fields = array("description"     => "post,string",
                              "type"                => "post,string",
                              "cost"            => "post,string",
                              "foundat"         => "post,string",
                              "priority"        => "post,number",
                              "wishlist_id"     => "post,number"
                              );
        $this->required = array("description", "type", "cost", "foundat", "priority", "wishlist_id");
        $this->table = "wishlist";
    
        parent::DataObject();
    }
    
    function insertIntoDB()
    {
        $query  = "INSERT INTO wishlist VALUES (";
        $query .= $this->values['priority'] .", ";
        $query .= "'". $this->values['description'] ."', ";
        $query .= "'". $this->values['cost'] ."', ";
        $query .= "'". $this->values['foundat'] ."', ";
        $query .= "NULL, ";
        $query .= "'". $this->values['type'] ."'";
        $query .= ")";
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("WishList", __LINE__, "Unable to insert data into DB! ($query)");
            exit;
        }
    }
    
    function updateDB()
    {
        $query  = "UPDATE wishlist SET ";
        $query .= "priority = ".$this->values['priority'].", ";
        $query .= "description = '".$this->values['description']."', ";
        $query .= "cost = '".$this->values['cost']."', ";
        $query .= "foundat = '".$this->values['foundat']."', ";
        $query .= "type = '".$this->values['types']."' ";
        $query .= "WHERE wishlist_id = ".$this->values['wishlist_id'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("WishList", __LINE__, "Unable to update data in DB! ($query)");
            exit;
        }
    }
    
    function loadFromDB()
    {
        $query = "SELECT * FROM wishlist WHERE wishlist_id = ".$this->values['wishlist_id'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("WishList", __LINE__, "Unable to select data from DB! ($query)");
            exit;
        }
    
        $this->values = $this->db->getResultsRow();
        if (!$this->values) {
            Utils::errorMsg("WishList", __LINE__, "No such row with ID ".$this->values['wishlist_id']);
            exit;
        }
    }
    
    function deleteFromDB()
    {
        $query = "DELETE FROM wishlist WHERE wishlist_id = ".$this->values['wishlist_id'];
    
        if (!$this->db->query($query)) {
            Utils::errorMsg("WishList", __LINE__, "Unable to delete from DB! ($query)");
            exit;
        }
    }
}
