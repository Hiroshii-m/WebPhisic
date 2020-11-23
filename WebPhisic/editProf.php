<?php
// 予想作成時間3時間
// 実際5時間程

// 共通変数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　プロフィール編集画面　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

// ユーザー情報取得
$u_id = $_SESSION['user_id'];
$dbFormData = getUserInfo($u_id);
debug('ユーザー情報:'.print_r($dbFormData, true));

if(!empty($_POST)){
    debug('POSTがありました。'.print_r($_POST, true));

    // 画像以外のPOSTデータを変数へ格納
    $name = (!empty($_POST['name'])) ? $_POST['name'] : '';
    $email = (!empty($_POST['email'])) ? $_POST['email'] : '';
    $height = (!empty($_POST['height'])) ? $_POST['height'] : '';

    // バリデーションチェック
    // DB情報と変更のあった情報を調べる
    if($dbFormData['name'] !== $name){
        validMax($name, 'name');
    }
    if($dbFormData['email'] !== $email){
        validationEmail($email, 'email');
    }
    if(!isset($height)){
        validHalf($height, 'height');
        validMax($height, 'height', 3);
    }

    // 画像を変数へ入れる
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    $pic = (!empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;

    if(empty($err_msg)){
        debug('バリデーションOK');

        try {
            // データベースへ登録
            $dbh = dbConnect();
            $sql = 'UPDATE users SET name = :name, email = :email, height = :height, pic = :pic WHERE id = :u_id AND delete_flg = 0';
            $data = array(':u_id' => $u_id, ':name' => $name, ':email' => $email, ':height' => $height, ':pic' => $pic);
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                $_POST = array();
                header('Location:mypage.php');
                exit;
            }

        } catch ( Exception $e ){
            error_log('エラー発生:'.print_r($e->getMessage(), true));
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
        <form class="form_list" action="" method="post" enctype="multipart/form-data">
            <h2 class="form_tit">プロフィール編集画面</h2>
            <div class="area-msg">
                <?= showErr('common'); ?>
            </div>
            <label class="form_item <?= validErr('name'); ?>" for="">
                名前
                <input class="form_input" type="text" name="name" value="<?= showDbPostData('name'); ?>">
                <div class="area-msg">
                    <?= showErr('name'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('email'); ?>" for="">
                Email
                <input class="form_input" type="text" name="email" value="<?= showDbPostData('email'); ?>">
                <div class="area-msg">
                    <?= showErr('email'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('height'); ?>" for="">
                身長（小数点なしで単位：cm）
                <input class="form_input" type="text" name="height" value="<?= showDbPostData('height'); ?>">
                <div class="area-msg">
                    <?= showErr('height'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('pic'); ?>" for="">
                画像
                <div class="area-drop js-area-drop">
                    <!-- 3MB以内まで -->
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input class="form_input img-input js-area-input" accept=".png, .jpg, .jpeg" type="file" name="pic" style="<?php if(empty($_POST['pic'])) echo 'opacity:0;'; ?>">
                    <img class="js-prev-img" src="<?= showFile('pic'); ?>" alt="" style="<?php if(empty(showFile('pic'))) echo 'display:none;'; ?>">
                </div>
                <div class="area-msg">
                    <?= showErr('pic'); ?>
                </div>
            </label>
            

            <input class="form_submit" type="submit" value="送信">
        </form>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>