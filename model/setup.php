<?php

require_once './model/file.php';
require_once './model/parse.php';
$view = isset($_GET['view']) ? $_GET['view'] : 'pg9.3';

$list = [
    'pg9.3' => 'pg-9.3.5',
    'MySQL' => 'my',
    'MariaDB' => 'maria',
    'pg9.4' => 'pg-9.4.0',
];
$path = isset($list["$view"]) ? $list["$view"] : 'pg-9.3.5';
$file = new file($path);
$parse = new parse($file);
$parse_delete_date = $parse->get();



