<h1>Moved_XH</h1>
<h4><?=$this->text('syscheck');?></h4>
<ul style="list-style: none">
<?php foreach ($this->checks as $check => $state):?>
    <li>
        <img src="<?=$this->escape($this->images[$state])?>" alt="<?=$this->escape($state)?>" style="padding-right: 1em"/>
        <?=$check?>
    </li>
<?php endforeach?>
</ul>
<hr />
<h4><?=$this->text('about')?></h4>
<img src="<?=$this->logo()?>" style="float: left; margin: 1.5em 0.5em 0 0" alt="Plugin Icon">
<p>Version: <?=$this->version()?></p>
<p>Copyright &copy; 2013-2017 Christoph M. Becker</a></p>
<p style="text-align: justify">
    This program is free software: you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the Free
    Software Foundation, either version 3 of the License, or (at your option)
    any later version.
</p>
<p style="text-align: justify">
    This program is distributed in the hope that it will be useful, but
    <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
    the GNU General Public License for more details.
</p>
<p style="text-align: justify">
    You should have received a copy of the GNU General Public License along with
    this program. If not, see <a
    href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
