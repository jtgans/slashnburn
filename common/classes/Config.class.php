<?PHP
require_once("jtlib/Singleton.class.php");

class Config
{
    var $_db;
    var $_keys;

    function Config()
    {
        global $_CONFIG;
        
        $this->_db = &Singleton::getInstance("Dolphin");
        $this->_keys = $_CONFIG;
    }

    function loadConfig()
    {
        global $_CONFIG;
        
        $query = "SELECT * FROM config";
        if (!$this->_db->query($query)) {
            trigger_error("Config::loadConfig: Unable to load configuration from database.", ERR_ERROR);
            return false;
        } else {
            $this->_keys = $_CONFIG;
            
            while ($row = $this->_db->getResultsRow()) {
                $this->_keys[$row['key']] = $row['value'];
            }

            return true;
        }
    }

    function setKey($key, $val)
    {
        $query = "SELECT * FROM config WHERE `key`='$key' LIMIT 1";
        
        if (!$this->_db->query($query)) {
            trigger_error("Config::setKey: Unable to access database.", ERR_ERROR);
            return false;
        } else {
            if ($this->_db->getNumRows() > 0) {
                $query = "UPDATE config SET value='$val' WHERE `key`='$key'";
                
                if (!$this->_db->query($query)) {
                    trigger_error("Config::setKey: Unable to update key '$key'.", ERR_ERROR);
                    return false;
                } else {
                    $this->_keys[$key] = $val;
                    return true;
                }
            } else {
                $query = "INSERT INTO config VALUES ('$key', '$val')";

                if (!$this->_db->query($query)) {
                    trigger_error("Config::setKey: Unable to insert key '$key' into database.", ERR_ERROR);
                    return false;
                } else {
                    $this->_keys[$key] = $val;
                    return true;
                }
            }
        }
    }

    function getKey($key)
    {
        if (isset($this->_keys[$key])) {
            return $this->_keys[$key];
        } else {
            return null;
        }
    }

    function unsetKey($key)
    {
        $query = "SELECT * FROM config WHERE `key`='$key' LIMIT 1";

        if (!$this->_db->query($query)) {
            trigger_error("Config::unsetKey: Unable to access database.", ERR_ERROR);
            return false;
        } else {
            if (!$this->_db->getNumRows() > 0) {
                $query = "DELETE FROM config WHERE `key`='$key'";

                if (!$this->_db->query($query)) {
                    trigger_error("Config::unsetKey: Unable to delete key '$key' from database.", ERR_ERROR);
                    return false;
                } else {
                    return true;
                }
            } else {
                trigger_error("Config::unsetKey: Key '$key' does not exist in the database.", ERR_WARNING);
                return false;
            }
        }
    }

    function getModules()
    {
        $query = "SELECT * FROM modules";
        if (!$this->_db->query($query)) {
            trigger_error("Config::getModules: Unable to load list of modules.", ERR_ERROR);
            return false;
        } else {
            return $this->_db->getResultsArray();
        }
    }
}
