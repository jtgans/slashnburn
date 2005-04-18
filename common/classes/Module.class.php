<?PHP
require_once("jtlib/Dolphin.class.php");
require_once("jtlib/ErrorHandler.class.php");

define("MODULE_SUCCESS",  0);
define("MODULE_ERROR",    1);
define("MODULE_NOTFOUND", 2);

class Module
{
    var $_db;
    var $_smarty;
    var $_config;
    var $_nav;
    var $_livenav;
    
    function initModule()
    {
        $this->_db        = &Singleton::getInstance("Dolphin");
        $this->_smarty    = &Singleton::getInstance("Smarty");
        $this->_config    = &Singleton::getInstance("Config");
        $this->_nav       = &Singleton::getInstance("Navigation");
        
        $this->_livenav = new Navigation();
        $this->_livenav->loadNav();

        $this->_actions = array();
    }

    function handlePage()
    {
        return array(MODULE_ERROR, "Module::handlePage: Default handlePage method called.");
    }
}
