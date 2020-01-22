<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　出品登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//ログイン認証
//require('auth.php');

$dbCategoryData = getCategory();
//debug('カテゴリデータ：'.print_r($dbCategoryData,true));

if(!empty($_POST)){
  debug('POST送信（商品登録）があります');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));
  
  $p_name = $_POST['p_name'];
  $category = $_POST['category'];
  $price = (!empty($_POST['price'])) ? $_POST['price'] : 0;
  $comment = $_POST['comment'];

  $pic1='';
  if(!empty($_FILES['pic1'])){
    $pic1 = uploadImg($_FILES['pic1'],'name');
  }
  //$pic1 = (empty($pic1) && !empty($dbFormData['pic1'])) ? $dbFormData['pic1'] : $pic1;
  // debug('$pic1の中身：'.print_r($pic1,true));
  // debug('ファイル名1：'.$_FILES['pic1']['name']);
  
  $pic2='';
  if(!empty($_FILES['pic2'])){
    $pic2 = uploadImg($_FILES['pic2'],'name');
  }
  //$pic2 = (empty($pic2) && !empty($dbFormData['pic2'])) ? $dbFormData['pic2'] : $pic1;
  // debug('$pic2の中身：'.print_r($pic2,true));
  // debug('ファイル名2：'.$_FILES['pic2']['name']);

  $pic3='';
  if(!empty($_FILES['pic3'])){
    $pic3 = uploadImg($_FILES['pic3'],'name');
  }
  //$pic3 = (empty($pic3) && !empty($dbFormData['pic3'])) ? $dbFormData['pic3'] : $pic1;
  // debug('$pic3の中身：'.print_r($pic3,true));
  // debug('ファイル名3：'.$_FILES['pic3']['name']);

  try{

    $dbh = dbConnect();
    debug('DB新規登録です');
    $data = array(':p_name'=>$p_name,':category'=>$category,
                  ':price'=>$price,':comment'=>$comment,
                  ':pic1'=>$pic1,':pic2'=>$pic2,':pic3'=>$pic3);
    $sql = 'INSERT into product (p_name,category,price,comment,pic1,pic2,pic3) 
            VALUES (:p_name,:category,:price,:comment,:pic1,:pic2,:pic3)';
    debug('SQL:'.$sql);
    debug('流し込みデータ：'.print_r($data,true));
    $stmt = queryPost($dbh, $data, $sql);

    if($stmt){
      debug('商品名を登録できました');
    }else{
      debug('商品名を登録できませんでした');
    }

  }catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
debug('商品登録画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>




<?php 
$siteTitle = 'しょうひんしゅっぴんとうろく';
require('head.php')
?>
 
  <body class="page-profEdit page-2colum page-logined">

    <!--ヘッダー-->
    <?php  
      require('header.php')
    ?>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

      <h1 class="page-title">商品を出品する</h1>

      <!--Main-->
      <section id="main">
        <div class="form-container">
          <form class="form" method="post" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">

            <label>
              商品名
              <input type="text" name="p_name">
            </label>

            <label>
              カテゴリ<span class="label-require">必須</span>
              <select name="category">

                <option value="0">選択してください</option>
                <?php foreach($dbCategoryData as $key => $val){ ?>
                  <option value="<?php echo $val['id'] ?>" >
                    <?php echo $val['name']; ?>
                  </option>
                <?php } ?>

              </select>
            </label>

            <label for="" style="text-align: left">
              金額
              <div class="form-group">
                <input type="text" name="price" style="width: 150px;" placeholder="0">
                <span class="option">円</span>
              </div>
            </label>

            <label>詳細
              <textarea name="comment" id="js-count" style="height:150px; resize:none;"></textarea>
              <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
            </label>

            <div style="overflow:hidden">

              <div class="imgDrop-container">
                画像1
                <label class="area-drop area1">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic1" class="input-file">
                  <img src="<?php echo getFormData('pic1'); ?>" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;'; ?>">
                    ドラッグ＆ドロップ
                </label>
              </div>

              <div class="imgDrop-container">
                画像2
                <label class="area-drop area2">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic2" class="input-file">
                  <img src="<?php echo getFormData('pic2');?>" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;'; ?>">
                    ドラッグ＆ドロップ
                </label>
              </div>

              <div class="imgDrop-container">
                画像3
                <label class="area-drop area3">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic3" class="input-file">
                  <img src="<?php echo getFormData('pic3');?>" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;'; ?>">
                    ドラッグ＆ドロップ
                </label>
              </div>

            </div>


            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="出品する">
            </div>

          </form>
        </div>
      </section>

      <!--サイドバー-->
      <?php 
            require('sidebar_mypage.php');
      ?>
      
    </div>

    <!--フッター-->
    <?php require('footer.php'); ?>
    