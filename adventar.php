<?php

// コマンドラインのみ
if (php_sapi_name() != 'cli') {
    exit('Service Unavailable');
}

require_once 'goutte.phar';

use Goutte\Client;

$client = new Client();

// Adventarの2015年一覧ページをGET
$crawler = $client->request('GET', 'http://www.adventar.org/calendars?year=2015');

// カレンダーリスト部分を取得
$dom = $crawler->filter('div.mod-calendarList ul li');

$dom->each(function ($node) use (&$records) {
    // タイトル
    $name = $node->filter('div.mod-calendarList-title')->text();
    // URL
    $url = $node->filter('div.mod-calendarList-title a')->attr('href');
    // 背景色
    $style = $node->filter('div.mod-calendarList-title a')->attr('style');
    // 登録件数
    $count = $node->filter('div.mod-calendarList-body div.mod-calendarIndicator div.mod-calendarIndicator-value')->attr('data-count');

    $records[] = array(
        'name' => trim($name),
        'url' => $url,
        'style' => trim(str_replace('background:', '', $style)),
        'count' => $count,
    );
});

$records = array(
    'name' => 'adventar',
    'children' => $records
);

// json出力
file_put_contents('adventar.json', json_encode($records));

