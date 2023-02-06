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

class Response
{
    /** @var string */
    private $body;

    /** @var int|null */
    private $statusCode;

    public function __construct(string $body, ?int $statusCode = null)
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
    }

    /** @return string|never */
    public function trigger()
    {
        if ($this->statusCode !== null) {
            if ($this->statusCode >= 300 && $this->statusCode < 400) {
                header("Location: {$this->body}", true, $this->statusCode);
                exit;
            } elseif ($this->statusCode >= 400 && $this->statusCode < 500) {
                switch ($this->statusCode) {
                    case 404:
                        header("HTTP/1.1 404 Not found");
                        break;
                    case 410:
                        header("HTTP/1.1 Gone");
                        break;
                    default:
                        assert(false); // @phpstan-ignore-line
                }
            } else {
                assert(false); // @phpstan-ignore-line
            }
        }
        return $this->body;
    }
}
