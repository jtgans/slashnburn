<?PHP
require_once("classes/Module.class.php");

class PagesAdmin extends Module
{
    function PagesAdmin()
    {
        $this->initModule();

        $this->_nav->appendSubNav("manage", "Custom Pages", 'pages');
        $this->_nav->appendSubNav("config", "Custom Pages", 'pages');
    }

    function _getPages()
    {
        $query = "SELECT * FROM pages ORDER BY title";

        if (!$this->_db->query($query)) {
            trigger_error("PagesAdmin::_getAllPages: Unable to query database.", ERR_ERROR);
            return false;
        }

        return $this->_db->getResultsArray();
    }

    function _getCategories()
    {
        $query = "SELECT * FROM categories ORDER BY name";

        if (!$this->_db->query($query)) {
            trigger_error("PagesAdmin::_getCategories: Unable to query database.", ERR_ERROR);
            return false;
        }

        return $this->_db->getResultsArray();
    }
         
    function handlePage($args)
    {
        switch ($args[0]) {
            case "manage":
                $this->_smarty->assign("pages", $this->_getPages());
                $this->_smarty->assign("categories", $this->_getCategories());
                $this->_smarty->assign_by_ref("livenav", $this->_livenav);
                $this->_smarty->display("admin-pages.tmpl");
                break;

            case "config":
                $this->_smarty->display("admin-pages-config.tmpl");
                break;

            default:
                return array(MODULE_NOTFOUND, "PagesAdmin::handlePage: Unknown mode $args[0].");
                break;
        }
    }
}
