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

class Plugin
{
    const VERSION = '1.0beta4';

    /** @return void */
    public function run()
    {
        if (defined('XH_ADM') && XH_ADM) {
            XH_registerStandardPluginMenuItems(true);
            if (XH_wantsPluginAdministration('moved')) {
                $this->handleAdministration();
            }
        }
    }

    /** @return void */
    private function handleAdministration()
    {
        global $sn, $pth, $plugin_tx, $o, $admin, $action, $_XH_csrfProtection;
    
        $o .= print_plugin_admin('on');
        switch ($admin) {
            case '':
                ob_start();
                (new InfoController("{$pth['folder']['plugins']}moved/", $plugin_tx['moved']))->defaultAction();
                $o .= ob_get_clean();
                break;
            case 'plugin_main':
                $controller = new MainAdminController(
                    $sn,
                    "{$pth['folder']['plugins']}moved/",
                    $pth['folder']['content'],
                    $plugin_tx['moved'],
                    $_XH_csrfProtection
                );
                if ($action == 'plugin_textsave') {
                    $act = 'saveAction';
                } else {
                    $act = 'defaultAction';
                }
                ob_start();
                $controller->{$act}();
                $o .= ob_get_clean();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }
}
