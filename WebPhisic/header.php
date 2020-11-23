<!-- ヘッダー -->
<header class="header">
    <div class="header_inner">
        <a href="login.php" class="header_top">体調管理サイト</a>
        <div class="header_right">
            <?php if(!empty($_SESSION['login_date']) && time() <= $_SESSION['login_date'] + $_SESSION['login_limit']){ ?>
                <a href="mypage.php" class="header_item">マイページ</a>
                <a href="logout.php" class="header_item">ログアウト</a>
            <?php }else{ ?>
                <a href="signup.php" class="header_item">サインイン</a>
                <a href="login.php" class="header_item">ログイン</a>
            <?php } ?>
        </div>
    </div>
</header><!-- /ヘッダー -->