<?php

ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);

require 'vendor/autoload.php';

require 'surah_ayah_count.php';

use DiDom\Document;

$document = new Document('https://read.tafsir.one/almukhtasar#pg_-1', true);

$divs = $document->find('#pages-cont > div');

$saArr = [];

foreach($divs as $div) {

    $saArrkey = explode(',', $div->getAttribute('data-sa') )[0];
    $saArrValue = explode(',', $div->getAttribute('data-sa') )[1];

    $saArr[] = [$saArrkey, $saArrValue];
}

$file = 'result.json';

$mainUrl = 'https://read.tafsir.one/get.php?uth&src=almukhtasar';

// https://read.tafsir.one/get.php?uth&src=almukhtasar&s=2&a=6

$saObj = [];

for ($i=0; $i < count( $saArr ); $i++) {

    $surah = $saArr[$i][0];

    $fromAyah =  1*$saArr[$i][1];

    $pageUrl = $mainUrl . '&s=' . $surah . '&a=' . $fromAyah;

    $page = file_get_contents($pageUrl);

    $pageJson = json_decode($page);

    $pageJsonData = $pageJson->data;

    $pageJsonData =  str_replace('\n', PHP_EOL, $pageJsonData);

    if( $surah == 114 || $saArr[$i+1][1] == 1){
        $toAyah = surah_ayah_count( $saArr[$i][0]);
    } else {
        $toAyah = $saArr[$i+1][1] - 1;
    }

    // echo $surah . ': ' . $fromAyah . '-' . $toAyah . PHP_EOL;

    $saObj[$surah][] = ['from:' => $fromAyah, 'to:' => $toAyah, 'text:' => $pageJsonData];
}

$result = json_encode($saObj, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

file_put_contents($file, $result);