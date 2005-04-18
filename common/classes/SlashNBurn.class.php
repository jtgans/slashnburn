<?PHP
require_once("smarty/Smarty.class.php");
require_once("jtlib/ErrorHandler.class.php");
require_once("jtlib/Singleton.class.php");
require_once("jtlib/Dolphin.class.php");
require_once("classes/Config.class.php");
require_once("classes/Navigation.class.php");
require_once("classes/Request.class.php");
require_once("include/errors.php");

class SlashNBurn
{
    var $_db;
    var $_smarty;
    var $_config;
    var $_nav;
    var $_request;
    
    function SlashNBurn()
    {
        $this->_db      = &Singleton::getInstance("Dolphin");
        $this->_smarty  = &Singleton::getInstance("Smarty");
        $this->_config  = &Singleton::getInstance("Config");
        $this->_nav     = &Singleton::getInstance("Navigation");
        $this->_request = &Singleton::getInstance("Request");

        if ((!$this->_db) || (!$this->_smarty) || (!$this->_config)) {
            if (!$this->_db)      trigger_error("SlashNBurn::SlashNBurn: Unable to get instance of Dolphin.",    ERR_ERROR);
            if (!$this->_smarty)  trigger_error("SlashNBurn::SlashNBurn: Unable to get instance of Smarty.",     ERR_ERROR);
            if (!$this->_config)  trigger_error("SlashNBurn::SlashNBurn: Unable to get instance of Config.",     ERR_ERROR);
            if (!$this->_nav)     trigger_error("SlashNBurn::SlashNBurn: Unable to get instance of Navigation.", ERR_ERROR);
            if (!$this->_request) trigger_error("SlashNBurn::SlashNBurn: Unable to get instance of Request.",    ERR_ERROR);
            return false;
        }

        // Connect to the database
        if (!$this->_db->connect($this->_config->getKey('global/db_url'))) {
            trigger_error("SlashNBurn::SlashNBurn: Unable to connect to database.", ERR_ERROR);
            return false;
        }

        // Load our config
        $this->_config->loadConfig();

        // Parse our URL
        $this->_request->parseURL();
        
        // Setup smarty
        $this->_smarty->template_dir = "common/templates";
        $this->_smarty->compile_dir  = "common/compiled-tmpl";
        $this->_smarty->debugging = false;
        $this->_smarty->register_outputfilter("protect_email");

        // Load our base navigation array
        $this->_nav->loadNav();

        // Finally, load into smarty the variables we'll need in the templates
        $this->_smarty->assign_by_ref("config", $this->_config);
        $this->_smarty->assign_by_ref("nav", $this->_nav);

        // Run the dispatcher
        $this->_dispatch();
    }

    function _dispatch()
    {
        global $_ERRORS;

        $modules = $this->_config->getModules();
        
        if (!$modules) {
            header("Status: 500 Internal Server Error");
            header("HTTP/1.1 500 Internal Server Error");

            $this->_smarty->assign("error_code",    SNBERR_DATABASE);
            $this->_smarty->assign("error_message", $_ERRORS[SNBERR_DATABASE]);
            $this->_smarty->display("error.tmpl");
            return false;
        } else {
            $module = false;
            foreach ($modules AS $key => $val) {
                if ($val['name'] == $this->_request->getModule()) {
                    @require_once("modules/". $val['name'] ."/". $val['class'] .".class.php");

                    if (class_exists($val['class'])) {
                        $module = new $val['class']();
                        $result = $module->handlePage($this->_request->getAllArgs());

                        switch ($result[0]) {
                            case MODULE_SUCCESS:
                                return true;

                            case MODULE_NOTFOUND:
                                ob_clean();
                                header("Status: 404 Not Found");
                                header("HTTP/1.1 404 Not Found");
                                $this->_smarty->display("404.tmpl");
                                return false;

                            case MODULE_ERROR:
                            default:
                                ob_clean();
                                header("Status: 500 Internal Server Error");
                                header("HTTP/1.1 500 Internal Server Error");
                                $this->_smarty->assign("error_code", $result[0]);
                                $this->_smarty->assign("error_message", $result[1]);
                                $this->_smarty->display("error.tmpl");
                                return false;
                        }
                    } else {
                        header("Status: 500 Internal Server Error");
                        header("HTTP/1.1 500 Internal Server Error");

                        $this->_smarty->assign("error_code",    SNBERR_BADDEFAULTMOD);
                        $this->_smarty->assign("error_message", $_ERRORS[SNBERR_BADDEFAULTMOD]);
                        $this->_smarty->display("error.tmpl");
                        return false;
                    }
                }
            }

            if ($module == false) {
                header("Status: 404 Not Found");
                header("HTTP/1.1 404 Not Found");
                
                if ($this->_request->usingDefaultMod()) {
                    $this->_smarty->assign("error_code",    SNBERR_BADDEFAULTMOD);
                    $this->_smarty->assign("error_message", $_ERRORS[SNBERR_BADDEFAULTMOD]);
                    $this->_smarty->display("error.tmpl");
                } else {
                    $this->_smarty->display("404.tmpl");
                }
                
                return false;
            } else {
                return true;
            }
        }
    }
}
