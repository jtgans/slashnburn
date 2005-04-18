<?PHP
require_once("classes/Module.class.php");

class pages extends Module
{
    function pages()
    {
        $this->initModule();
    }

    function handlePage($args)
    {
        $query  = "SELECT pages.*, categories.name AS category ";
        $query .= "FROM pages, categories ";
        
        if (!isset($args[0])) {
            // We don't have a page to load
            $query .= "WHERE pages.page_id=". $this->_config->getKey("pages/default_page") ." ";
        } else {
            $query .= "WHERE page_id=". (integer)($args[0]) ." ";
        }
        
        $query .= "AND pages.category_id=categories.category_id";
        
        if (!$this->_db->query($query)) {
            trigger_error("pages::handlePage: Unable to query database for pages.", ERR_ERROR);
            return false;
        } else {
            $this->_smarty->assign("page", $this->_db->getResultsRow());
            $this->_smarty->display("pages.tmpl");
            return true;
        }
    }
}