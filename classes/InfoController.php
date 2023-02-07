<?php

/**
 * Copyright 2013-2023 Christoph M. Becker
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

namespace Moved;

use Moved\Infra\SystemChecker;

class InfoController
{
    /** @var string */
    private $baseFolder;

    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $lang;

    /** @var SystemChecker */
    private $systemChecker;

    /** @param array<string,string> $lang */
    public function __construct(string $baseFolder, string $pluginFolder, array $lang, SystemChecker $systemChecker)
    {
        $this->baseFolder = $baseFolder;
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->systemChecker = $systemChecker;
    }

    public function defaultAction(): string
    {
        $view = new View("{$this->pluginFolder}views/", $this->lang);
        return $view->render('info', [
            'checks' => [
                $this->checkPhpVersion('7.1.0'),
                $this->checkXhVersion('1.7.0'),
                $this->checkWritability("{$this->pluginFolder}css/"),
                $this->checkWritability("{$this->pluginFolder}languages/"),
                $this->checkCustom404(),
            ],
            'logo' => "{$this->pluginFolder}moved.png",
            'version' => MOVED_VERSION
        ]);
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkPhpVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(PHP_VERSION, $version) ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_phpversion'], $version),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkXhVersion(string $version): array
    {
        $state = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version") ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_xhversion'], $version),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkWritability(string $folder): array
    {
        $state = $this->systemChecker->checkWritability($folder) ? 'success' : 'warning';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_writable'], $folder),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }

    /** @return array{class:string,label:string,stateLabel:string} */
    private function checkCustom404(): array
    {
        $filename = $this->systemChecker->checkCustom404($this->baseFolder);
        $state = $filename === "plugins/moved/index.php" ? 'success' : 'fail';
        return [
            'class' => "xh_$state",
            'label' => sprintf($this->lang['syscheck_custom_404'], "{$this->baseFolder}{$filename}"),
            'stateLabel' => $this->lang["syscheck_$state"],
        ];
    }
}
