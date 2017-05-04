<?php

/**
 * Copyright 2013-2017 Christoph M. Becker
 *
 * This file is part of Moved_XH.
 *
 * Moved_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moved_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moved_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Moved;

class Moved
{
    /**
     * @param string $key
     * @return string
     */
    public function l10n($key)
    {
        global $plugin_tx;

        $args = array_slice(func_get_args(), 1);
        $o = vsprintf($plugin_tx['moved'][$key], $args);
        return $o;
    }

    /**
     * @return string
     */
    public function dataFolder()
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
     * @return array
     */
    public function data()
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
     * @return bool
     */
    public function log404()
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
     * @param string $status
     * @return void
     */
    public function statusHeader($status)
    {
        global $cgi, $iis;

        $header = ($cgi || $iis ? 'Status: ' : 'HTTP/1.1 ') . $status;
        header($header);
    }

    /**
     * @param string $str
     */
    public function isUtf8($str)
    {
        return preg_match('/^.{1}/us', $str) == 1;
    }

    /**
     * @return array
     */
    public function successIcons()
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
     * @return array
     */
    public function writableFolders()
    {
        global $pth;

        $folders = array();
        foreach (array('languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'moved/' . $folder;
        }
        $folders[] = $this->dataFolder();
        return $folders;
    }

    /**
     * @return string
     */
    public function info() // RELEASE-TODO: syscheck
    {
        global $pth, $tx, $plugin_tx;

        $phpVersion = '5.4.0';
        $checks = array();
        $checks[$this->l10n('syscheck_phpversion', $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('date', 'pcre') as $ext) {
            $checks[$this->l10n('syscheck_extension', $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $checks[$this->l10n('syscheck_magic_quotes')]
            = !get_magic_quotes_runtime() ? 'ok' : 'fail';
        $checks[$this->l10n('syscheck_encoding')]
            = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
        $folders = $this->writableFolders();
        foreach ($folders as $folder) {
            $checks[$this->l10n('syscheck_writable', $folder)]
                = is_writable($folder) ? 'ok' : 'warn';
        }
        $view = new View('info');
        $view->images = $this->successIcons();
        $view->checks = $checks;
        $view->logo = "{$pth['folder']['plugins']}moved/moved.png";
        $view->version = MOVED_VERSION;
        return (string) $view;
    }

    /**
     * @return string
     */
    public function admin()
    {
        global $sn, $action, $tx;

        $filename = $this->dataFolder() . 'data.csv';
        if ($action == 'plugin_textsave') {
            $contents = $_POST['plugin_text'];
            $contents = preg_replace('/\r\n|\r|\n/', PHP_EOL, $contents);
            if (($handle = fopen($filename, 'c')) !== false) {
                flock($handle, LOCK_EX);
                ftruncate($handle, 0);
                fwrite($handle, $contents);
                flock($handle, LOCK_UN);
                fclose($handle);
                $url = CMSIMPLE_URL . '?&moved&admin=plugin_main&action=plugin_text';
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
        $view = new View('admin');
        $view->contents = $contents;
        $view->actionUrl = "$sn?&moved";
        $view->saveLabel = ucfirst($tx['action']['save']);
        return (string) $view;
    }
}
