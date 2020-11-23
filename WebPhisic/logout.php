<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　ログアウト画面　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

// ブラウザのセッション変数削除
$_SESSION = array();
// ブラウザのセッションIDを破棄
if(isset($_COOKIE[session_name()])){
    setcookie(session_name(), '', time()-42000, '/');
}

// サーバーのセッション削除
session_destroy();
header("Location:login.php");

debug(' 「　画面表示終了　」');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');