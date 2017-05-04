<!-- Moved_XH: admin -->
<div class="pluginedit">
    <form action="<?=$this->actionUrl()?>" method="post">
        <div class="plugineditcaption">Moved</div>
        <input type="hidden" name="admin" value="plugin_main">
        <input type="hidden" name="action" value="plugin_textsave">
        <textarea class="plugintextarea" name="plugin_text" cols="80" rows="25"><?=$this->contents()?></textarea>
        <br>
        <input type="submit" class="submit" value="<?=$this->saveLabel()?>">
    </form>
</div>
