<?PHP
/*
 * This file is part of the Slash 'N Burn content management system.
 *
 * Slash 'N Burn is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * Slash 'N Burn is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Slash 'N Burn; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307  USA
 */

/**
 * Initialization and setup code.
 *
 * This file sets up the include_path ini directive, loads in the
 * framework classes, prepares the Smarty template system, and finally
 * sets up various global variables for use by the rest of the modules
 * in the system.
 *
 * Initialization happens in three phases:
 *
 *   Phase 1: Read in the first stage config and setup the
 *            include_path INI directive.
 *   Phase 2: Load framework classes, setup Dolphin, and load in the
 *            config in the database.
 *   Phase 3: Finally setup the global variables for the modules and
 *            return control to the calling module.
 *
 * Waay offtopic:
 * Oh, and if you like anime, or are interested in really unorthodox
 * stories, have a look at Read or Die. It's funny, a good story, and
 * unorthodox all in one. Oh yeah, and it's the official anime of the
 * Slash 'N Burn project. =op
 *
 * @package slashnburn
 * @copyright Copyright(c) 2004, June Rebecca Tate
 * @author June R. Tate <june@theonelab.com>
 * @version $Revision$
 */

// -----------------------------------------------------------------------------
// Stage 1: Setup our output buffer, load in boostrap config, and include paths.
// -----------------------------------------------------------------------------

ob_start();

// Load in the bootstrap config and base functions
require_once("config.php");
require_once("functions.php");
require_once("errors.php");

// Set our INI settings (security related)
ini_set("include_path",         ".:common");
ini_set("magic_quotes_gpc",     false);   // Turn off magic quotes -- yuck magic
ini_set("magic_quotes_runtime", false);   // Bad magic.
ini_set("magic_quotes_sybase",  false);   // EVIL magic.
ini_set("register_globals",     false);   // EVIL! Leave it off!

// Setup our version, name, and main website URL
$_CONFIG['cms/version'] = "v0.0.1";
$_CONFIG['cms/name']    = "Slash 'N Burn";
$_CONFIG['cms/url']     = "http://www.slashnburn.org";


// -----------------------------------------------------------------------------
// Stage 2: Load in our classes and setup the error handler.
// -----------------------------------------------------------------------------

require_once("smarty/Smarty.class.php");
require_once("jtlib/Utils.class.php");
require_once("jtlib/Singleton.class.php");
require_once("jtlib/ErrorHandler.class.php");
require_once("jtlib/BaseClass.class.php");
require_once("jtlib/Dolphin.class.php");
require_once("jtlib/DolphinDriver.class.php");
require_once("jtlib/DolphinDriver_mysql.class.php");
require_once("jtlib/XMLRPC.class.php");
require_once("classes/Config.class.php");
require_once("classes/SlashNBurn.class.php");
require_once("classes/Navigation.class.php");
require_once("classes/Module.class.php");

$_EH = new ErrorHandler($_CONFIG['errors/mode'], $_CONFIG['errors/file'], $_CONFIG['errors/level']);


/* Go Yomiko! */
