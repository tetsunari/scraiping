<?php
// Composerでインストールしたライブラリを一括読み込み
require_once __DIR__ . '/vendor/autoload.php';

// アクセストークンを使いCurlHTTPClientをインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

//CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

// LINE Messaging APIがリクエストに付与した署名を取得
$signature = $_SERVER["HTTP_" . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

//署名をチェックし、正当であればリクエストをパースし配列へ、不正であれば例外処理
try {
    $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
  } catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
    error_log('parseEventRequest failed. InvalidSignatureException => '.var_export($e, true));
  } catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
    error_log('parseEventRequest failed. UnknownEventTypeException => '.var_export($e, true));
  } catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
    error_log('parseEventRequest failed. UnknownMessageTypeException => '.var_export($e, true));
  } catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
    error_log('parseEventRequest failed. InvalidEventRequestException => '.var_export($e, true));
  }
    
//配列に格納された各イベントをループで処理
foreach ($events as $event) {
    // MessageEvent型でなければ処理をスキップ
    if (!($event instanceof \LINE\LINEBot\Event\MessageEvent))
    {
        error_log('Non message event has cone');
        continue;
    }
      // TextMessage型でなければ処理をスキップ
    if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage))
    {
        error_log('Non message event has cone');
        continue;
    }

    ////おうむ返し
    //$bot->replyText($event->getREplyToken(), $event->getText());
    // }

    //ゲーム開始時の石の配置
    $stones =   
    [
    [0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 1, 2, 0, 0, 0],
    [0, 0, 0, 2, 1, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 0, 0],
    [0, 0, 0, 0, 0, 0, 0, 0],
    ];

    //Imagemapを返信
    replyImagemap($bot, $event->getReplyToken(), '石盤', $stones);


    //石盤のImagemapを返信
    function replyImagemap($bot, $replyToken, $alternativeText, $stones)
    {
        //アクションの配列
        $actionArray = array();
        //１つ以上のエリアが必要なためダミーのタップ可能エリアを追加
        array_push($actionArray, new \LINE\LINEBot\ImagemapActioonBuilder\ImagemapMessageActioonBuilder(
            '-',
            new \LINE\LINEBot\ImagemapActioonBuilder\AreaBuilder(0, 0, 1, 1)));
        
        //ImagemapMessageBuilderの引数は画像のURL、代替テキスト
        //基本比率サイズ(幅は1040固定)、アクションの配列
        $ImagemapMessageBuilder = new LINE\LINEBot\ImagemapActioonBuilder(
            'https://' . $_SERVER['HTTP_HOST'] . '\images' . urlencode(json_encode($stones)) . '/' . uniqid(),
            $alternativeText,
            new \LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder(1040, 1040),
            $actionArray
        );

        $response = $bot->replyMessage($replyToken, $ImagemapMessageBuilder);
        if (!$response->isSucceeded())
            {
                error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
            }      
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


?>
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
            new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($body, $actionArray)
        );
        $response = $bot->replyMessage($replyToken, $builder);
        if (!$response->isSucceeded())
            {
                error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
            }    
    }

    //carouselテンプレートを返す関数
    function carousel($bot, $replyToken, $text, $columnArray)
    {
        $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
            $text,
            new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columnArray)
        );
        $response = $bot->replyMessage($replyToken, $builder);
        if (!$response->isSucceeded())
            {
                error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
            }    
    }
}

?>
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

    // //confirmテンプレートメッセージを返信
    // confirm($bot, $event->getReplyToken(), 
    //     'webで詳しく見ますか？', 'webで詳しく見ますか？',
    //     new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('見る', 'https://google.jp'),
    //     new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('見ない', 'ignore'),
    // );

    //carouselテンプレートメッセージを返信
    // $columnArray = array();
    // for($i = 0; $i < 5; $i++)
    // {
    //     $actionArray = array();
    //     array_push($actionArray, new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('ボタン' . $i . '-' . 1, 'c-' . $i . '-' . 1));
    //     array_push($actionArray, new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('ボタン' . $i . '-' . 2, 'c-' . $i . '-' . 2));
    //     array_push($actionArray, new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('ボタン' . $i . '-' . 3, 'c-' . $i . '-' . 3));
    //     $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder( ($i + 1) . '日後の天気', '晴れ', 'https://' . $_SERVER['HTTP_HOST'] . '/imgs/template.jpg', $actionArray);
    //     array_push($columnArray, $column);
    // }
    // carousel($bot, $event->getReplyToken(), '今後の天気予報', $columnArray);


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
        new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder($body, $actionArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if (!$response->isSucceeded())
        {
            error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
        }    
}

//carouselテンプレートを返す関数
function carousel($bot, $replyToken, $text, $columnArray)
{
    $builder = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder(
        $text,
        new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder($columnArray)
    );
    $response = $bot->replyMessage($replyToken, $builder);
    if (!$response->isSucceeded())
        {
            error_log('Failed! '. $response->getHTTPStatus . ' ' . $response->getRawBody());
        }    
}