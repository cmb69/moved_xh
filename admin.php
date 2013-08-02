<?php

/**
 * Back-End functionality of Moved_XH.
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * Handles editing of the moved pages files.
 *
 * @return string (X)HTML.
 *
 * @global string $sn     The script name.
 * @global string $action The requested action.
 */
function Moved_admin()
{
    global $sn, $action;

    $filename = Moved_dataFolder() . 'data.csv';
    if ($action == 'plugin_textsave') {
        $contents = stsl($_POST['plugin_text']);
        file_put_contents($filename, $contents);
    } else {
        if (!file_exists($filename)) {
            touch($filename);
        }
        $contents = file_get_contents($filename);
    }
    $contents = htmlspecialchars($contents, ENT_NOQUOTES, 'UTF-8');
    $action = $sn . '?&moved';
    $o = '<div class="pluginedit">'
        . '<form action="' . $action . '" method="post">'
        . '<div class="plugineditcaption">Moved</div>'
        . tag('input type="hidden" name="admin" value="plugin_main"')
        . tag('input type="hidden" name="action" value="plugin_textsave"')
        . '<textarea class="plugintextarea" name="plugin_text" cols="80" rows="25">'
        . $contents . '</textarea>'
        . tag('br')
        . tag('input type="submit" class="submit" value="Save"')
        . '</form></div>';
    return $o;
}

/*
 * Handle the plugin administration.
 */
if (isset($moved) && $moved == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= 'INFO';
        break;
    case 'plugin_main':
        $o .= Moved_admin();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
