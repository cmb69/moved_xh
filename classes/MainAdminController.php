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

class MainAdminController
{
    /** @var string */
    private $scriptName;

    /** @var string */
    private $pluginFolder;

    /** @var string */
    private $contentFolder;

    /** @var array<string,string> */
    private $lang;

    /**
     * @var CsrfProtector
     */
    private $csrfProtector;

    /** @param array<string,string> $lang */
    public function __construct(
        string $scriptName,
        string $pluginFolder,
        string $contentFolder,
        array $lang,
        CsrfProtector $csrfProtector
    ) {
        $this->scriptName = $scriptName;
        $this->pluginFolder = $pluginFolder;
        $this->contentFolder = $contentFolder;
        $this->lang = $lang;
        $this->csrfProtector = $csrfProtector;
    }

    /** @return void */
    public function defaultAction()
    {
        $contents = (new DbService("{$this->contentFolder}moved.csv"))->readTextContent();
        echo $this->renderView($contents);
    }

    /** @return void */
    public function saveAction()
    {
        $this->csrfProtector->check();
        $contents = $_POST['plugin_text'];
        $contents = preg_replace('/\r\n|\r|\n/', PHP_EOL, $contents);
        $dbService = new DbService("{$this->contentFolder}moved.csv");
        if ($dbService->storeTextContent($contents)) {
            $url = CMSIMPLE_URL . "?&moved&admin=plugin_main&action=plugin_text";
            header('Location: ' . $url, true, 303);
            exit();
        } else {
            e('cntsave', 'file', $dbService->getFilename());
        }
        echo $this->renderView($contents);
    }

    /**
     * @param string $contents
     * @return string
     */
    private function renderView($contents)
    {
        $view = new View("{$this->pluginFolder}views/", $this->lang);
        return $view->render('admin', [
            'csrfTokenInput' => $this->csrfProtector->tokenInput(),
            'contents' => $contents,
            'actionUrl' => "{$this->scriptName}?&moved&admin=plugin_main&action=plugin_textsave",
        ]);
    }
}
