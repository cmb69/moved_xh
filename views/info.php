<h1>Moved – <?=$this->text('info')?></h1>
<img class="moved_logo" src="<?=$this->logo()?>" alt="<?=$this->text('alt_logo')?>">
<p>Version: <?=$this->version()?></p>
<p>Copyright © 2013-2017 <a href="http://3-magi.net/">Christoph M. Becker</a></p>
<p class="moved_license">
    This program is free software: you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the Free
    Software Foundation, either version 3 of the License, or (at your option)
    any later version.
</p>
<p class="moved_license">
    This program is distributed in the hope that it will be useful, but
    <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
    the GNU General Public License for more details.
</p>
<p class="moved_license">
    You should have received a copy of the GNU General Public License along with
    this program. If not, see <a
    href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
<div class="moved_syscheck">
    <h2><?php echo $this->text('syscheck_title')?></h2>
<?php foreach ($this->checks as $check):?>
    <p class="xh_<?php echo $this->escape($check->state)?>"><?php echo $this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?php endforeach?>
</div>