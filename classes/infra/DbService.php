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

namespace Moved\Infra;

class DbService
{
    /** @var string */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function findRedirectFor(string $su): ?string
    {
        $result = null;
        if (($handle = fopen($this->filename, 'r')) !== false) {
            flock($handle, LOCK_SH);
            while (($fields = fgetcsv($handle, 4096, '=')) !== false) {
                if ($fields[0] === $su) {
                    $result = isset($fields[1]) ? $fields[1] : '';
                    break;
                } else {
                    $quoted = preg_quote($fields[0], '/');
                    $search = '/^' . str_replace(['\?', '\*'], ['(.)', '(.*)'], $quoted) . '$/';
                    if (preg_match($search, $su, $matches)) {
                        if (!isset($fields[1])) {
                            $result = '';
                        } else {
                            $result = preg_replace_callback(
                                '/\$(\d)/',
                                function ($m) use ($matches) {
                                    return $matches[$m[1]];
                                },
                                $fields[1]
                            );
                        }
                        break;
                    }
                }
            }
            flock($handle, LOCK_UN);
            fclose($handle);
        }
        return $result;
    }

    public function readTextContent(): ?string
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

    public function storeTextContent(string $content): bool
    {
        return ($stream = fopen($this->filename, 'c')) !== false
            && flock($stream, LOCK_EX)
            && ftruncate($stream, 0)
            && fwrite($stream, $content) !== false
            && flock($stream, LOCK_UN)
            && fclose($stream);
    }
}
