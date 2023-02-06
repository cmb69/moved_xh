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

class View
{
    /** @var string */
    private $templateFolder;

    /** @var array<string,string> */
    private $lang;

    /** @param array<string,string> $lang */
    public function __construct(string $templateFolder, array $lang)
    {
        $this->templateFolder = $templateFolder;
        $this->lang = $lang;
    }

    /** @param mixed $args */
    public function text(string $key, ...$args): string
    {
        return $this->escape(vsprintf($this->lang[$key], $args));
    }

    /** @param mixed $args */
    public function error(string $key, ...$args): string
    {
        return sprintf('<p class="xh_fail">%s</p>', $this->text($key, ...$args));
    }

    /** @param array<string,mixed> $_data */
    public function render(string $_template, array $_data): string
    {
        extract($_data);
        ob_start();
        include "{$this->templateFolder}{$_template}.php";
        return (string) ob_get_clean();
    }

    /** @param mixed $value */
    public function escape($value): string
    {
        return XH_hsc($value);
    }
}
