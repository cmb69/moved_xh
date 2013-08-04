<?php

/**
 * Handling the functionality of Moved_XH.
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

/**
 * A class for handling the functionality of Moved_XH.
 *
 * @category CMSimple_XH
 * @package  Moved
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Moved_XH
 */
class Moved
{
    /**
     * Returns the path of the data folder.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    function dataFolder()
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
     * Returns the records of moved pages as associative array.
     *
     * @return array
     */
    function data()
    {
        $filename = $this->dataFolder() . 'data.csv';
        $records = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            flock($handle, LOCK_SH);
            while (($fields = fgetcsv($handle, 4096, '=')) !== false) {
                $key = $fields[0];
                $value = count($fields) > 1 ? $fields[1] : '';
                $records[$key] = $value;
            }
            flock($handle, LOCK_UN);
            fclose($handle);
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
    function log404()
    {
        global $su;

        $time = isset($_SERVER['REQUEST_TIME'])
            ? $_SERVER['REQUEST_TIME']
            : time();
        $time = date('Y-m-d H:i:s', $time);
        $referrer = isset($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : '';
        $line = $time . "\t" . $su . "\t" . $referrer;
        $filename = $this->dataFolder() . 'log.csv';
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
    function statusHeader($status)
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
    function isUtf8($str)
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
    function render($_template, $_bag)
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
     * Returns an array of success icon file paths.
     *
     * @return array
     */
    function successIcons()
    {
        global $pth;

        $icons = array();
        foreach (array('ok', 'warn', 'fail') as $state) {
            $icons[$state] = $pth['folder']['plugins'] . 'moved/images/'
                . $state . '.png';
        }
        return $icons;
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
    function info() // RELEASE-TODO: syscheck
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['moved'];
        $phpVersion = '4.3.10';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('date', 'pcre') as $ext) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $checks[$ptx['syscheck_magic_quotes']]
            = !get_magic_quotes_runtime() ? 'ok' : 'fail';
        $checks[$ptx['syscheck_encoding']]
            = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
        foreach (array('languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'moved/' . $folder;
        }
        $folders[] = $this->dataFolder();
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable'], $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        $bag = array(
            'ptx' => $ptx,
            'images' => $this->successIcons(),
            'checks' => $checks,
            'icon' => $pth['folder']['plugins'] . 'moved/moved.png',
            'version' => MOVED_VERSION
        );
        return $this->render('info', $bag);
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
    function admin()
    {
        global $sn, $action, $tx;

        $filename = $this->dataFolder() . 'data.csv';
        if ($action == 'plugin_textsave') {
            $contents = stsl($_POST['plugin_text']);
            $contents = preg_replace('/\r\n|\r|\n/', PHP_EOL, $contents);
            if (($handle = fopen($filename, 'c')) !== false) {
                flock($handle, LOCK_EX);
                ftruncate($handle, 0);
                fwrite($handle, $contents);
                flock($handle, LOCK_UN);
                fclose($handle);
                $url = MOVED_URL . '?&moved&admin=plugin_main&action=plugin_text';
                header('Location: ' . $url, true, 303);
                exit();
            } else {
                e('cntsave', 'file', $filename);
            }
        } else {
            $contents = '';
            if (($handle = fopen($filename, 'r')) !== false) {
                flock($handle, LOCK_SH);
                while (($line = fgets($handle, 4096)) !== false) {
                    $contents .= $line;
                }
                flock($handle, LOCK_UN);
                fclose($handle);
            }
        }
        $contents = htmlspecialchars($contents, ENT_NOQUOTES, 'UTF-8');
        $action = $sn . '?&moved';
        $label = array(
            'save' => ucfirst($tx['action']['save'])
        );
        $bag = compact('action', 'contents', 'label');
        return $this->render('admin', $bag);
    }

}
