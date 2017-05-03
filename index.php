<?php

/**
 * Front-end functionality of Moved_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Moved
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2017 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Moved_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The plugin version.
 */
define('MOVED_VERSION', '1beta2');

/**
 * The fully qualified base URL for redirections.
 */
define(
    'MOVED_URL',
    'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['HTTP_HOST'] . preg_replace('/index.php$/', '', $sn)
);

/**
 * Returns the unique instance of the Moved class.
 *
 * @return object
 *
 * @global array The paths of system files and folders.
 */
function Moved_instance()
{
    global $pth;
    static $instance;

    include_once $pth['folder']['plugins'] . 'moved/classes/Moved.php';
    if (!isset($instance)) {
        $instance = new Moved();
    }
    return $instance;
}

/**
 * Hook function for not existing pages. Redirects to new page or gives
 * appropriate response.
 *
 * @return void
 *
 * @global array  The paths of system files and folders.
 * @global string The URL of the requested page.
 * @global string The title of the page.
 * @global string The (X)HTML output of the contents area.
 * @global array  The localization of the plugins.
 */
function custom_404()
{
    global $pth, $su, $title, $o, $plugin_tx;

    $moved = Moved_instance();
    $ptx = $plugin_tx['moved'];
    $records = $moved->data();
    if (isset($records[$su])) {
        if ($records[$su]) {
            $parts = parse_url($records[$su]);
            if (isset($parts['scheme'])) {
                $url = $records[$su];
            } else {
                $qs = strpos($_SERVER['QUERY_STRING'], $su) === 0
                    ? substr($_SERVER['QUERY_STRING'], strlen($su))
                    : '';
                $url = MOVED_URL . '?' . $records[$su] . $qs;
            }
            header('Location: ' . $url, true, 301);
            exit;
        } else {
            $moved->statusHeader('410 Gone');
            $title = $ptx['title_gone'];
            $url = urldecode($su);
            if (!$moved->isUtf8($url)) {
                $url = $su;
            }
            $desc = str_replace('{{URL}}', $url, $ptx['desc_gone']);
            $desc = htmlspecialchars($desc, ENT_COMPAT, 'UTF-8');
            $o .= '<p>' . $desc . '</p>';
        }
    } else {
        $moved->statusHeader('404 Not found');
        $title = $ptx['title_notfound'];
        $url = urldecode($su);
        if (!$moved->isUtf8($url)) {
            $url = $su;
        }
        $desc = str_replace('{{URL}}', $url, $ptx['desc_notfound']);
        $desc = htmlspecialchars($desc, ENT_COMPAT, 'UTF-8');
        $o .= '<p>' . $desc . '</p>';
        $moved->log404();
    }
}

?>
