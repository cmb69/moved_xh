<?php

/**
 * Back-End functionality of Moved_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Moved
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Moved_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * Handles the plugin administration.
 *
 * @param string $admin  Requested admin section.
 * @param string $action Requested admin action.
 *
 * @return void
 *
 * @global string The (X)HTML of the contents area.
 */
function Moved_admin($admin, $action)
{
    global $o;

    $moved = Moved_instance();
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= $moved->info();
        break;
    case 'plugin_main':
        $o .= $moved->admin();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, 'moved');
    }
}

/*
 * Handle the plugin administration.
 */
if (isset($moved) && $moved == 'true') {
    initvar('admin');
    Moved_admin($admin, $action);
}

?>
