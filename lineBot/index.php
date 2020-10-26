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

        // //テキストを返信
        var_dump($event->getReplyToken());
        $bot->replyText($event->getReplyToken(), 'Textmessage');

        //テキストを返信し次のイベント処理へ
        //replyTextMessage($bot, $event->getReplyToken(), 'TextMessage');
    }

        //テキストを返信。引数はLINEbot、返信先、テキスト
        // function replyTextMessage($bot, $replyToken, $text){
            //返信を行いレスポンスを取得
            //TextMessageBuilderの引数はテキスト
            // $response = $bot->replyMessage($replyToken, new LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

            //レスポンスが異常な場合
            // if (!$response->isSucceeded()){
                //エラー内容を出力
                // error_log('Failed! '. $response->getHTTPStatus .' ' . $response->getRawBOdy());
            // }
        // }

?>
    
