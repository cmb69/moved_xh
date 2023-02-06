<?php

use Moved\View;

if (!defined('CMSIMPLE_XH_VERSION')) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

/**
 * @var View $this
 * @var string $url
 */
?>

<p><?=$this->text('desc_notfound', $url)?></p>
