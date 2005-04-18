<?PHP
require_once("jtlib/Dolphin.class.php");
require_once("jtlib/BaseClass.class.php");

class Articles extends BaseClass
{
    function Articles()
    {
        $this->_fields = array();

        parent::BaseClass();
    }

    function handleMode()
    {
        global $_ARGS;

        switch ($this->mode) {
        }

        $this->data['link'] = $_ARGS[1];
    }

    function getEntry($link)
    {
        $query  = "SELECT * FROM articles ";
        $query .= "WHERE articles.link='". $link ."'";
        
        $this->_db->query($query);

        if ($this->_db->getNumRows() > 0) {
            return $this->_db->getResultsRow();
        } else {
            return false;
        }
    }

    function getArticles()
    {
        $query  = "SELECT * FROM articles ";
        $query .= "ORDER BY title";
        $this->_db->query($query);

        if ($this->_db->getNumRows() > 0) {
            $results = $this->_db->getResultsArray();
        } else {
            $results = array();
        }

        return $results;
    }
}
