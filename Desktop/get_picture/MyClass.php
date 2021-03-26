<?php

// ========================================================
// urlをセット
// ========================================================
class setURL {

 public $items; // urlを格納する変数
 public $url_root; // ルートディレクトリのパス
 public $url_dir; // 表示ページディレクトリまでのパス
 public $str = 'http'; // 検索対象の文字列をセット

 // 新規インスタンス作成時に実行される処理（コンストラクタ）
 // ---------------------------------------------------------
 function __construct($val) {
 // 引数が空でなければ処理
 if(isset($val)) {
 // urlをitemsへ追加
 $this->add_item($val);

 // urlのルートパスを取得
 $url_array = parse_url($val);
 $this->url_root = "https://generated.photos/face/joyfull-asian-child-female-with-medium-black-hair-and-brown-eyes--5e6884066d3b380006f0ff5d";

 // 表示ページディレクトリまでのパスを取得
 $url_path = pathinfo($val);

 // urlに拡張子があるかどうかで$url_dirにセットする値を変える
 $ext = $this->res_extension($val);
 if (!isset($ext)) {
 // 拡張子なし
 $this->url_dir = $val;
 } else {
 // 拡張子あり
 $this->url_dir = $url_path["dirname"]."/";
 }

 }
 }

 // ソースを読み込むためのトリガー（ココから ソース読み込み --> 画像パスセット が始まる）
 // ---------------------------------------------------------
 function get_html() {
 foreach( $this->items as $key1 => $val1 ){
 foreach( $val1 as $key2 => $val2 ){
 if ($key1==="html" || $key1==="php" || $key1==="") {
 // htmlの時の処理
 $this->type_html($val2);
 } elseif($key1==="css") {
 // cssの時の処理
 $this->type_css($val2);
 }
 }
 }
 }

 // urlが相対パスで書かれていたら絶対パスで結果を返す
 // ---------------------------------------------------------
 function checkURI($val) {
 // もし引数が対象の文字列を含んでいなかったら文字列の編集結果を返す
 if (!strstr($val, $this->str)) {
 $result = $this->url_dir.$val;
 return $result;
 } else {
 return $val;
 }
 }

 // $itemsへurlを格納
 // ---------------------------------------------------------
 function add_item($val) {
 if(!isset($this->items)) {
 // 空の時の処理
 $this->items[$this->res_extension($val)][] = $val;
 } else {
 // 空ではない時の処理
 $this->items[$this->res_extension($val)][] = $this->checkURI($val);
 }
 }

 // urlの拡張子を返す
 // ---------------------------------------------------------
 function res_extension($val) {
 $url_path = pathinfo($val);
 return $url_path["extension"];
 }

 // チェック用の処理
 // ---------------------------------------------------------
 function preview($data) {
 echo "<pre>";
 var_dump($data);
 echo "</pre><br>";
 }

}//class setURL


// ========================================================
// img-srcをセット
// ========================================================
class setImgURL extends setURL {

 // 画像url保存用の変数
 public $src;

 // 新規インスタンス作成時に実行される処理
 // ---------------------------------------------------------
 function __construct($val) {
 // 継承元のコンストラクタを実行
 parent::__construct($val);

 // html内のlink->hrefを取得して$itemsへ格納
 $html = file_get_html( $val );
 foreach($html->find('link') as $element) {
 $element = $this->checkURI($element->href);
 $this->add_item($element);
 }

 // ソースを読み込むためのトリガー
 $this->get_html();

 }

 // 画像urlを変数へ追加
 // ---------------------------------------------------------
 function add_src($val) {
 $this->src[] = $this->checkURI($val);
 }

 // 引数のソースを返す
 // ---------------------------------------------------------
 function get_data($url) {
 return file_get_html( $url );
 }

 // htmlファイルの処理（img->src）
 // ---------------------------------------------------------
 function type_html($url) {
 // 引数のソースを返す
 $data = $this->get_data($url);

 // 画像urlを特定の変数へ追加
 foreach($data->find('img') as $element) {
 $this->add_src($element->src);
 }
 }

 // cssファイルの処理（url(*)）
 // ---------------------------------------------------------
 function type_css($url) {
 // 引数のソースを返す
 $data = $this->get_data($url);

 // urlのルートパスを取得
 $url_array = parse_url($url);
 $url_root = $url_array[scheme]."://".$url_array[host]."/";

 // 表示ページディレクトリまでのパスを取得
 $url_path = pathinfo($url);
 $url_dir = $url_path["dirname"]."/";

 // スラッシュで文字列を区切る
 $parts = explode("/", $url_dir);

 // 正規表現パターンセット（「url()」のカッコの中身を取得）
 $pattern = '/url\\((.+?)\\)/';

 // パターンにマッチした文字列をすべて取得
 preg_match_all($pattern, $data , $match);

 // カッコの中身を画像urlへ追加
 foreach( $match[1] as $key => $val ){

 // parent要素の数を取得
 $dir_count = substr_count($val, "../")+1;

 // 余分な文字の置き換え削除
 $repVal = array("\"", "'", "../", "./");
 $val = str_replace($repVal, "", $val);

 // parent要素の分だけパスのディレクトリを遡る
 $path = "";
 for ($i = 0, $size = count($parts)-$dir_count; $i < $size; $i++) {
 $path .= $parts[$i]."/";
 }
 $val = $path.$val;

 // 画像urlを特定の変数へ追加
 $this->add_src($val);
 }

 }

}//class getImgURL

?>