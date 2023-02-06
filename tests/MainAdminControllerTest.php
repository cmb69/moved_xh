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
use XH\CSRFProtection as CsrfProtector;

class MainAdminControllerTest extends TestCase
{
    /** @var MainAdminController&MockObject */
    private $sut;

    /** @var CsrfProtector&MockObject */
    private $csrfProtector;

    /** @var DbService&MockObject */
    private $dbService;

    public function setUp(): void
    {
        $plugin_tx = XH_includeVar("./languages/en.php", 'plugin_tx');
        $lang = $plugin_tx['moved'];
        $this->csrfProtector = $this->createStub(CsrfProtector::class);
        $this->dbService = $this->createStub(DbService::class);
        $this->sut = new MainAdminController("/", "./", $lang, $this->csrfProtector, $this->dbService);
    }

    public function testDefaultActionRendersEditor(): void
    {
        $this->dbService->method('readTextContent')->willReturn("foo=bar\nbaz=qux");
        $response = $this->sut->defaultAction();
        Approvals::verifyHtml($response->body());
    }

    public function testSaveActionPreventsCSRF(): void
    {
        $_POST = ['plugin_text' => "foo=bar\nbaz=qux"];
        $this->csrfProtector->expects($this->once())->method('check');
        $this->dbService->method('storeTextContent')->willReturn(true);
        $this->sut->saveAction();
    }

    public function testSaveActionStoresContent(): void
    {
        $_POST = ['plugin_text' => "foo=bar\nbaz=qux"];
        $this->dbService->expects($this->once())->method('storeTextContent')
            ->with("foo=bar" . PHP_EOL . "baz=qux")
            ->willReturn(true);
        $this->sut->saveAction();
    }

    public function testSaveActionRedirectsOnSuccess(): void
    {
        $_POST = ['plugin_text' => "foo=bar\nbaz=qux"];
        $this->dbService->method('storeTextContent')->willReturn(true);
        $response = $this->sut->saveAction();
        $this->assertEquals("http://example.com/?&moved&admin=plugin_main&action=plugin_text", $response->body());
        $this->assertEquals(303, $response->statusCode());
    }

    public function testSaveActionRendersErrorOnFailure(): void
    {
        $_POST = ['plugin_text' => "foo=bar\nbaz=qux"];
        $this->dbService->method('storeTextContent')->willReturn(false);
        $this->dbService->method('getFilename')->willReturn("./moved.csv");
        $response = $this->sut->saveAction();
        Approvals::verifyHtml($response->body());
    }
}
