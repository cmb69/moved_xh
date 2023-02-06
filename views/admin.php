<?php

use Moved\View;

if (!defined('CMSIMPLE_XH_VERSION')) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

/**
 * @var View $this
 * @var string $actionUrl
 * @var string $contents
 * @var string $csrfTokenInput HTML
 */
?>

<div class="moved_main">
  <h1>Moved</h1>
  <form action="<?=$this->esc($actionUrl)?>" method="post">
    <span><?=$this->raw($csrfTokenInput)?></span>
    <p>
      <textarea class="plugintextarea" name="plugin_text" cols="80" rows="25"><?=$this->esc($contents)?></textarea>
    </p>
    <p>
      <button><?=$this->text('label_save')?></button>
    </p>
  </form>
</div>
