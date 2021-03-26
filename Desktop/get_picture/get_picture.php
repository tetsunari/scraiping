<?php

require_once 'simple_html_dom.php';

$url = 'https://bigakusei.com/binan-bijo/25483/';
$url_1 = 'https://bigakusei.com/binan-bijo/25473/';
$url_2 = 'https://bigakusei.com/binan-bijo/25462/';
$url_3 = 'https://bigakusei.com/binan-bijo/25453/';
$url_4 = 'https://bigakusei.com/binan-bijo/25448/';

$html = file_get_html($url);
$html_1 = file_get_html($url_1);
$html_2 = file_get_html($url_2);
$html_3 = file_get_html($url_3);
$html_4 = file_get_html($url_4);

$image_path = $html->find('.flexslider img');
$image_path_1 = $html_1->find('.flexslider img');
$image_path_2 = $html_2->find('.flexslider img');
$image_path_3 = $html_3->find('.flexslider img');
$image_path_4 = $html_4->find('.flexslider img');
// $image_path = $html->find('.photo_slide_0 img');
// $image_path = $html->find('.bx-viewport img');

// echo count($image_path);
// exit;
$value = [];
$value_1 = [];
$value_2 = [];
$value_3 = [];
$value_4 = [];
$image = [];
$image_1 = [];
$image_2 = [];
$image_3 = [];
$image_4 = [];

foreach ($image_path as $item) {
//    $value[] = $item->href;
   $value[] = $item->src;
//    $value[] = $item->getAttribute('data-src');
}
foreach ($image_path_1 as $item) {
//    $value[] = $item->href;
   $value_1[] = $item->src;
//    $value[] = $item->getAttribute('data-src');
}
foreach ($image_path_2 as $item) {
//    $value[] = $item->href;
   $value_2[] = $item->src;
//    $value[] = $item->getAttribute('data-src');
}
foreach ($image_path_3 as $item) {
//    $value[] = $item->href;
   $value_3[] = $item->src;
//    $value[] = $item->getAttribute('data-src');
}
foreach ($image_path_4 as $item) {
//    $value[] = $item->href;
   $value_4[] = $item->src;
//    $value[] = $item->getAttribute('data-src');
}

foreach ($value as $images) {
    $image[] = file_get_contents($images);
}
foreach ($value_1 as $images) {
    $image_1[] = file_get_contents($images);
}
foreach ($value_2 as $images) {
    $image_2[] = file_get_contents($images);
}
foreach ($value_3 as $images) {
    $image_3[] = file_get_contents($images);
}
foreach ($value_4 as $images) {
    $image_4[] = file_get_contents($images);
}

$count = 1;
foreach ($image as $face) {
    $file_name = '/Users/lettuce/Desktop/a/' .$count. '.jpg';
    file_put_contents($file_name, $face);
    $count++;
}
$count = 11;
foreach ($image_1 as $face) {
    $file_name = '/Users/lettuce/Desktop/a/' .$count. '.jpg';
    file_put_contents($file_name, $face);
    $count++;
}
$count = 21;
foreach ($image_2 as $face) {
    $file_name = '/Users/lettuce/Desktop/a/' .$count. '.jpg';
    file_put_contents($file_name, $face);
    $count++;
}
$count = 31;
foreach ($image_3 as $face) {
    $file_name = '/Users/lettuce/Desktop/a/' .$count. '.jpg';
    file_put_contents($file_name, $face);
    $count++;
}
$count = 41;
foreach ($image_4 as $face) {
    $file_name = '/Users/lettuce/Desktop/a/' .$count. '.jpg';
    file_put_contents($file_name, $face);
    $count++;
}

// $count = 229;
// foreach ($image as $face) {
//     $file_name = '/Users/lettuce/Desktop/bigakusei/' .$count. '.jpg';
//     file_put_contents($file_name, $face);
//     $count++;
// }
