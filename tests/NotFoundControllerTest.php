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

use Moved\Infra\DbService;
use Moved\Infra\Logger;

class NotFoundontrollerTest extends TestCase
{
    /** @var NotFoundController */
    private $sut;

    /** @var DbService&MockObject */
    private $dbService;

    /** @var Logger&MockObject */
    private $logger;

    public function setUp(): void
    {
        $plugin_tx = XH_includeVar("./languages/en.php", 'plugin_tx');
        $lang = $plugin_tx['moved'];
        $this->dbService = $this->createStub(DbService::class);
        $this->logger = $this->createStub(Logger::class);
        $this->sut = new NotFoundController("./", $lang, $this->dbService, $this->logger);
    }

    public function testDefaultActionRendersNotFoundPage(): void
    {
        $response = $this->sut->defaultAction("some-url");
        $this->assertEquals(404, $response->statusCode());
        $this->assertEquals("Page not found", $response->title());
        Approvals::verifyHtml($response->body());
    }

    public function testDefaultActionLogsUnhandled404(): void
    {
        $_SERVER['HTTP_REFERER'] = "http://example.com/";
        $this->logger->expects($this->once())->method('logNotFound')->with(
            $this->equalTo("some-url"),
            $this->equalTo("http://example.com/")
        );
        $this->sut->defaultAction("some-url");
    }

    public function testDefaultActionRendersGonePage(): void
    {
        $this->dbService->method('findRedirectFor')->willReturn("");
        $response = $this->sut->defaultAction("some-url");
        $this->assertEquals(410, $response->statusCode());
        $this->assertEquals("Page is gone", $response->title());
        Approvals::verifyHtml($response->body());
    }

    public function testDefaultActionRedirectsInternally(): void
    {
        $_SERVER['QUERY_STRING'] = "some-url";
        $this->dbService->method('findRedirectFor')->willReturn("other-url");
        $response = $this->sut->defaultAction("some-url");
        $this->assertEquals(301, $response->statusCode());
        $this->assertEquals("http://example.com/?other-url", $response->body());
    }

    public function testDefaultActionRedirectsExternally(): void
    {
        $_SERVER['QUERY_STRING'] = "some-url";
        $this->dbService->method('findRedirectFor')->willReturn("http://example.com/other-url");
        $response = $this->sut->defaultAction("some-url");
        $this->assertEquals(301, $response->statusCode());
        $this->assertEquals("http://example.com/other-url", $response->body());
    }
}
