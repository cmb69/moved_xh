<?php

/**
 * Copyright 2013-2023 Christoph M. Becker
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

    /** @var DbService */
    private $dbService;

    /** @var View */
    private $view;

    /** @param array<string,string> $lang */
    public function __construct(string $pluginFolder, string $contentFolder, array $lang)
    {
        $this->lang = $lang;
        $this->dbService = new DbService("{$contentFolder}moved.csv");
        $this->view = new View("{$pluginFolder}views/", $this->lang);
    }

    /** @return void */
    public function defaultAction(string $selectedUrl)
    {
        global $title;
    
        $redirect = $this->dbService->findRedirectFor($selectedUrl);
        if (isset($redirect)) {
            if ($redirect) {
                if (strpos($redirect, '://') !== false) {
                    $url = $redirect;
                } else {
                    $qs = strpos($_SERVER['QUERY_STRING'], $selectedUrl) === 0
                        ? substr($_SERVER['QUERY_STRING'], strlen($selectedUrl))
                        : '';
                    $url = CMSIMPLE_URL . '?' . $redirect . $qs;
                }
                header('Location: ' . $url, true, 301);
                exit;
            } else {
                header('HTTP/1.1 410 Gone');
                $title = $this->lang['title_gone'];
                $url = urldecode($selectedUrl);
                if (!$this->isUtf8($url)) {
                    $url = $selectedUrl;
                }
                echo $this->view->render('gone', [
                    'url' => $url,
                ]);
            }
        } else {
            header('HTTP/1.1 404 Not found');
            $title = $this->lang['title_notfound'];
            $url = urldecode($selectedUrl);
            if (!$this->isUtf8($url)) {
                $url = $selectedUrl;
            }
            echo $this->view->render('not-found', [
                'url' => $url,
            ]);
            $this->log404($selectedUrl);
        }
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
    private function log404(string $selectedUrl)
    {
        $referrer = isset($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : 'unknown';
        return XH_logMessage('warning', 'moved', 'not found', "$selectedUrl from $referrer");
    }
}
