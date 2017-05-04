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

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('MOVED_VERSION', '@MOVED_VERSION@');

/**
 * @return object
 */
function Moved_instance()
{
    static $instance;

    if (!isset($instance)) {
        $instance = new Moved\Moved();
    }
    return $instance;
}

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
                $url = CMSIMPLE_URL . '?' . $records[$su] . $qs;
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
