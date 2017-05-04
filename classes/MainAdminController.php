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

class MainAdminController
{
    public function defaultAction()
    {
        $filename = (new Moved)->dataFolder() . 'data.csv';
        $contents = '';
        if (($handle = fopen($filename, 'r')) !== false) {
            flock($handle, LOCK_SH);
            while (($line = fgets($handle, 4096)) !== false) {
                $contents .= $line;
            }
            flock($handle, LOCK_UN);
            fclose($handle);
        }
        $this->prepareView($contents)->render();
    }

    public function saveAction()
    {
        $filename = (new Moved)->dataFolder() . 'data.csv';
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
        $this->prepareView($contents)->render();
    }

    /**
     * @param string $contents
     * @return View
     */
    private function prepareView($contents)
    {
        global $sn, $tx;

        $view = new View('admin');
        $view->contents = $contents;
        $view->actionUrl = "$sn?&moved";
        $view->saveLabel = ucfirst($tx['action']['save']);
        return $view;
    }
}
