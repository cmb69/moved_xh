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

use PHPUnit_Framework_TestCase;

class MovedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var object
     */
    private $moved;

    public function setUp()
    {
        global $pth;

        $this->moved = new Moved();
        $pth = array(
            'folder' => array(
                'content' => '../../content/',
                'plugins' => '../'
            )
        );
    }

    /**
     * @return array
     */
    public function dataForIsUtf8()
    {
        return array(
            array('foo', true),
            array("Fahrvergn\xC3\xBCgen", true),
            array("Fahrvergn\xFCgen", false)
        );
    }

    /**
     * @dataProvider dataForIsUtf8
     * @param string $string
     * @param string $expected
     */
    public function testIsUtf8($string, $expected)
    {
        $actual = $this->moved->isUtf8($string);
        $this->assertEquals($expected, $actual);
    }
}
