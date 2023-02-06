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

use Pfw\SystemCheckService;

class InfoController
{
    /**
     * @var string
     */
    private $pluginFolder;

    public function __construct()
    {
        global $pth;

        $this->pluginFolder = "{$pth['folder']['plugins']}moved/";
    }

    public function defaultAction()
    {
        global $plugin_tx;

        $view = new View("{$this->pluginFolder}views/", $plugin_tx['moved']);
        echo $view->render('info', [
            'checks' => (new SystemCheckService)
                ->minPhpVersion('5.4.0')
                ->minXhVersion('1.6.3')
                ->writable("{$this->pluginFolder}css/")
                ->writable("{$this->pluginFolder}languages/")
                ->getChecks(),
            'logo' => "{$this->pluginFolder}moved.png",
            'version' => Plugin::VERSION
        ]);
    }
}
