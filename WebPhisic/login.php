<?php

// 共通変数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　ログイン画面　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

if(!empty($_POST)){
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    // バリデーションチェック
    validationEmail($email, 'email');
    validRequired($pass, 'pass');
    validationPass($pass, 'pass');

    if(empty($err_msg)){
        debug('バリデーションOKです。');

        // DB接続
        $dbh = dbConnect();
        $sql = 'SELECT id, email, password FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $rst = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($rst) && password_verify($pass, $rst['password'])){
            debug('パスワードがマッチしました。');
            
            getLoginLimit();
            // ユーザーidをセッションに格納
            $_SESSION['user_id'] = $rst['id'];

            // マイページへ遷移
            debug('マイページへ移動');
            header("Location:mypage.php");
        }else{
            $err_msg['common'] = MSG_NOLOGIN;
        }

    }
}
debug(' 「　画面表示終了　」');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>
<?php
$headTitle = 'ログイン';
require('head.php');
?>
<body>

    <?php require('header.php'); ?>

    <!-- フォーム -->
    <section class="form">
        <form class="form_list" action="" method="post">
            <h2 class="form_tit">ログイン</h2>
            <label class="form_item <?php validErr('email'); ?>" for="">
                Email
                <input class="form_input" type="text" name="email" value="<?= showValue('email'); ?>">
                <div class="area-msg">
                    <?= showErr('email'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('pass'); ?>" for="">
                パスワード
                <input class="form_input" type="password" name="pass">
                <div class="area-msg">
                    <?= showErr('pass'); ?>
                </div>
            </label>

            <input class="form_submit" type="submit" value="送信">
        </form>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>