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
 * First level config.
 *
 * This configuration file contains base information about how SNB's
 * environment, which includes things such as database connection
 * information.
 *
 * This file is meant to be edited by you, the user. Some time in the
 * future, the entire system will have an installer that will automate
 * this task, and then you, the user, won't have to touch this
 * file. Until then, please provide the information needed for each
 * variable in this file. Thanks.
 *
 * @package slashnburn
 * @copyright Copyright(c) 2004, June Rebecca Tate
 * @author June R. Tate <june@theonelab.com>
 * @version $Revision$
 */

// This variable is the URL to the database. The format is:
// mysql://<username>:<password>@<hostname>/<databasename>
$_CONFIG['global/db_url'] = "mysql://slashnburn:slashnburn@localhost/slashnburn";

// !!! THIS VARIABLE IS AUTO-SET BY THE INSTALLER -- DO NOT EDIT !!!
//
// This variable is the fully qualified path to the base directory of
// where the installation of SNB is. Please make sure you make this
// accurate -- many of SNB's core functions rely on this parameter.
//
// !!! THIS VARIABLE IS AUTO-SET BY THE INSTALLER -- DO NOT EDIT !!!
$_CONFIG['global/base_dir'] = "";

// Error reporting values. Generally users don't need to play with
// this. Developers, however will find it useful for debugging and
// such. errors/mode is either 0 for inline HTML error reporting, or 1
// for logging to a file in plaintext. If you set errors/mode to 1,
// make sure that the file that you reference in errors/file is set to
// a webserver writable file, otherwise you'll get
// errors. errors/level is used to set the error reporting level. Most
// users will want to set this to E_ERROR.
$_CONFIG['errors/mode']  = 1;
$_CONFIG['errors/file']  = "error.log";
$_CONFIG['errors/level'] = E_ALL ^ E_NOTICE;
