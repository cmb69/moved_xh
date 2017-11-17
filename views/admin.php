<div class="moved_main">
    <h1>Moved</h1>
    <form action="<?=$actionUrl?>" method="post">
        <input type="hidden" name="admin" value="plugin_main">
        <input type="hidden" name="action" value="plugin_textsave">
        <?=$csrfTokenInput?>
        <p>
            <textarea class="plugintextarea" name="plugin_text" cols="80" rows="25"><?=$contents?></textarea>
        </p>
        <p>
            <button><?=$this->text('label_save')?></button>
        </p>
    </form>
</div>
