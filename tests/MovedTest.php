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
     *
     * @global array The paths of system files and folders.
     */
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
     * Test data for the Moved::isUtf8() method.
     *
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

    /**
     * Testing Moved::successIcons().
     *
     * @return void
     */
    public function testSuccessIcons()
    {
        $icons = $this->moved->successIcons();
        $this->assertCount(3, $icons);
        foreach ($icons as $icon) {
            $this->assertTrue(is_file($icon));
        }
    }

    /**
     * Testing Moved::writableFolders().
     *
     * @return void
     */
    public function testWritableFolders()
    {
        $folders = $this->moved->writableFolders();
        $this->assertCount(2, $folders);
        foreach ($folders as $folder) {
            $this->assertTrue(is_dir($folder));
        }
    }
}

?>
