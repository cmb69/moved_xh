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
 * Writes a file and returns whether that succeeded.
 *
 * @param string $filename A file path.
 * @param string $string   A string to write as file contents.
 *
 * @return bool
 */
function Moved_writeFile($filename, $string)
{
    $func = 'file_put_contents';
    if (function_exists($func)) {
        return (bool) $func($filename, $string);
    } else {
        $ok = (($fp = fopen($filename, 'w')) !== false
            && fwrite($fp, $string) !== false);
        if ($fp !== false) {
            fclose($fp);
        }
        return $ok;
    }
}

/**
 * Returns the plugin information view.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The localization of the core.
 * @global array The localization of the plugins.
 */
function Moved_info() // RELEASE-TODO: syscheck
{
    global $pth, $tx, $plugin_tx;

    $ptx = $plugin_tx['moved'];
    $phpVersion = '4.3.0';
    foreach (array('ok', 'warn', 'fail') as $state) {
        $images[$state] = "{$pth['folder']['plugins']}moved/images/$state.png";
    }
    $checks = array();
    $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)] =
        version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
    foreach (array('pcre', 'session') as $ext) {
	$checks[sprintf($ptx['syscheck_extension'], $ext)] =
            extension_loaded($ext) ? 'ok' : 'fail';
    }
    $checks[$ptx['syscheck_magic_quotes']] =
        !get_magic_quotes_runtime() ? 'ok' : 'fail';
    $checks[$ptx['syscheck_encoding']] =
        strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
    foreach (array('languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'] . 'moved/' . $folder;
    }
    $folders[] = Moved_dataFolder();
    foreach ($folders as $folder) {
	$checks[sprintf($ptx['syscheck_writable'], $folder)] =
            is_writable($folder) ? 'ok' : 'warn';
    }
    $bag = array(
        'ptx' => $ptx,
        'images' => $images,
        'checks' => $checks,
        'icon' => $pth['folder']['plugins'] . 'moved/moved.png',
        'version' => MOVED_VERSION
    );
    return Moved_render('info', $bag);
}

/**
 * Handles editing of the moved pages files.
 *
 * @return string (X)HTML.
 *
 * @global string The script name.
 * @global string The requested action.
 * @global array  The localization of the core.
 */
function Moved_admin()
{
    global $sn, $action, $tx;

    $filename = Moved_dataFolder() . 'data.csv';
    if ($action == 'plugin_textsave') {
        $contents = stsl($_POST['plugin_text']);
        Moved_writeFile($filename, $contents);
    } else {
        if (!file_exists($filename)) {
            touch($filename);
        }
        $contents = file_get_contents($filename);
    }
    $contents = htmlspecialchars($contents, ENT_NOQUOTES, 'UTF-8');
    $action = $sn . '?&moved';
    $label = array(
        'save' => ucfirst($tx['action']['save'])
    );
    $bag = compact('action', 'contents', 'label');
    return Moved_render('admin', $bag);
}

/*
 * Handle the plugin administration.
 */
if (isset($moved) && $moved == 'true') {
    $o .= print_plugin_admin('on');
    switch ($admin) {
    case '':
        $o .= Moved_info();
        break;
    case 'plugin_main':
        $o .= Moved_admin();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
