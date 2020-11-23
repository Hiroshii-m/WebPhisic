<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　マイページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

// 折れ線グラフ描画に必要なライブラリ


// DBからデータを取得する
$u_id = $_SESSION['user_id'];
$UserInfo = getUserInfo($u_id);
$dbFormData = getPhysicData($u_id);

?>
<?php
$headTitle = 'マイページ';
require('head.php');
?>
<body>
    <!-- ヘッダー -->
    <?php require('header.php'); ?>

    <!-- コンテンツ -->
    <section class="ct">
        <div class="ct_inner">
            <h2 class="ct_head"><?= $UserInfo['name']; ?>さんの体調一覧</h2>
            <div class="ct_list">
                <div class="ct_wrap">
                <?php 
                if(!empty($dbFormData[0])){
                foreach($dbFormData as $key => $val): 
                ?>
                    <div class="ct_item">
                        <a href="editPhysic.php?h_id=<?= $val['id']; ?>" class="ct_change">編集する</a>
                        <p>日程：<?= $val['date']; ?></p>
                        <div class="ct_data c-flex">
                            <p class="">体重:<?= $val['weight']; ?>kg</p>
                            <p>BMI：<?= $val['bmi']; ?></p>
                            <p>体脂肪率：<?= $val['bpercent']; ?>%</p>
                            <p>睡眠時間：<?= $val['sleeptime']; ?></p>
                            <p>体調：<?= $val['feel']; ?></p>
                            <p>起床時間：<?= $val['getime']; ?></p>
                        </div>
                        <div class="ct_food c-flex">
                            <p>朝食：<?= $val['breakfast']; ?></p>
                            <p>昼食：<?= $val['lunch']; ?></p>
                            <p>晩食：<?= $val['dinner']; ?></p>
                        </div>
                        <div class="ct_comment">
                            コメント：<?= $val['comment']; ?>
                        </div>
                    </div>
                <?php 
                endforeach; 
                }else{
                ?>
                <p>投稿はまだありません。</p>
                <?php
                }
                ?>
                </div>
            </div>
        </div>
    </section><!-- /コンテンツ -->

    <!-- サイドバー -->
    <?php require('sidebar.php'); ?>

    <!-- フッター -->
    <?php require('footer.php'); ?>