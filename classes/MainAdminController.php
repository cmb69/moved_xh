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

use XH\CSRFProtection as CsrfProtector;
use Moved\Infra\DbService;
use Moved\Infra\Response;

class MainAdminController
{
    /** @var string */
    private $scriptName;

    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $lang;

    /** @var CsrfProtector */
    private $csrfProtector;

    /** @var DbService */
    private $dbService;

    /** @var View */
    private $view;

    /** @param array<string,string> $lang */
    public function __construct(
        string $scriptName,
        string $pluginFolder,
        array $lang,
        CsrfProtector $csrfProtector,
        DbService $dbService
    ) {
        $this->scriptName = $scriptName;
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->csrfProtector = $csrfProtector;
        $this->dbService = $dbService;
        $this->view = new View("{$this->pluginFolder}views/", $this->lang);
    }

    public function defaultAction(): Response
    {
        $contents = $this->dbService->readTextContent();
        return new Response($this->renderView($contents));
    }

    public function saveAction(): Response
    {
        $this->csrfProtector->check();
        $contents = $_POST['plugin_text'];
        $contents = preg_replace('/\r\n|\r|\n/', PHP_EOL, $contents);
        if ($this->dbService->storeTextContent($contents)) {
            $url = CMSIMPLE_URL . "?&moved&admin=plugin_main&action=plugin_text";
            return new Response($url, 303);
        }
        $o = $this->view->error('error_save', $this->dbService->getFilename())
            . $this->renderView($contents);
        return new Response($o);
    }

    private function renderView(string $contents): string
    {
        return $this->view->render('admin', [
            'csrfTokenInput' => $this->csrfProtector->tokenInput(),
            'contents' => $contents,
            'actionUrl' => "{$this->scriptName}?&moved&admin=plugin_main&action=plugin_textsave",
        ]);
    }
}
