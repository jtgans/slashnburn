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
 * This is the main entry point to the entire system. It's not quite
 * the most elegant way to set things up, but it makes for a more
 * predictable system.
 *
 * After the setup.php file has set the environment up, this file then
 * determines which module to transfer control to. Basically, it's a
 * glorified redirector. =op
 *
 * @package slashnburn
 * @copyright Copyright(c) 2004, June Rebecca Tate
 * @author June R. Tate <june@theonelab.com>
 * @version $Revision$
 */

require_once("common/include/setup.php");

$_SNB = &Singleton::getInstance("SlashNBurn");
