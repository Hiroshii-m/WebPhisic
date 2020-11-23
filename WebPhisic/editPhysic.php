<?php

// 共通変数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　体調編集画面　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

$u_id = $_SESSION['user_id'];
$UserInfo = getUserInfo($u_id);
$height = $UserInfo['height']/100;
$h_id = (!empty($_GET['h_id'])) ? $_GET['h_id'] : '';
// 体調データを取得
$dbFormData = getPhysicOne($h_id, $u_id);

if(!empty($_POST)){
    debug('POSTがありました。');

    // POSTデータを変数へ格納
    $weight = (!empty($_POST['weight'])) ? $_POST['weight'] : 0;
    $bpercent = (!empty($_POST['bpercent'])) ? $_POST['bpercent'] : 0;
    // bmiデータ
    $bmi = (!empty($height) && !empty($weight)) ? round($weight/($height**2), 1): '';
    $getime = (!empty($_POST['getime'])) ? $_POST['getime'] : '';
    $sleeptime = (!empty($_POST['sleeptime'])) ? $_POST['sleeptime'] : '';
    $feel = (!empty($_POST['feel'])) ? $_POST['feel'] : 3;
    $breakfast = (!empty($_POST['breakfast'])) ? $_POST['breakfast'] : '';
    $lunch = (!empty($_POST['lunch'])) ? $_POST['lunch'] : '';
    $dinner = (!empty($_POST['dinner'])) ? $_POST['dinner'] : '';
    $comment = (!empty($_POST['comment'])) ? $_POST['comment'] : '';

    if(empty($err_msg)){
        debug('バリデーションOK');

        try {
            // データベースへ登録
            $dbh = dbConnect();
            $sql = 'UPDATE health SET weight = :weight, bpercent = :bpercent, bmi = :bmi, getime = :getime, sleeptime = :sleeptime, feel = :feel, breakfast = :breakfast, lunch = :lunch, dinner = :dinner, comment = :comment WHERE id = :h_id AND u_id = :u_id';
            $data = array(':weight' => $weight, ':bpercent' => $bpercent, ':bmi' => $bmi, ':getime' => $getime, ':sleeptime' => $sleeptime, ':feel' => $feel,  ':breakfast' => $breakfast, ':lunch' => $lunch, ':dinner' => $dinner, ':comment' => $comment, ':h_id' => $h_id, ':u_id' => $u_id);
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
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
        <form class="form_list" action="" method="post">
            <h2 class="form_tit">体調登録</h2>
            <div class="area-msg">
                    <?= showErr('common'); ?>
                </div>
            <label class="form_item <?= validErr('date'); ?>" for="">
                日付
                <p><?= $dbFormData['date']; ?></p>
            </label>
            <label class="form_item <?php validErr('weight'); ?>" for="">
                体重（kg）
                <input class="form_input" type="text" name="weight" value="<?= showDbPostData('weight'); ?>">
                <div class="area-msg">
                    <?= showErr('weight'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('bpercent'); ?>" for="">
                体脂肪率（%）
                <input class="form_input" type="text" name="bpercent" value="<?= showDbPostData('bpercent'); ?>">
                <div class="area-msg">
                    <?= showErr('bpercent'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('getime'); ?>" for="">
                起床時間
                <input class="form_input" type="time" name="getime" value="<?= showDbPostData('getime'); ?>">
                <div class="area-msg">
                    <?= showErr('getime'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('sleeptime'); ?>" for="">
                睡眠時間
                <input class="form_input" type="time" name="sleeptime" value="<?= showDbPostData('sleeptime'); ?>">
                <div class="area-msg">
                    <?= showErr('sleeptime'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('feel'); ?>" for="">
                体調
                <input class="form_input" type="number" min="1" max="5" name="feel" value="<?= showDbPostData('feel'); ?>">
                <div class="area-msg">
                    <?= showErr('feel'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('breakfast'); ?>" for="">
                朝食
                <input class="form_input" type="text" name="breakfast" value="<?= showDbPostData('breakfast'); ?>">
                <div class="area-msg">
                    <?= showErr('breakfast'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('lunch'); ?>" for="">
                昼食
                <input class="form_input" type="text" name="lunch" value="<?= showDbPostData('lunch'); ?>">
                <div class="area-msg">
                    <?= showErr('lunch'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('dinner'); ?>" for="">
                夕食
                <input class="form_input" type="text" name="dinner" value="<?= showDbPostData('dinner'); ?>">
                <div class="area-msg">
                    <?= showErr('dinner'); ?>
                </div>
            </label>
            <label class="form_item <?php validErr('comment'); ?>" for="">
                コメント
                <input class="form_input" type="text" name="comment" value="<?= showDbPostData('comment'); ?>">
                <div class="area-msg">
                    <?= showErr('comment'); ?>
                </div>
            </label>
            


            <input class="form_submit" type="submit" value="送信">
        </form>
    </section>

    <!-- フッター -->
    <?php
    require('footer.php');
    ?>