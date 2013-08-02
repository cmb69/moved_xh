<?php

/**
 * Front-end functionality of Moved_XH.
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
 * The plugin version.
 */
define('MOVED_VERSION', '1dev1');

/**
 * The fully qualified base URL for redirections.
 */
define('MOVED_URL', 'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['HTTP_HOST']
    . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT'])
    . preg_replace('/index.php$/', '', $sn));

/**
 * Returns the path of the data folder.
 *
 * @return string
 *
 * @global array The paths of system files and folders.
 */
function Moved_dataFolder()
{
    global $pth;

    $dirname = $pth['folder']['content'] . 'moved/';
    if (!file_exists($dirname)) {
        if (!mkdir($dirname)) {
            e('cntwriteto', 'folder', $dirname);
        }
    }
    return $dirname;
}

/**
 * Locks resp. unlocks the data file. Returns whether that succeeded.
 *
 * @param int $operation A LOCK_* constant.
 *
 * @return bool
 */
function Moved_lock($operation)
{
    static $handle;

    $filename = Moved_dataFolder() . 'data.csv';
    if (!isset($handle)) {
        $handle = fopen($filename, 'r+');
    }
    $ok = flock($handle, $operation);
    if ($operation == LOCK_UN) {
        fclose($handle);
        $handle = null;
    }
    return $ok;
}

/**
 * Returns the records of moved pages as associative array,
 * <var>false</var> on failure reading the file.
 *
 * @return array
 */
function Moved_data()
{
    $filename = Moved_dataFolder() . 'data.csv';
    if (!file_exists($filename)) {
        touch($filename);
    }
    Moved_lock(LOCK_SH);
    $lines = file($filename);
    Moved_lock(LOCK_UN);
    if ($lines === false) {
        return false;
    }
    $records = array();
    foreach ($lines as $line) {
        if (trim($line) != '') {
            $fields = explode('->', $line);
            $key = trim($fields[0]);
            $value = isset($fields[1]) ? trim($fields[1]) : '';
            $records[$key] = $value;
        }
    }
    return $records;
}

/**
 * Logs a page not found error. Returns whether that succeeded.
 *
 * @return bool
 *
 * @global string The URL of the requested page.
 */
function Moved_log404()
{
    global $su;

    $time = isset($_SERVER['REQUEST_TIME'])
        ? $_SERVER['REQUEST_TIME']
        : time();
    $time = date('Y-m-d H:i:s', $time);
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $line = $time . "\t" . $su . "\t" . $referrer;
    $filename = Moved_dataFolder() . 'log.csv';
    $ok = (($fp = fopen($filename, 'a')) !== false
        &&  fwrite($fp, $line . PHP_EOL) !== false);
    if ($fp !== false) {
        fclose($fp);
    }
    return $ok;
}

/**
 * Sends a status header.
 *
 * @param string $status The HTTP status header.
 *
 * @return void
 *
 * @global bool Whether PHP runs as (F)CGI.
 * @global bool Whether the server is IIS.
 */
function Moved_statusHeader($status)
{
    global $cgi, $iis;

    $header = ($cgi || $iis ? 'Status: ' : 'HTTP/1.1 ') . $status;
    header($header);
}

/**
 * Returns whether a string is a valid UTF-8 string.
 *
 * @param string $str A string.
 *
 * @return bool
 */
function Moved_isUtf8($str)
{
    return preg_match('/^.{1}/us', $str) == 1;
}

/**
 * Renders a view template.
 *
 * @param string $_template The path of the template file.
 * @param array  $_bag      The variables.
 *
 * @return string (X)HTML.
 *
 * @global array The paths of system files and folders.
 * @global array The configuration of the core.
 */
function Moved_render($_template, $_bag)
{
    global $pth, $cf;

    $_template = $pth['folder']['plugins'] . 'moved/views/' . $_template
        . '.htm';
    $_xhtml = strtolower($cf['xhtml']['endtags']) == 'true';
    unset($pth, $cf);
    extract($_bag);
    ob_start();
    include $_template;
    $view = ob_get_clean();
    if (!$_xhtml) {
        $view = str_replace(' />', '>', $view);
    }
    return $view;
}

/**
 * Hook function for not existing pages. Redirects to new page or gives
 * appropriate response.
 *
 * @global string The URL of the requested page.
 * @global string The title of the page.
 * @global string The (X)HTML output of the contents area.
 * @global array  The localization of the plugins.
 */
function custom_404()
{
    global $su, $title, $o, $plugin_tx;

    $ptx = $plugin_tx['moved'];
    $records = Moved_data();
    if (isset($records[$su])) {
        if ($records[$su]) {
            $qs = strpos($_SERVER['QUERY_STRING'], $su) === 0
                ? substr($_SERVER['QUERY_STRING'], strlen($su))
                : '';
            $url = MOVED_URL . '?' . $records[$su] . $qs;
            header('Location: ' . $url, true, 301);
            exit;
        } else {
            Moved_statusHeader('410 Gone');
            $title = $ptx['title_gone'];
            $url = urldecode($su);
            if (!Moved_isUtf8($url)) {
                $url = $su;
            }
            $desc = str_replace('{{URL}}', $url, $ptx['desc_gone']);
            $desc = htmlspecialchars($desc, ENT_COMPAT, 'UTF-8');
            $o .= '<p>' . $desc . '</p>';
        }
    } else {
        Moved_statusHeader('404 Not found');
        $title = $ptx['title_notfound'];
        $url = urldecode($su);
        if (!Moved_isUtf8($url)) {
            $url = $su;
        }
        $desc = str_replace('{{URL}}', $url, $ptx['desc_notfound']);
        $desc = htmlspecialchars($desc, ENT_COMPAT, 'UTF-8');
        $o .= '<p>' . $desc . '</p>';
        Moved_log404();
    }
}

?>
