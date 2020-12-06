<?php

// 共通変数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　パスワード変更画面　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

$u_id = $_SESSION['user_id'];

if(!empty($_POST)){
    $pass = (!empty($_POST['pass'])) ? $_POST['pass'] : '';
    $pass_new = (!empty($_POST['pass_new'])) ? $_POST['pass_new'] : '';
    $repass_new = (!empty($_POST['repass_new'])) ? $_POST['repass_new'] : '';

    // バリデーションチェック
    validRequired($pass, 'pass');
    if(empty($err_msg['pass'])){
        $dbPass = getPass($u_id);
        // 以前のパスワードと入力されたパスワードがあっているのかチェック
        validPassVerify($pass, $dbPass, 'pass');
    }
    validRequired($pass_new, 'pass_new');
    validRequired($repass_new, 'repass_new');
    validationPass($pass_new, 'pass_new');
    validationPass($repass_new, 'repass_new');
    if(empty($err_msg['pass']) && empty($err_msg['pass_new'])){
        validPassVerify($pass_new, $dbPass, 'pass_new', $pass_flg = true);
    }
    if(empty($err_msg['pass_new']) && empty($err_msg['repass_new'])){
        validMatch($pass_new, $repass_new, 'repass_new');
    }

    if(empty($err_msg)){
        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET `password` = :pass_new WHERE id = :u_id';
            $data = array(':pass_new' => password_hash($pass_new, PASSWORD_DEFAULT), ':u_id' => $u_id);
            $stmt = queryPost($dbh, $sql, $data);

            header('Location:mypage.php');

        } catch ( Exception $e ){
            error_log('エラー発生：'.print_r($e->getMessage()));
            $err_msg['common'] = MSG_WAIT;
        }
    }
}

debug(' 「　画面表示終了　」');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」」');
?>
<?php
$headTitle = '体調登録';
require('head.php');
?>
<body>

    <?php require('header.php'); ?>

    <!-- フォーム -->
    <section class="form">
        <form class="form_list" action="" method="post">
            <h2 class="form_tit">パスワード変更画面</h2>
            <div class="area-msg">
                <?= showErr('common'); ?>
            </div>
            <label class="form_item <?php validErr('pass'); ?>" for="">
                古いパスワード
                <input class="form_input" type="password" name="pass" value="<?= showValue('pass'); ?>">
                <div class="area-msg">
                    <?= showErr('pass'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('pass_new'); ?>" for="">
                新しいパスワード
                <input class="form_input" type="password" name="pass_new" value="<?= showValue('pass_new'); ?>">
                <div class="area-msg">
                    <?= showErr('pass_new'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('repass_new'); ?>" for="">
                新しいパスワード（再入力）
                <input class="form_input" type="password" name="repass_new" value="<?= showValue('repass_new'); ?>">
                <div class="area-msg">
                    <?= showErr('repass_new'); ?>
                </div>
            </label>

            <input class="form_submit" type="submit" value="送信">
        </form>
    </section>

    <!-- サイドバー -->
    <?php
    require('sidebar.php');
    ?>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>