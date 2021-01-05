<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 「　マイページ　」');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

require('auth.php');

// 折れ線グラフ描画に必要なライブラリ
require_once('jpgraph-4.3.4/src/jpgraph.php');
require_once('jpgraph-4.3.4/src/jpgraph_line.php');
require_once('jpgraph-4.3.4/src/jpgraph_date.php');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : 1;

// グラフの表示項目
$g_option = (!empty($_POST['graph_item'])) ? $_POST['graph_item'] : 'weight';
// 折れ線グラフの準備
$wData = array();
$yData = array();
// 項目によって目盛りを変更する
switch ($g_option){
    case 'weight':
        $argMin = 0;
        $argMax = 100;
        $yTitle = '(kg)';
    break;
    case 'bpercent':
        $argMin = 0;
        $argMax = 100;
        $yTitle = '(%)';
    break;
    case 'sleeptime':
        $argMin = 0;
        $argMax = 24;
        $yTitle = '(h)';
    break;
    case 'feel':
        $argMin = 0;
        $argMax = 5;
        $yTitle = '(value)';
    break;
}

// DBからデータを取得する
$u_id = $_SESSION['user_id'];
$UserInfo = getUserInfo($u_id);
$listSpan = (!empty($_POST['graph_term'])) ? $_POST['graph_term'] : 7; // 期間を指定
$dbPhysicData = getPhysicData($u_id, $listSpan);
// 体調データリスト
$currentPageNum = $p_id;
$dbFormData = getPhysicDataList($u_id, $currentPageNum); // 一覧のデータを取得
$totalPages = $dbFormData['total_pages']; // 一覧のトータルページ数

foreach($dbPhysicData as $key => $val){
    if($g_option === 'sleeptime'){
        $yTime = mb_substr($val[$g_option], 0, 2);
        $yMinute = mb_substr($val[$g_option], 3, 2)/60;
        $yData[] = $yTime + $yMinute;
    }else{
        $yData[] = $val[$g_option];
    }
}
foreach($dbPhysicData as $key => $val){
    // 体調データが入っている場合
    if(!empty($val[$g_option])){
        $xData[] = mb_substr($val['date'], 5, 2).'/'.mb_substr($val['date'], 8, 2);
    }
}
// データがあるかどうか
if(!empty($xData) && !empty($yData)){
    $graphShow_flg = true;
    $graph = new Graph(400, 400, "auto"); // グラフサイズ
    $graph->SetScale('datlin', $argMin, $argMax); // 目盛り
    $graph->title->Set($g_option); // グラフのタイトル
    $graph->xaxis->SetTickLabels($xData);
    $graph->xaxis->SetTitle('(Date)');
    $graph->yaxis->SetTitle($yTitle); // Y軸のタイトル
    // グラフを描写
    $lineplot = new LinePlot($yData);
    $graph->Add($lineplot);
    // imgフォルダにグラフを保存する
    $graph->Stroke("img/graph.jpg");
}else{
    // データがない場合
    $graphShow_flg = false;
}

?>
<?php
$headTitle = 'マイページ';
require('head.php');
?>
<body>
    <!-- ヘッダー -->
    <?php require('header.php'); ?>

    <!-- グラフ -->
    <section class="ct">
        <div class="ct_inner">
            <h2 class="ct_head"><?= $UserInfo['name']; ?>さんのデータグラフ</h2>
            <form class="graph" action="" method="post">
                <div class="graph_tmenu">
                    <label for="l-week" class="graph_param">
                        1週間
                        <input type="radio" name="graph_term" id="l-week" value="7" checked="checked">
                    </label>
                    <label for="l-month" class="graph_param">
                        1ヶ月
                        <input type="radio" name="graph_term" id="l-month" value="30" <?= (!empty($listSpan) && $listSpan === '30') ? 'checked="checked"': ''; ?> >
                    </label>
                    <label for="l-year" class="graph_param">
                        1年間
                        <input type="radio" name="graph_term" id="l-year" value="360" <?= (!empty($listSpan) && $listSpan === '360') ? 'checked="checked"': ''; ?> >
                    </label>
                </div>
                <div class="graph_menu">
                    <label for="l-weight" class="graph_param">
                        体重
                        <input type="radio" name="graph_item" id="l-weight" value="weight" checked="checked">
                    </label>
                    <label for="l-bpercent" class="graph_param">
                        体脂肪率
                        <input type="radio" name="graph_item" id="l-bpercent" value="bpercent" <?= (!empty($g_option) && $g_option === 'bpercent') ? 'checked="checked"': ''; ?> >
                    </label>
                    <label for="l-sleeptime" class="graph_param">
                        睡眠時間
                        <input type="radio" name="graph_item" id="l-sleeptime" value="sleeptime" <?= (!empty($g_option) && $g_option === 'sleeptime') ? 'checked="checked"': ''; ?>>
                    </label>
                    <label for="l-feel" class="graph_param">
                        体調
                        <input type="radio" name="graph_item" id="l-feel" value="feel" <?= (!empty($g_option) && $g_option === 'feel') ? 'checked="checked"': ''; ?>>
                    </label>
                    <input class="graph_submit" type="submit" value="検索">
                </div>
            </form>
            <div class="ct_list">
            <?php if($graphShow_flg === true){ ?>
                <img src="img/graph.jpg" alt="">
            <?php }else{ ?>
                <p>登録されているデータがありません。</p>
            <?php } ?>
            </div>
        </div>
    </section><!-- /グラフ -->

    <!-- コンテンツ -->
    <section class="ct">
        <div class="ct_inner">
            <h2 class="ct_head"><?= $UserInfo['name']; ?>さんの体調一覧</h2>
            <div class="ct_list">
                <div class="ct_wrap">
                <?php 
                if(!empty($dbFormData['data'][0])){
                foreach($dbFormData['data'] as $key => $val): 
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
            <!-- ページネーション -->
            <?php
                pagenation($currentPageNum, $totalPages);
            ?>

        </div>
    </section><!-- /コンテンツ -->


    <!-- サイドバー -->
    <?php require('sidebar.php'); ?>

    <!-- フッター -->
    <?php require('footer.php'); ?>