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
 * This file defines a set of globally available error messages that
 * SNB will throw when something has gone arwy with either the
 * configuration or something else.
 *
 * @package slashnburn
 * @copyright Copyright(c) 2004, June Rebecca Tate
 * @author June R. Tate <june@theonelab.com>
 * @version $Revision$
 */

define("SNBERR_BADDEFAULTMOD", 0);
define("SNBERR_UNAVAILABLE",   1);
define("SNBERR_BADMODULE",     2);
define("SNBERR_DATABASE",      3);

$_ERRORS[SNBERR_BADDEFAULTMOD] = "The default module could not be found.";
$_ERRORS[SNBERR_UNAVAILABLE]   = "This website is currently not available at this time. Please try back again in a few hours.";
$_ERRORS[SNBERR_BADMODULE]     = "The module that was reference in the URL is not available.";
$_ERRORS[SNBERR_DATABASE]      = "The database was unavailable, incorrectly configured, or some other error occurred.";