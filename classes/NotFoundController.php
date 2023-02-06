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

class NotFoundController
{
    /**
     * @var array<string,string>
     */
    private $lang;

    /** @var View */
    private $view;

    public function __construct()
    {
        global $pth, $plugin_tx;

        $this->lang = $plugin_tx['moved'];
        $this->view = new View("{$pth['folder']['plugins']}moved/views/", $this->lang);
    }

    /** @return void */
    public function defaultAction()
    {
        global $su, $title;
    
        $redirect = (new DbService)->findRedirectFor($su);
        if (isset($redirect)) {
            if ($redirect) {
                if (strpos($redirect, '://') !== false) {
                    $url = $redirect;
                } else {
                    $qs = strpos($_SERVER['QUERY_STRING'], $su) === 0
                        ? substr($_SERVER['QUERY_STRING'], strlen($su))
                        : '';
                    $url = CMSIMPLE_URL . '?' . $redirect . $qs;
                }
                header('Location: ' . $url, true, 301);
                exit;
            } else {
                $this->statusHeader('410 Gone');
                $title = $this->lang['title_gone'];
                $url = urldecode($su);
                if (!$this->isUtf8($url)) {
                    $url = $su;
                }
                echo $this->view->render('gone', [
                    'url' => $url,
                ]);
            }
        } else {
            $this->statusHeader('404 Not found');
            $title = $this->lang['title_notfound'];
            $url = urldecode($su);
            if (!$this->isUtf8($url)) {
                $url = $su;
            }
            echo $this->view->render('not-found', [
                'url' => $url,
            ]);
            $this->log404();
        }
    }

    /**
     * @param string $status
     * @return void
     */
    private function statusHeader($status)
    {
        global $cgi, $iis;

        $header = ($cgi || $iis ? 'Status: ' : 'HTTP/1.1 ') . $status;
        header($header);
    }

    /**
     * @param string $str
     * @return bool
     */
    private function isUtf8($str)
    {
        return preg_match('/^.{1}/us', $str) == 1;
    }

    /**
     * @return bool
     */
    private function log404()
    {
        global $su;

        $referrer = isset($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : 'unknown';
        return XH_logMessage('warning', 'moved', 'not found', "$su from $referrer");
    }
}
