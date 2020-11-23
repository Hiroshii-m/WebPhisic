<?php

// 共通変数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　新規登録　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

if(!empty($_POST)){
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    // バリデーションチェック
    validationEmail($email, 'email');
    if(empty($err_msg['email'])){
        // Email重複チェック
        validEmailDup($email, 'email');
    }
    validRequired($pass, 'pass');
    if(empty($err_msg['pass'])){
        // 最小文字数チェック
        validMin($pass, 'pass');
    }
    if(empty($err_msg['pass'])){
        // 半角英数字チェック
        validHalf($pass, 'pass');
    }
    validRequired($pass_re, 'pass_re');
    if(empty($err_msg['pass']) && empty($err_msg['pass_re'])){
        validMatch($pass, $pass_re, 'pass');
    }

    if(empty($err_msg)){
        debug('バリデーションOKです。');

        // DB接続
        $dbh = dbConnect();
        $sql = 'INSERT INTO users (email, password) VALUES (:email, :password)';
        $data = array(':email' => $email, ':password' => password_hash($pass, PASSWORD_DEFAULT));
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            getLoginLimit();
            // ユーザーidをセッションに格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            // マイページへ遷移
            header("Location:mypage.php");
            exit;
        }
    }

}

?>
<?php
$headTitle = '新規登録';
require('head.php');
?>
<body>

    <?php require('header.php'); ?>

    <!-- フォーム -->
    <section class="form">
        <form class="form_list" action="" method="post">
            <h2 class="form_tit">新規登録</h2>
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
            <label class="form_item <?php validErr('pass_re'); ?>" for="">
                パスワード（再入力）
                <input class="form_input" type="password" name="pass_re">
                <div class="area-msg">
                    <?= showErr('pass_re'); ?>
                </div>
            </label>
            <input class="form_submit" type="submit" value="送信">
        </form>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>
