<?php

/**
 * Copyright 2023 Christoph M. Becker
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

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;

class InfoControllerTest extends TestCase
{
    public function testDefaultActionRendersPluginInfo(): void
    {
        $plugin_tx = XH_includeVar("./languages/en.php", 'plugin_tx');
        $lang = $plugin_tx['moved'];
        $systemChecker = $this->createStub(SystemChecker::class);
        $systemChecker->method('checkVersion')->willReturn(true);
        $systemChecker->method('checkWritability')->willReturn(true);
        $sut = new InfoController("./", $lang, $systemChecker);
        $response = $sut->defaultAction();
        Approvals::verifyHtml($response);
    }
}
