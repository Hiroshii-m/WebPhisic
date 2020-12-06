<?php

// ログインしたかどうか判定
if(!empty($_SESSION['login_date'])){
    // ログイン有効期限オーバーの場合
    if($_SESSION['login_date'] + $_SESSION['login_limit'] <= time()){
        if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
            header("Location:login.php");
            exit;
        }

    }else if(time() <= $_SESSION['login_date'] + $_SESSION['login_limit']){
        // ログインを更新
        $_SESSION['login_date'] = time();
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            header("Location:mypage.php");
        }
    }
}else{
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:login.php");
    }
}