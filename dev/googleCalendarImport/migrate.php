#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = new LWK\GoogleCalendarImport\MigrateGoogle(
    realpath(__DIR__)
);

$status = $app->handle();

exit($status);
