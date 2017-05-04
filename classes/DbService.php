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
     * @var string
     */
    private $filename;

    public function __construct()
    {
        global $pth;

        $this->filename = "{$pth['folder']['content']}moved.csv";
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string
     * @return ?string
     */
    public function findRedirectFor($su)
    {
        $result = null;
        if (($handle = fopen($this->filename, 'r')) !== false) {
            flock($handle, LOCK_SH);
            while (($fields = fgetcsv($handle, 4096, '=')) !== false) {
                if ($fields[0] === $su) {
                    $result = isset($fields[1]) ? $fields[1] : '';
                    break;
                }
            }
            flock($handle, LOCK_UN);
            fclose($handle);
        }
        return $result;
    }

    /**
     * @return ?string
     */
    public function readTextContent()
    {
        $contents = null;
        file_exists($this->filename)
            && ($stream = fopen($this->filename, 'r')) !== false
            && flock($stream, LOCK_SH)
            && ($contents = stream_get_contents($stream)) !== false
            && flock($stream, LOCK_UN)
            && fclose($stream);
        if ($contents === false) {
            $contents = null;
        }
        return $contents;
    }

    /**
     * @param string $content
     * @return bool
     */
    public function storeTextContent($content)
    {
        return ($stream = fopen($this->filename, 'c')) !== false
            && flock($stream, LOCK_EX)
            && ftruncate($stream, 0)
            && fwrite($stream, $content) !== false
            && flock($stream, LOCK_UN)
            && fclose($stream);
    }
}
