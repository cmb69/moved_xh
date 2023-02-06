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
 * @var string $csrfTokenInput
 */
?>

<div class="moved_main">
  <h1>Moved</h1>
  <form action="<?=$actionUrl?>" method="post">
    <?=$csrfTokenInput?>
    <p>
      <textarea class="plugintextarea" name="plugin_text" cols="80" rows="25"><?=$contents?></textarea>
    </p>
    <p>
      <button><?=$this->text('label_save')?></button>
    </p>
  </form>
</div>
