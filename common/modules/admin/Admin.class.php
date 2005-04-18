<?PHP
require_once("classes/Module.class.php");

class Admin extends Module
{
    var $_modules;
    
    function Admin()
    {
        $this->initModule();
    }

    function setupAdminNav()
    {
        $nav = array(array('module'  => "manage",
                           'caption' => "Manage Content",
                           'params'  => ""),
                     array('module'  => "config",
                           'caption' => "Configuration",
                           'params'  => ""),
                     array('module'  => "maint",
                           'caption' => "Maintenance",
                           'params'  => "")
                     );

        $this->_nav->setNav($nav);
        $this->_nav->appendSubNav("config", "General", "general");
        
        foreach ($this->_config->getModules() AS $key => $val) {
            if ($val['name'] == "admin") continue;
            
            @include_once("modules/". $val['name'] ."/". $val['class'] ."Admin.class.php");

            if (class_exists($val['class'] ."Admin")) {
                $class = $val['class'] ."Admin";
                $this->_modules[$val['name']] = new $class();
            } else {
                trigger_error("Admin::setupAdminNav: No module admin class called ". $val['class'] ."Admin", ERR_WARNING);
            }
        }

        $this->_nav->appendSubNav("manage", "Navigation", "nav");
        $this->_nav->appendSubNav("config", "Manage Themes", "themes");
        $this->_nav->appendSubNav("config", "Manage Modules", "modules");
        $this->_nav->appendSubNav("config", "Manage Config Database", "configdb");
        $this->_nav->appendSubNav("maint",  "Backup Database",  "backup");
        $this->_nav->appendSubNav("maint",  "Restore Database", "restore");
    }

    function _checkLogin()
    {
        if (!isset($_SESSION['logged_in'])) {
            if (isset($_POST['submit'])) {
                trigger_error("Admin::handlePage: username [". $_POST['username'] ."]", ERR_NOTICE);
                trigger_error("Admin::handlePage: password [". $_POST['password'] ."]", ERR_NOTICE);
                
                if (($_POST['username'] == $this->_config->getKey('admin/username')) &&
                    ($_POST['password'] == $this->_config->getKey('admin/password'))) {
                    $_SESSION['logged_in'] = true;
                    return true;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    function _handleModule($args)
    {
        if (isset($args[1])) {
            $module = $args[1];
            
            if (isset($this->_modules[$module])) {
                return $this->_modules[$module]->handlePage($args);
            } else {
                trigger_error("Admin::handlePage: Unable to find instance of administrative module by name $module.", ERR_ERROR);
                return array(MODULE_NOTFOUND, "Admin::handlePage: Unable to find instance of administrative module by name $module.");
            }
        } else {
            $this->_smarty->display("admin-header.tmpl");
            echo "<h1 class='line'>". $args[0] ."</h1>";
            $this->_smarty->display("admin-footer.tmpl");
            return array(MODULE_SUCCESS, "Success");
        }
    }
    
    function handlePage($args)
    {
        session_start();
        
        if (!$this->_checkLogin()) {
            $this->_smarty->display("admin-login.tmpl");
            
        } else {
            $this->setupAdminNav();

            // Force the default selection to be the manage section.
            if (!isset($args[0])) {
                $args[0] = "manage";
                $_SERVER['REQUEST_URI'] .= "manage/";
            }
            
            switch ($args[0]) {
                case "manage":
                    // Stave off stupid PHP notices
                    switch ($args[1]) {
                        case "nav":
                            $this->_smarty->display("admin-header.tmpl");
                            echo "<h1 class='line'>". $args[0] ."</h1>";
                            $this->_smarty->display("admin-footer.tmpl");
                            return array(MODULE_SUCCESS, "Success");
                            break;

                        default:
                            return $this->_handleModule($args);
                            break;
                    }
                    break;
                    
                case "config":
                    switch ($args[1]) {
                        case "general":
                        case "themes":
                        case "modules":
                        case "configdb":
                            $this->_smarty->display("admin-header.tmpl");
                            echo "<h1 class='line'>". $args[0] ."</h1>";
                            $this->_smarty->display("admin-footer.tmpl");
                            return array(MODULE_SUCCESS, "Success");
                            break;

                        default:
                            return $this->_handleModule($args);
                            break;
                    }
                    break;
                    
                case "maint":
                    switch ($args[1]) {
                        case "backup":
                        case "restore":
                            $this->_smarty->display("admin-header.tmpl");
                            echo "<h1 class='line'>". $args[0] ."</h1>";
                            $this->_smarty->display("admin-footer.tmpl");
                            return array(MODULE_SUCCESS, "Success");
                            break;

                        default:
                            return $this->_handleModule($args);
                            break;
                    }
                    break;
                    
                case "help":
                    return $this->_handleModule($args);
                    break;
                    
                case "logout":
                    $_SESSION['logged_in'] = false;
                    session_destroy();
                    header("Location: /");
                    return array(MODULE_SUCCESS, "Success");
                    break;
                    
                default:
                    $this->_smarty->display("admin-header.tmpl");
                    $this->_smarty->display("admin-footer.tmpl");
                    return array(MODULE_SUCCESS, "Success");
                    break;
            }
        }
    }
}