<?php
// =====================================
// ログ
// =====================================
ini_set('log_errors', 'on');
ini_set('error_log', 'error.log');

// =====================================
// デバッグ
// =====================================
$debug_flg = false;
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ:'.$str);
    }
}

// =====================================
// セッション準備・セッション有効期限を延ばす
// =====================================
// デフォルトだと、24分でセッションが削除されてしまうので、置き場所変更
session_save_path("/var/tmp/");
// ガーベージコレクションが削除するセッションの有効期限を設定（30日に設定）
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
// ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
// 上の設定は、session_start()の前に書かないといけない。キャッシュなどのヘッダー情報が送信される。
session_start();
// セッションIDを再発行
session_regenerate_id();

// =====================================
// 定数
// =====================================
define('MSG_EMPTY', '入力必須です。');
define('MSG_EMAIL', 'Email形式で入力してください。');
define('MSG_EMAILDUP', 'このEmailは既に登録されています。');
define('MSG_MIN', '6文字以上入力してください。');
define('MSG_MAX', '最大文字数を超えています。');
define('MSG_HALFENG', '半角英数字で入力してください。');
define('MSG_NOMATCH', 'パスワードとパスワード（再入力）が合っていません。');
define('MSG_NEWMATCH', '新しいパスワードに変更してください。');
define('MSG_PASSMATCH', 'パスワードが合っていません。');
define('MSG_WAIT', 'エラーが発生しました。しばらく経ってから、やり直してください。');
define('MSG_NOLOGIN', 'Emailもしくは、パスワードが一致しません。');
define('MSG_NODATA', '本日の体調管理データは登録済です。変更したい場合、マイページから編集ボタンを押してください。');

// =====================================
// グローバル関数
// =====================================
$err_msg = array();

// =====================================
// バリデーション関数
// =====================================
// 空欄でないか判定
function validRequired($str, $key){
    global $err_msg;
    if($str === ''){
        $err_msg[$key] = MSG_EMPTY;
    }
}
// Email形式化どうか判定
function validEmail($str, $key){
    global $err_msg;
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        $err_msg[$key] = MSG_EMAIL;
    }
}
// Email重複チェック
function validEmailDup($str, $key){
    global $err_msg;
    $dbh = dbConnect();
    $sql = 'SELECT id FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $str);
    $stmt = queryPost($dbh, $sql, $data);
    $rst = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($rst)){
        $err_msg[$key] = MSG_EMAILDUP;
    }else{
        return false;
    }
}
// Emailのバリデーションをまとめる
function validationEmail($email, $key){
    global $err_msg;
    validRequired($email, 'email');
    if(empty($err_msg['email'])){
        // 最大文字数かどうか
        validMax($email, 'email');
    }
    if(empty($err_msg['email'])){
        // メール形式かどうか
        validEmail($email, 'email');
    }
}
// パスワードのバリデーションをまとめる
function validationPass($pass, $key){
    global $err_msg;
    if(empty($err_msg[$key])){
        // 最小文字数チェック
        validMin($pass, $key);
    }
    if(empty($err_msg[$key])){
        // 半角英数字チェック
        validHalf($pass, $key);
    }
}
// パスワードバーリファイ
function validPassVerify($pass, $pass_hash, $key, $pass_flg = false){
    global $err_msg;
    if(!password_verify($pass, $pass_hash) && $pass_flg === false){
        $err_msg[$key] = MSG_PASSMATCH;
    }else if(password_verify($pass, $pass_hash) && $pass_flg === true){
        $err_msg[$key] = MSG_NEWMATCH;
    }
}
// 最小文字数以上かどうか判定
function validMin($str, $key, $min = 6){
    global $err_msg;
    if(mb_strlen($str) < $min){
        $err_msg[$key] = MSG_MIN;
    }
}
// 最大文字数以内かどうか判定
function validMax($str, $key, $max = 256){
    global $err_msg;
    if($max <= mb_strlen($str)){
        $err_msg[$key] = MSG_MAX;
    }
}
// 半角英数字かどうか判定
function validHalf($str, $key){
    global $err_msg;
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        $err_msg[$key] = MSG_HALFENG;
    }
}
// 値がマッチしているか判定
function validMatch($str, $str2, $key){
    global $err_msg;
    if($str !== $str2){
        $err_msg[$key] = MSG_NOMATCH;
    }
}
// すでに体調登録しているかどうか
function validDate($date, $u_id){
    global $err_msg;
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM health WHERE date = :date AND u_id = :u_id';
        $data = array(':date' => $date, ':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);

        if($rst){
            $err_msg['common'] = MSG_NODATA;
        }else{
            return false;
        }

    } catch ( Exception $e ){
        error_log('エラー発生：' . print_r($e->getMessage()));
        $err_msg['common'] = MSG_WAIT;
    }
}
// =====================================
// 画面表示関数
// =====================================
// エラーを表示する
function showErr($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
// POSTされたデータを表示
function showValue($str){
    if(!empty($_POST[$str])){
        return sanitize($_POST[$str]);
    }
}
// DBもしくはPOSTされたデータを表示させる
function showDbPostData($str){
    global $dbFormData;
    // POSTされているかどうか
    if(!empty($_POST[$str])){
        return sanitize($_POST[$str]);
    }else if(!empty($dbFormData[$str])){
        return sanitize($dbFormData[$str]);
    }
}
// DB、POSTされたファイルを表示
function showFile($str){
    global $dbFormData;
    if(!empty($_FILES[$str])){
        return sanitize($_FILES[$str]);
    }else if(!empty($dbFormData[$str])){
        return sanitize($dbFormData[$str]);
    }
}
// エラーが合った場合のデザイン
function validErr($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return 'err';
    }
}

// =====================================
// データベース
// =====================================
function dbConnect(){
    // DB接続準備
    $dsn = 'mysql:dbname=management;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}
// クエリー実行関数
function queryPost($dbh, $sql, $data){
    // クエリ作成
    $stmt = $dbh->prepare($sql);
    // SQL実行
    if(!$stmt->execute($data)){
        debug('クエリ失敗しました。');
        debug('失敗したSQL:'.print_r($stmt, true));
        $err_msg['common'] = MSG_WAIT;
        return 0;
    }
    debug('クエリ成功しました。');
    return $stmt;
}
// ユーザー情報を取得
function getUserInfo($u_id){
    try {
        // DB接続
        $dbh = dbConnect();
        $sql = 'SELECT name, email, height, pic FROM users WHERE id = :u_id';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            debug('クエリ成功しました。');
            $rst = $stmt->fetch(PDO::FETCH_ASSOC);
            return $rst;
        }

    } catch ( Exception $e ){
        error_log('エラー発生；' . print_r($e->getMessage()));
        $err_msg['common'] = MSG_WAIT;
    }
}
// パスワードを取得
function getPass($u_id){
    try {
        $dbh = dbConnect();
        $sql = 'SELECT `password` FROM users WHERE id = :u_id';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            $rst = $stmt->fetch(PDO::FETCH_ASSOC);
            return $rst['password'];
        }

    } catch ( Exception $e ){
        error_log('エラー発生' . print_r($e->getMessage()));
        $err_msg['common'] = MSG_WAIT;
    }
}
// 体調データを取得する
function getPhysicOne($h_id, $u_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM health WHERE id = :h_id AND u_id = :u_id';
        $data = array(':h_id' => $h_id, 'u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            $rst = $stmt->fetch(PDO::FETCH_ASSOC);
            return $rst;
        }

    } catch ( Exception $e ){
        error_log('エラー発生：' . print_r($e->getMessage()));
        $err_msg['common'] = MSG_WAIT;
    }
}
// 体調データの塊を取得する
function getPhysicData($u_id, $listSpan){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM health WHERE u_id = :u_id ORDER BY `date` DESC LIMIT :listSpan';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':u_id', $u_id, PDO::PARAM_INT);
        $stmt->bindValue(':listSpan', $listSpan, PDO::PARAM_INT);
        $stmt->execute();

        if($stmt){
            $rst = $stmt->fetchAll();
            return array_reverse($rst);
        }

    } catch ( Exception $e ){
        error_log('エラー発生；' . print_r($e->getMessage()), true);
        $err_msg['common'] = MSG_WAIT;
    }
}
// 体調データリストを取得する
function getPhysicDataList($u_id, $currentNum){
    try{
        $listSpan = 7 * $currentNum;
        $minSpan = 7 * ($currentNum - 1);
        $dbh = dbConnect();
        $sql = 'SELECT * FROM health WHERE u_id = :u_id ORDER BY `date` DESC LIMIT :listSpan OFFSET :minSpan';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':u_id', $u_id, PDO::PARAM_INT);
        $stmt->bindValue(':listSpan', $listSpan, PDO::PARAM_INT);
        $stmt->bindValue(':minSpan', $minSpan, PDO::PARAM_INT);
        $rst = $stmt->execute();

        if($stmt){
            $rst = $stmt->fetchAll();
            return $rst;
        }

    }catch ( Exception $e ){
        error_log('エラー発生：' . print_r($e->getMessage()), true);
        $err_msg['common'] = MSG_WAIT;
    }
}
// =====================================
// その他
// =====================================
// ログインの有効期限を設定
function getLoginLimit(){
    // デフォルトのログイン有効期限
    $ses_limit = 60 * 60;
    // ログインの有効期限を設定
    $_SESSION['login_limit'] = $ses_limit;
    $_SESSION['login_date'] = time();
}
// サニタイズ
function sanitize($str){
    return htmlspecialchars($str, ENT_QUOTES);
}
// ファイルアップロード
function uploadImg($file, $key){
    // ファイルにエラーがある　かつ　画像は数値で返ってくるので数値かどうか判定
    if (isset($file['error']) && is_int($file['error']) ){
        try {
            // バリデーションチェック
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
            break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません。');
                break;
                case UPLOAD_ERR_INI_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます。');
                break;
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます。');
                break;
                default:
                    throw new RuntimeException('例外のエラーが発生しました。');
            }

            // 画像ファイル形式の判定
            $type = exif_imagetype($file['tmp_name']);
            if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)){
                throw new RuntimeException('画像形式が未対応です。');
            }

            // パスを指定
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

            if(!move_uploaded_file($file['tmp_name'], $path)) {
                throw new RuntimeException('ファイル保存時にエラーが発生しました。');
            }

            chmod($path, 0644);

            debug('ファイルは正常にアップロードできました。');
            debug('ファイルパス'.$path);

            // 保存先のパスを返す
            return $path;

        } catch ( RuntimeException $e ){
            error_log('エラー発生：'.$e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}