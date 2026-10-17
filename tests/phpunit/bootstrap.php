<?php
/**
 * PHPUnit bootstrap: loads the AbanteCart core once, before any test is started.
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'AbanteCartTestBootstrap.php';

phpunit\AbanteCartTestBootstrap::initCore();
