<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　退会ページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

if(!empty($_POST)){
    debug('POSTされました');
    try{
        // ユーザーidから、そのIDのアカウントのdelete_flgをTRUEにする
        $dbh = dbConnect();
        $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            // ブラウザのセッション変数削除
            $_SESSION = array();
            // ブラウザのセッションIDを破棄
            if(isset($_COOKIE[session_name()])){
                setcookie(session_name(), '', time()-42000, '/');
            }
            // サーバーのセッション削除
            session_destroy();
            header("Location:login.php");
        }
    } catch ( Exception $e ){
        error_log('エラー発生：'.print_r($e->getMessage()));
    }

}
?>
<?php
$headTitle = '退会ページ';
require('head.php');
?>
<body>

    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>

    <!-- フォーム -->
    <section class="form">
        <form class="form_list" action="" method="post">
            <h2 class="form_tit">退会</h2>
            <input name="retire" href="" class="form_retire" type="submit" value="退会する"></input>
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