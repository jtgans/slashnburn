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
 * Misc functions that have no other logical place for now.
 *
 * Eventually these functions will be moved to a more logical place,
 * such as a Utility class or something. In the meantime, they stay
 * here until I figure out what to do with them. =o)
 *
 * @package slashnburn
 * @copyright Copyright(c) 2004, June Rebecca Tate
 * @author June R. Tate <june@theonelab.com>
 * @version $Revision$
 */

function protect_email($tpl_output, &$smarty)
{
    preg_match_all('/([a-zA-Z0-9-.+]+)@([a-zA-Z0-9-.]+)/', $tpl_output, $matches, PREG_SET_ORDER);

    foreach ($matches AS $val) {
        $search = array_shift($val);
        
        $replace  = $val[0] ." [at] ";
        $replace .= str_replace(".", " [dot] ", $val[1]);
        
        $tpl_output = str_replace($search, $replace, $tpl_output);
    }

    return $tpl_output;
}
