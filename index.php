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

const MOVED_VERSION = "1.0beta4";

if (!function_exists('custom_404')) {
    /** @return void */
    function custom_404()
    {
        global $su, $o;

        $o .= Dic::makeNotFoundController()->defaultAction($su)->trigger();
    }
}
