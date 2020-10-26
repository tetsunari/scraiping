<?php
// Composerでインストールしたライブラリを一括読み込み
require_once __DIR__ . '/vendor/autoload.php';

//POSTメソッドで渡される値を取得、表示
// $inputString = file_get_contents('php://input');
// error_log($inputString);

// アクセストークンを使いCurlHTTPClientをインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

//CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

// LINE Messaging APIがリクエストに付与した署名を取得
$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

//署名をチェックし、正当であればリクエストをパースし配列へ、不正であれば例外処理
$events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

foreach ($events as $event) {
    //メッセージを返信
    // $response = $bot->replyMessage(
    //     $event->getReplyToken(), new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($event->getText())  
    // );
    // error_log($response->getRawBody());

    //テキストを返信
    // error_log($bot->replyText($event->getReplyToken(), 'Textmessage'));
    // $bot->replyText($event->getReplyToken(), $event->getText());
    // $bot->replyText($event->getReplyToken(), 'TextMassage');

    //テキストを返信
    // aaa($bot, $event->getReplyToken(), 'TextMessage');
    
    //画像を返信
    // image($bot, $event->getReplyToken(), 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg', 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/view.jpg');

    // image($bot, $event->getReplyToken(), 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg');

    //位置情報の返信
    // loca($bot, $event->getReplyToken(), 'LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473);

    //スタンプを返信
    // sticker($bot, $event->getReplyToken(), 1, 1);

    //複数のメッセージをまとめて返信
    // multi($bot, $event->getReplyToken(),
    //     new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('TextMessage'),
    //     new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder('https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg', 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/view.jpg'),
    //     new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder('LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473),
    //     new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 1)
    // );

    //複数のメッセージをまとめて返信
    $aaa = ('TextMessage');
    $image = ('https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg');
    $image2 = ('https://' . $_SERVER['HTTP_HOST'] . '/imgs/view.jpg');
    // $loca = ('LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473);
    // $sticker = (1, 1);
    multi($bot, $event->getReplyToken(),
        new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($this->aaa),
        new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($this->image, $this->image2),
        new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder('LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473),
        new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 1)
    );
}

//’TextMessage’を返す関数
function aaa($bot, $replyToken, $text)
{
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

    if (!$response->isSucceeded())
        {
            //エラー内容を出力
            error_log('Failed! '. $response->getHTTPStatus .' ' . $response->getRawBody());
        }    
}

//画像を返す関数
function image($bot, $replyToken, $originalImageUrl, $viewImageUrl)
{
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl, $viewImageUrl));

    if (!$response->isSucceeded())
        {
            //エラー内容を出力
            error_log('Failed! '. $response->getHTTPStatus .' ' . $response->getRawBody());
        }    
}

//viewの画像を引数として与えないと表示できない
// function image($bot, $replyToken, $originalImageUrl)
// {
//     $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($originalImageUrl));

//     if (!$response->isSucceeded())
//         {
//             //エラー内容を出力
//             error_log('Failed! '. $response->getHTTPStatus .' ' . $response->getRawBody());
//         }    
// }

//位置情報を返す関数
function loca($bot, $replyToken, $title, $address, $lat, $lon)
{
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder($title, $address, $lat, $lon));
    if (!$response->isSucceeded())
        {
            error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
        }    
}

//スタンプを返す関数
function sticker($bot, $replyToken, $packageID, $stickerID)
{
    $response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder($packageID, $stickerID));
    if (!$response->isSucceeded())
        {
            error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
        }    
}

//複数のメッセージをまとめて返す関数
function multi($bot, $replyToken, ...$msgs)
{
    $builder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
    foreach($msgs as $value)
    {
        $builder->add($value);
    }
    $response = $bot->replyMessage($replyToken, $builder);
    if (!$response->isSucceeded())
        {
            error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
        }    
}