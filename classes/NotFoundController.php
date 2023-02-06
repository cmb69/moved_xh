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

use Moved\Infra\DbService;
use Moved\Infra\Logger;
use Moved\Infra\Response;

class NotFoundController
{
    /** @var array<string,string> */
    private $lang;

    /** @var DbService */
    private $dbService;

    /** @var Logger */
    private $logger;

    /** @var View */
    private $view;

    /** @param array<string,string> $lang */
    public function __construct(
        string $pluginFolder,
        array $lang,
        DbService $dbService,
        Logger $logger
    ) {
        $this->lang = $lang;
        $this->dbService = $dbService;
        $this->logger = $logger;
        $this->view = new View("{$pluginFolder}views/", $this->lang);
    }

    public function defaultAction(string $selectedUrl): Response
    {
        $redirect = $this->dbService->findRedirectFor($selectedUrl);
        if (!isset($redirect)) {
            $url = urldecode($selectedUrl);
            if (!$this->isUtf8($url)) {
                $url = $selectedUrl;
            }
            $body = $this->view->render('not-found', [
                'url' => $url,
            ]);
            $this->log404($selectedUrl);
            return new Response($body, 404, $this->lang['title_notfound']);
        }
        if ($redirect) {
            if (strpos($redirect, '://') !== false) {
                $url = $redirect;
            } else {
                $qs = strpos($_SERVER['QUERY_STRING'], $selectedUrl) === 0
                    ? substr($_SERVER['QUERY_STRING'], strlen($selectedUrl))
                    : '';
                $url = CMSIMPLE_URL . '?' . $redirect . $qs;
            }
            return new Response($url, 301);
        }
        $url = urldecode($selectedUrl);
        if (!$this->isUtf8($url)) {
            $url = $selectedUrl;
        }
        $body = $this->view->render('gone', [
            'url' => $url,
        ]);
        return new Response($body, 410, $this->lang['title_gone']);
    }

    private function isUtf8(string $str): bool
    {
        return preg_match('/^.{1}/us', $str) == 1;
    }

    private function log404(string $selectedUrl): bool
    {
        $referrer = isset($_SERVER['HTTP_REFERER'])
            ? $_SERVER['HTTP_REFERER']
            : 'unknown';
        return $this->logger->logNotFound($selectedUrl, $referrer);
    }
}
