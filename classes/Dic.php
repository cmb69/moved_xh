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
use Moved\Infra\SystemChecker;

class Dic
{
    public static function makeNotFoundController(): NotFoundController
    {
        global $pth, $plugin_tx;

        return new NotFoundController(
            "{$pth['folder']['plugins']}moved/",
            $plugin_tx['moved'],
            self::makeDbService(),
            new Logger()
        );
    }

    public static function makeMainAdminController(): MainAdminController
    {
        global $sn, $pth, $plugin_tx, $_XH_csrfProtection;

        return new MainAdminController(
            $sn,
            "{$pth['folder']['plugins']}moved/",
            $plugin_tx['moved'],
            $_XH_csrfProtection,
            self::makeDbService()
        );
    }

    public static function makeInfoController(): InfoController
    {
        global $pth, $plugin_tx;

        return new InfoController(
            "{$pth['folder']['plugins']}moved/",
            $plugin_tx['moved'],
            new SystemChecker()
        );
    }

    private static function makeDbService(): DbService
    {
        global $pth;

        return new DbService("{$pth['folder']['content']}moved.txt");
    }
}
