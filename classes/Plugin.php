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

class Plugin
{
    const VERSION = '@MOVED_VERSION@';

    public function run()
    {
        if (XH_ADM) {
            if (XH_wantsPluginAdministration('moved')) {
                $this->handleAdministration();
            }
        }
    }

    private function handleAdministration()
    {
        global $o, $admin, $action;
    
        $moved = new Moved;
        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                $o .= $moved->info();
                break;
            case 'plugin_main':
                $o .= $moved->admin();
                break;
            default:
                $o .= plugin_admin_common($action, $admin, 'moved');
        }
    }
}
