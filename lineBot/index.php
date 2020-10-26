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

    // //テキストを返信
    // aaa($bot, $event->getReplyToken(), 'TextMessage');
    
    // //画像を返信
    // image($bot, $event->getReplyToken(), 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg', 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/view.jpg');

    // image($bot, $event->getReplyToken(), 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg');

    // //位置情報の返信
    // loca($bot, $event->getReplyToken(), 'LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473);

    // //スタンプを返信
    // sticker($bot, $event->getReplyToken(), 1, 1);

    // // 複数のメッセージをまとめて返信
    // multi($bot, $event->getReplyToken(),
    //     new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('TextMessage'),
    //     new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder('https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg', 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/view.jpg'),
    //     new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder('LINE', '東京都渋谷区渋谷2-21-1 ヒカリエ27階', 35.659025, 139.703473),
    //     new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(1, 1)
    // );

    // //Buttonテンプレートメッセージを返信
    // if($events instanceof \LINE\LINEBot\Event\PostbackEvent)
    // {
    //     replyTextMessage($bot, $event->getReplyToken(), 'Postback受信『' . $event->getPostbackData() . '』');
    //     continue;
    // }
    // button($bot, $event->getReplyToken(),
    //     'お天気お知らせ - 今日は天気予報は晴れです',
    //     'https://' . $_SERVER['HTTP_HOST'] . '/imgs/template.jpg',
    //     'お天気お知らせ',
    //     '今日は天気予報は晴れです',
    //     new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('明日の天気', 'tomorrow'),         //MessageTemplateActionBuilder:ユーザーに発現させるアクション
    //     new \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder('週末の天気', 'weekend'),         //PostbackTemplateActionBuilder:ユーザーからボットに文字列を送信するアクション
    //     new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('webで見る', 'https://google.jp')      //UriTemplateAction:URLを開かせるアクション
    // );

    //confirmテンプレートメッセージを返信
    confirm($bot, $event->getReplyToken(), 
        'webで詳しく見ますか？', 'webで詳しく見ますか？',
        new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('見る', 'https://google.jp'),
        new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('見ない', 'ignore'),
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

//Buttonsテンプレートを返す関数
function button($bot, $replyToken, $text, $imageUrl, $title, $body, ...$actions)
{
    $actionArray = array();
    foreach($actions as $value){
        array_push($actionArray, $value);
    }
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
        $text,
        new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder($title, $body, $imageUrl, $actionArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if (!$response->isSucceeded())
        {
            error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
        }    
}

//confirmテンプレートを返す関数
function confirm($bot, $replyToken, $text, $body, ...$actions)
{
    $actionArray = array();
    foreach($actions as $value)
    {
        array_push($actionArray,$value);
    }
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
        $text,
        new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder\ConfirmTemplateBuilder($body, $actionArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if (!$response->isSucceeded())
        {
            error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
        }    
}