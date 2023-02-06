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

use Moved\Dic;

if (!defined('CMSIMPLE_XH_VERSION')) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

/**
 * @var string $admin
 * @var string $action
 * @var string $o
 */

XH_registerStandardPluginMenuItems(true);

if (XH_wantsPluginAdministration('moved')) {
    $o .= print_plugin_admin('on');
    switch ($admin) {
        case '':
            $o .= Dic::makeInfoController()->defaultAction();
            break;
        case 'plugin_main':
            if ($action == 'plugin_textsave') {
                $temp = 'saveAction';
            } else {
                $temp = 'defaultAction';
            }
            $o .= Dic::makeMainAdminController()->{$temp}()->trigger();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
