<?php

$env = parse_ini_file(".env");

define ("DB_DSN", $env['SNACKER_RANK_DSN']);
define("DB_USER", $env['SNACKER_RANK_DB_USER']);
define("DB_PASS", $env['SNACKER_RANK_DB_PASS']);

