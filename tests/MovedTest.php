<?php

/**
 * Testing the Moved class.
 *
 * PHP versions 4 and 5
 *
 * @category  Testing
 * @package   Moved
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Moved_XH
 */

/**
 * The class under test.
 */
require_once './classes/Moved.php';

/**
 * A class for testing the Moved class.
 *
 * @category Testing
 * @package  Moved
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Moved_XH
 */
class MovedTest extends PHPUnit_Framework_TestCase
{
    /**
     * The test instance.
     *
     * @var object
     */
    private $moved;

    /**
     * Set up the next test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->moved = new Moved();
    }

    /**
     * Test data for the Moved::isUtf8() method.
     *
     * @return array
     */
    public function dataForIsUtf8()
    {
        return array(
            array('foo', true)
        );
    }

    /**
     * Testing the Moved::isUtf8() method.
     *
     * @param string $string   A string.
     * @param string $expected Expected result.
     *
     * @return void
     *
     * @dataProvider dataForIsUtf8
     */
    public function testIsUtf8($string, $expected)
    {
        $actual = $this->moved->isUtf8($string);
        $this->assertEquals($expected, $actual);
    }
}

?>
