<?PHP
require_once("jtlib/ErrorHandler.class.php");
require_once("jtlib/Dolphin.class.php");

class Navigation
{
    var $_db;
    var $_nav;
    
    function Navigation()
    {
        $this->_db = &Singleton::getInstance("Dolphin");
        $this->_request = &Singleton::getInstance("Request");

        if ((!$this->_db) || (!$this->_request)) {
            if (!$this->_db)      trigger_error("Navigation::Navigation: Unable to get instance of Dolphin.", ERR_ERROR);
            if (!$this->_request) trigger_error("Navigation::Navigation: Unable to get instance of Request.", ERR_ERROR);

            return false;
        } else {
            return true;
        }
    }

    function loadNav()
    {
        $this->_nav = array();

        $query = "SELECT * FROM navigation WHERE parent_id IS NULL ORDER BY ordernum";
        if (!$this->_db->query($query)) {
            trigger_error("Navigation::loadNav: Unable to load navigation from database.", ERR_ERROR);
            return false;
        }

        while ($row = $this->_db->getResultsRow()) {
            $this->_nav[$row['nav_id']] = $row;
        }

        foreach ($this->_nav AS $key => $val) {
            $query = "SELECT * FROM navigation WHERE parent_id=". $val['nav_id'] ." ORDER BY ordernum";

            if (!$this->_db->query($query)) {
                trigger_error("Navigation::loadNav: Unable to load navigation from database.", ERR_ERROR);
                return false;
            }

            if ($this->_db->getNumRows() > 0) {
                $this->_nav[$key]['children'] = $this->_db->getResultsArray();
            }
        }

        return true;
    }

    function getNav()
    {
        return $this->_nav;
    }

    function setNav($nav)
    {
        $this->_nav = $nav;
    }
    
    function appendSubNav($module, $caption, $params)
    {
        foreach ($this->_nav AS $idx => $row) {
            if ($row['module'] == $module) {
                $this->_nav[$idx]['children'][] =  array('module' => $module,
                                                         'caption'  => $caption,
                                                         'params'   => $params);
            }
        }
    }

    function isSelectedNav($idx)
    {
        if (isset($this->_nav[$idx])) {
            $uri = $this->_nav[$idx]['module'] .'/'. $this->_nav[$idx]['params'];
            return strstr($_SERVER['REQUEST_URI'], $uri);
        } else {
            trigger_error("Navigation::isSelectedNav: No nav by index $idx.", ERR_WARNING);
            return false;
        }
    }
}
