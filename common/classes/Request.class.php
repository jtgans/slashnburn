<?PHP
require_once("classes/Config.class.php");

class Request
{
    var $_url;
    var $_module;
    var $_args;
    var $_config;
    
    function Request()
    {
        $this->_config = &Singleton::getInstance("Config");

        if (!$this->_config) {
            trigger_error("Request::parseURL: Unable to find instance of Config!", ERR_ERROR);
            return false;
        } else {
            return true;
        }
    }

    function parseURL()
    {
        $this->_args = array();
        $this->_module = "";
        $this->_default_mod = false;
        
        // Rip out the index.php part if it's there
        $this->_args = preg_replace('/^\/index.php/', '', $_SERVER['REQUEST_URI']);

        // Rip out any query strings
        $this->_args = preg_replace('/\?.*$/', '', $this->_args);
        
        // Split up the args
        $this->_args = explode("/", $this->_args);
        
        // Run through the args and remove empty ones
        $valid_args = array();
        foreach ($this->_args AS $key => $val) {
            if (strlen(trim($val)) > 0) {
                $valid_args[] = $val;
            }
        }
        $this->_args = $valid_args;
        
        // If we don't have any arguments, use the default in the
        // database. Also, set the flag that we're using the default
        // module.
        if (count($this->_args) == 0) {
            $this->_module = $this->_config->getKey('global/default_module');
            $this->_default_mod = true;
        } else {
            // Shift off the module name
            $this->_module = array_shift($this->_args);
        }
    }
    
    function usingDefaultMod()
    {
        return $this->_default_mod;
    }
    
    function getModule()
    {
        return $this->_module;
    }

    function getArg($index)
    {
        return $this->_args[$index];
    }

    function getAllArgs()
    {
        return $this->_args;
    }
}
