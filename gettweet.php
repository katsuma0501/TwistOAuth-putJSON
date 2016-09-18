<?php
// TwistOAuthを読み込む
require_once './TwistOAuth/TwistOAuth.php';
require_once './TwistOAuth/TwistException.php';
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// TwistOAuthの各種キー
$ck = 'YOUR_CONSUMER_KEY';
$cs = 'YOUR_CONSUMER_SECRET';
$at = 'YOUR_ACCESS_TOKEN';
$as = 'YOUR_ACCESS_TOKEN_SECRET';

// 取得するツイート数
$get_count = '2';

try{
	$to = new TwistOAuth($ck, $cs, $at, $as);
	$statuses = $to->get('statuses/user_timeline', ['count' => $get_count]);
}catch(TwistException $e){
	$error = $e->getMessage();
	exit;
}

// 日付と本文を取得
// ついでに本文中にあるURLをaタグに置き換え
foreach ($statuses as $key => $value) {
	$create_at = date('n月j日', strtotime($value->created_at) );
	$text = htmlspecialchars($value->text,ENT_QUOTES,"utf-8");
	$text = nl2br($text);
	$text = mb_ereg_replace("(https?|ftp)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\\1\\2">\\1\\2</a>' , $text);
	$data = array(
		'create_at' => $create_at,
		'text'		=> $text,
		);
	$tweetdatas[] = $data;
}

// jsonファイル吐き出し先
$jsonfilepath = "./tweet.json";
// open -> write -> close
if (!file_exists($jsonfilepath)) {
	touch($jsonfilepath);
}
$fp = fopen($jsonfilepath,'w');
fwrite($fp, sprintf(json_encode($tweetdatas, JSON_PRETTY_PRINT)));
fclose($fp);

?>