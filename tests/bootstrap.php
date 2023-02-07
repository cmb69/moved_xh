<?php

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";
const CMSIMPLE_URL = "http://example.com/";
const MOVED_VERSION = "1.1-dev";

require_once "../../cmsimple/classes/CSRFProtection.php";
require_once "../../cmsimple/functions.php";

require_once "./classes/infra/DbService.php";
require_once "./classes/infra/Logger.php";
require_once "./classes/infra/Response.php";
require_once "./classes/infra/SystemChecker.php";
require_once "./classes/InfoController.php";
require_once "./classes/MainAdminController.php";
require_once "./classes/NotFoundController.php";
require_once "./classes/View.php";
