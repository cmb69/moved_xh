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

use Pfw\View\View;
use Pfw\View\HtmlString;

class MainAdminController
{
    /**
     * @var string
     */
    private $contentFolder;

    /**
     * @var XH_CSRFProtection
     */
    private $csrfProtector;

    public function __construct()
    {
        global $pth, $_XH_csrfProtection;

        $this->contentFolder = $pth['folder']['content'];
        $this->csrfProtector = $_XH_csrfProtection;
    }

    public function defaultAction()
    {
        $contents = (new DbService)->readTextContent();
        $this->prepareView($contents)->render();
    }

    public function saveAction()
    {
        $this->csrfProtector->check();
        $contents = $_POST['plugin_text'];
        $contents = preg_replace('/\r\n|\r|\n/', PHP_EOL, $contents);
        $dbService = new DbService;
        if ($dbService->storeTextContent($contents)) {
            $url = CMSIMPLE_URL . '?&moved&admin=plugin_main&action=plugin_text';
            header('Location: ' . $url, true, 303);
            exit();
        } else {
            e('cntsave', 'file', $dbService->getFilename());
        }
        $this->prepareView($contents)->render();
    }

    /**
     * @param string $contents
     * @return View
     */
    private function prepareView($contents)
    {
        global $sn;

        return (new View('moved'))
            ->template('admin')
            ->data([
                'csrfTokenInput' => new HtmlString($this->csrfProtector->tokenInput()),
                'contents' => $contents,
                'actionUrl' => "$sn?&moved"
            ]);
    }
}
