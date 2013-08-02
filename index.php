<?php

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

define('MOVED_VERSION', '1dev1');


define('MOVED_DATA_PATH', $pth['folder']['plugin'] . 'data/data-' . basename($sl) . '.csv');
define('MOVED_LOG_PATH', $pth['folder']['plugin'] . 'data/log404.csv');

define('MOVED_URL', 'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['SERVER_NAME']
    . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT'])
    . preg_replace('/index.php$/', '', $_SERVER['SCRIPT_NAME']));


/**
 * Returns the records of moved pages as associative array,
 * <var>false</var> on failure reading the file.
 *
 * @return array
 */
function Moved_data()
{
    $lines = file(MOVED_DATA_PATH);
    if ($lines === false) {
        return false;
    }
    $records = array();
    foreach ($lines as $line) {
        if (trim($line) != '') {
            $fields = explode('->', $line);
            $key = trim($fields[0]);
            $value = isset($fields[1]) ? trim($fields[1]) : '';
            $records[$key] = $value;
        }
    }
    return $records;
}

function Moved_log404()
{
    global $su, $sl;

    $time = isset($_SERVER['REQUEST_TIME'])
        ? $_SERVER['REQUEST_TIME']
        : time();
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $line = $time . "\t" . $sl . "\t" . $su . "\t" . $referrer;
    $fp = fopen(MOVED_LOG_PATH, 'a');
    fwrite($fp, $line . PHP_EOL);
    fclose($fp);
}


function Moved_statusHeader($status)
{
    global $cgi, $iis;

    $header = ($cgi || $iis ? 'Status: ' : 'HTTP/1.1 ') . $status;
    header($header);
}

function Moved_isUtf8($str)
{
    return preg_match('/^.{1}/us', $str) == 1;
}

function custom_404()
{
    global $su, $title, $o, $plugin_tx;

    $ptx = $plugin_tx['moved'];
    $records = Moved_data();
    if (isset($records[$su])) {
        if ($records[$su]) {
            $qs = strpos($_SERVER['QUERY_STRING'], $su) === 0
                ? substr($_SERVER['QUERY_STRING'], strlen($su))
                : '';
            $url = MOVED_URL . '?' . $records[$su] . $qs;
            header('Location: ' . $url, true, 301);
            exit;
        } else {
            Moved_statusHeader('410 Gone');
            $title = $ptx['title_gone'];
            $url = urldecode($su);
            if (!Moved_isUtf8($url)) {
                $url = $su;
            }
            $desc = str_replace('{{URL}}', $url, $ptx['desc_gone']);
            $desc = htmlspecialchars($desc, ENT_COMPAT, 'UTF-8');
            $o .= '<p>' . $desc . '</p>';
        }
    } else {
        Moved_statusHeader('404 Not found');
        $title = $ptx['title_notfound'];
        $url = urldecode($su);
        if (!Moved_isUtf8($url)) {
            $url = $su;
        }
        $desc = str_replace('{{URL}}', $url, $ptx['desc_notfound']);
        $desc = htmlspecialchars($desc, ENT_COMPAT, 'UTF-8');
        $o .= '<p>' . $desc . '</p>';
        Moved_log404();
    }
}

?>
