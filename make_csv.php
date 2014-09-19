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
ksort($parse_delete_date);
$fp = fopen('php://temp', 'r+b');
fputcsv($fp, $fields);
$header[] = "全実行時間(sec.)";
$header[] = "r";
$header[] = "b";
$header[] = "buff";
$header[] = "cache";
$header[] = "bi";
$header[] = "bo";
$header[] = "in";
$header[] = "cs";
$header[] = "us";
$header[] = "sy";
$header[] = "id";
$header[] = "wa";

fputcsv($fp, $header);
foreach ($parse_delete_date as $file_name => $value) {
    unset($fields);
    $fields[] = $file_name;
    $fields[] = $value['out_parse_data']["全実行時間(sec.):"];
    $fields[] = $value['vmstat_parse_data']['r'];
    $fields[] = $value['vmstat_parse_data']['b'];
    $fields[] = $value['vmstat_parse_data']['buff'];
    $fields[] = $value['vmstat_parse_data']['cache'];
    $fields[] = $value['vmstat_parse_data']['bi'];
    $fields[] = $value['vmstat_parse_data']['bo'];
    $fields[] = $value['vmstat_parse_data']['in'];
    $fields[] = $value['vmstat_parse_data']['cs'];
    $fields[] = $value['vmstat_parse_data']['us'];
    $fields[] = $value['vmstat_parse_data']['sy'];
    $fields[] = $value['vmstat_parse_data']['id'];
    $fields[] = $value['vmstat_parse_data']['wa'];
    fputcsv($fp, $fields);
}
rewind($fp);
$tmp = str_replace(PHP_EOL, "\r\n", stream_get_contents($fp));
file_put_contents("csv/{$path}.csv", $tmp);
