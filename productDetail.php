<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　商品詳細画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//================================
// 画面処理
//================================

// 画面表示用データ取得
//================================
//商品IDのGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
//DBから商品データを取得
$viewData = getProductOne($p_id);
//パラメータに不正な値が入っていないかチェック
if(empty($viewData)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");//トップページへ
}
debug('取得したDBデータ($viewData)：'.print_r($viewData,true));

if(!empty($_POST['submit'])){
  debug('購入ボタンが押下されました');

  //ログイン認証
  require('auth.php');

  try{
    $dbh = dbConnect();
    $data = array(':p_id' => $p_id, ':s_uid' => $viewData['user_id'], ':b_uid' => $_SESSION['user_id'], ':create_date' => date('Y-m-d H:i:s'));
    $sql = 'INSERT into board (p_id,sale_user,buy_user,create_date) values (:p_id,:s_uid,:b_uid,:create_date)';
    $stmt = queryPost($dbh, $data, $sql);
    
    //クエリ成功の場合
    if($stmt){
      debug('無事購入されました。連絡掲示板へ遷移します。');
      header("Location:msg.php?m_id=".$dbh->lastInsertID());
    }else{
      debug('購入できませんでした');
    }

  }catch(Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }

}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'しょうひんしょうさい';
require('head.php')
?>
  
<body class="page-productDetail page-1colum">

  <style>
    /*お気に入りアイコン*/
    .icn-like{
      float:right;
      color: #ddd;
    }
    .icn-like:hover{
      cursor: pointer;
    }
    .icn-like.active{
      float:right;
      color: #fe8a8b;
    }
  </style>

  <!--ヘッダー-->
  <?php  
    require('header.php')
  ?>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <!--メイン-->
    <section id="main">
        
      <div class="title">
        <span class="badge"><?php echo sanitize($viewData['category']); ?></span>
        <?php echo sanitize($viewData['p_name']); ?>
        <i class="fa fa-heart icn-like js-click-like <?php if(isLike($_SESSION['user_id'],$viewData['id'])){ echo 'active'; } ?>" area-hiden="true" data-productid="<?php echo sanitize($viewData['id']); ?>"></i>
      </div>

      <div class="product-img-container">
        <div class="img-main">
          <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="メイン画像：<?php echo sanitize($viewData['p_name']); ?>" id="js-switch-img-main">
        </div>
        <div class="img-sub">
          <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="画像1：<?php echo sanitize($viewData['p_name']); ?>" class="js-switch-img-sub">
          <img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="画像2：<?php echo sanitize($viewData['p_name']); ?>" class="js-switch-img-sub">
          <img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>" alt="画像3：<?php echo sanitize($viewData['p_name']); ?>" class="js-switch-img-sub">
        </div>
      </div>

      <div class="product-detail">
        <p><?php echo sanitize($viewData['comment']); ?></p>
      </div>
      <div class="product-buy">

        <div class="item-left">
          <a class="common" href="index.php<?php echo appendGetParam(array('p_id')); ?>">&lt; 商品一覧に戻る</a>
        </div>

        <form action="" method="post">
          <div class="item-right">
            <input type="submit" name="submit" class="btn btn-primary" value="買う！">
          </div>
        </form>

        <div class="item-right">
          <p class="price">¥<?php echo sanitize(number_format($viewData['price'])); ?>-</p>
        </div>

      </div>

    </section>
  </div>

  <?php 
  require('footer.php')
  ?>