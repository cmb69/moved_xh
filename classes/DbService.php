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

class DbService
{
    /**
     * @return array
     */
    public function data()
    {
        global $pth;

        $filename = "{$pth['folder']['content']}moved.csv";
        $records = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            flock($handle, LOCK_SH);
            while (($fields = fgetcsv($handle, 4096, '=')) !== false) {
                $key = $fields[0];
                $value = count($fields) > 1 ? $fields[1] : '';
                $records[$key] = $value;
            }
            flock($handle, LOCK_UN);
            fclose($handle);
        }
        return $records;
    }
}
