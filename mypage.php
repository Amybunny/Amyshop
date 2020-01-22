<?php 
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//require('auth.php');

// 画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];

$productData = getMyProducts($u_id);
//debug('取得した商品データ($productData):'.print_r($productData,true));
$boardData = getMyMsgsAndBoard($u_id);
//debug('取得した掲示板データ($boardData):'.print_r($boardData,true));
$likeData = getMyLike($u_id);
//debug('取得したお気に入りデータ($likeData):'.print_r($likeData,true));


debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php  
$siteTitle = 'まいぺーじ';
require('head.php')
?>
  <body class="page-mypage page-2colum page-logined">

    <style>
        #main{
            border: none !important;
        }
    </style>

    <!--ヘッダー-->
    <?php  
      require('header.php')
    ?>

    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
    </p>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

      <h1 class="page-title">まいぺーじ</h1>

      <!--Main-->
      <section id="main">





        <section class="list panel-list">

          <h2 class="title">
            登録商品一覧
          </h2>

          <?php 
          if(!empty($productData)):
            foreach($productData as $key => $val): 
          ?>

            <a href="registProduct.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>" class="panel">
              <div class="panel-head">
                <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['p_name']); ?>">
              </div>
              <div class="panel-body">
                <p class="panel-title"><?php echo sanitize($val['p_name']); ?><span class="price">¥<?php echo sanitize(number_format($val['price'])); ?></span></p>
              </div>
            </a>
          
          <?php 
              endforeach;
            endif;
          ?>


        </section>








        <style>
          .list{
            margin-bottom: 30px;
          }
        </style>

        <section class="list list-table">
          <h2 class="title">
            連絡掲示板一覧
          </h2>
          <table class="table">
            <thead>
              <tr>
                <th class="th-send-time">最新送信日時</th>
                <th class="th-partner-name">取引相手</th>
                <th class="th-message">メッセージ</th>
              </tr>
            </thead>

            <tbody>

              <?php 
                if(!empty($boardData)){
                  foreach($boardData as $key => $val){
                    if(!empty($val['msg'])){
                      $msg = array_shift($val['msg']);
              ?>

                <tr>
                  <td><?php echo sanitize(date('Y.m.d H:i:s',strtotime($msg['send_time']))); ?></td>
                  <td><?php echo sanitize($val['partner_name']) ?></td>
                  <td><a class="common" href="msg.php?m_id=<?php echo sanitize($val['id']); ?>"><?php echo mb_substr(sanitize($msg['msg']),0,40); ?>...</a></td>
                </tr>


              <?php 
                }else{
              ?>

              <?php 
                    }
                  }
                }
              ?>





            </tbody>
          </table>
        </section>

        <section class="list panel-list">

          <h2 class="title">
            お気に入り一覧
          </h2>

          <?php 
            if(!empty($likeData)): 
              foreach($likeData as $key => $val):
          ?>

            <a href="productDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['p_id'] : '?p_id='.$val['p_id']; ?>" class="panel">
              <div class="panel-head">
                <img src="<?php echo showImg(sanitize($val['pic1'])); ?>" alt="<?php echo sanitize($val['p_name']); ?>">
              </div>
              <div class="panel-body">
                <p class="panel-title"><?php echo sanitize($val['p_name']); ?><span class="price">¥<?php echo sanitize(number_format($val['price'])); ?></span></p>
              </div>
            </a>

          <?php  
              endforeach;
            endif;
          ?>
          
        </section>









      </section>

      <!--サイドバー-->
      <?php 
            require('sidebar_mypage.php');
      ?>

    </div>

  <?php require('footer.php') ?>