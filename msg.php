<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　連絡掲示板　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();



//================================
// 画面処理
//================================
$partnerUserId = '';
$partnerUserInfo = '';
$myUserInfo = '';
$productInfo = '';
$viewData = '';

//画面表示用データを取得
//================================
//GETパラメータを取得
$m_id = (!empty($_GET['m_id'])) ? $_GET['m_id'] : '' ;
//DBから掲示板とメッセージデータを取得
$viewData = getMsgsAndBoard($m_id);
debug('$viewDataの中身：'.print_r($viewData,true));
//パラメータに不正な値が入っていないかチェック
if(empty($viewData)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  //header("Location:mypage.php");//マイページへ
}

//商品情報を取得
$productInfo = getProductOne($viewData[0]['p_id']);
//debug('$productInfoの中身：'.print_r($productInfo,true));
//商品情報が入っているかチェック
if(empty($productInfo)){
  error_log('エラー発生：商品情報が取得できませんでした');
  //header("Location:mypage.php");//マイページへ
}

//viewDataから相手のユーザーIDを取り出す
$dealUserIds[] = $viewData[0]['sale_user'];
debug('$dealUserIds1:'.print_r($dealUserIds,true));
$dealUserIds[] = $viewData[0]['buy_user'];
debug('$dealUserIds2:'.print_r($dealUserIds,true));
if(($key = array_search($_SESSION['user_id'],$dealUserIds)) == true){
    unset($dealUserIds[$key]);
}
$partnerUserId = array_shift($dealUserIds);
debug('取得した相手のユーザーID：'.$partnerUserId);

//DBから取引相手のユーザー情報を取得
if(isset($partnerUserId)){
  $partnerUserInfo = getUser($partnerUserId);
}

//相手のユーザー情報が取れたかチェック
if(empty($partnerUserInfo)){
  error_log('エラー発生：相手のユーザー情報が取得できませんでした');
  //header("Location:mypage.php");
}

//DBから自分のユーザー情報を取得
$myUserInfo = getUser($_SESSION['user_id']);
//debug('取得したユーザーデータ：'.print_r($partnerUserInfo,true));

//自分のユーザー情報が取れたかチェック
if(empty($myUserInfo)){
  error_log('エラー発生：自分のユーザー情報が取得できませんでした');
  //header("Location:mypage.php");
}

//post送信されていた場合
if(!empty($_POST)){
  debug('メッセージのPOST送信があります');
  debug('送信内容：'.print_r($_POST,true));

  //ログイン認証
  //require('auth.php');

  //バリデーションチェック
  $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
  //最大文字数チェック
  //validMaxLen($msg,'msg',500);
  //未入力チェック
  validRequired($msg,'msg');

  if(empty($err_msg)){
    debug('バリデーションOKです');

    try{
      $dbh = dbConnect();
      $data = array(':board_id'=>$m_id, ':msg'=>$msg, ':send_time'=>date('Y-m-d H:i:s'), ':to_user'=> $partnerUserId, ':from_user'=>$_SESSION['user_id']);
      $sql = 'INSERT into messages(board_id, msg, send_time, to_user, from_user) VALUES (:board_id, :msg, :send_time, :to_user, :from_user)';
      $stmt = queryPost($dbh, $data, $sql);

      if($stmt){
        $_POST=array();//postをクリア
        debug('連絡掲示板へ遷移します');
        header("Location:".$_SERVER['PHP_SELF'].'?m_id='.$m_id);
      }
    }catch(Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

}

?>


<?php
$siteTitle = 'れんらくけいじばん';
require('head.php');
?>
  
<body class="page-msg page-1colum">

  <!--ヘッダー-->
  <?php  
    require('header.php')
  ?>
    
  <!--メインコンテンツ-->
  <div id="contents" class="site-width">
    <!--Main-->
    <!--上段-->
    <section id="main">
      <div class="msg-info">
        <div class="avatar-img">
            <img src="<?php echo showImg(sanitize($myUserInfo['pic'])); ?>" alt="" class="avatar"><br>
        </div>

        <div class="avatar-info">
          <?php echo sanitize($myUserInfo['username']).' '.sanitize($myUserInfo['age'].'歳') ?><br>
          〒<?php echo wordwrap($myUserInfo['zip'],4,"-",true); ?><br>
          <?php echo sanitize($myUserInfo['addr']); ?><br>
          TEL:<?php echo sanitize($myUserInfo['tel']); ?>
        </div>

          <div>

            <div class="product-info">

              <div class="left">
                取引商品<br>
                <img src="<?php echo showImg(sanitize($productInfo['pic1'])); ?>" alt="" height="70px" width="auto">
              </div>

              <div class="right">
                <?php echo sanitize($productInfo['p_name']); ?><br>
                取引金額：<span class="price">¥<?php echo number_format(sanitize($productInfo['price'])); ?></span><br>
                取引開始日：
              </div>

            </div>

          </div>

      </div>

        <!--下段-->
        <div class="area-bord" id="js-scroll-bottom">

          <?php if(!empty($viewData)){
            foreach($viewData as $key => $val){
              if(!empty($val['from_user']) && $val['from_user'] == $partnerUserId){
          ?>
                  <div class="msg-cnt msg-left">
                    <div class="avatar">
                      <img src="<?php echo sanitize(showImg($partnerUserInfo['pic'])); ?>" alt="">
                    </div>
                    <p class="msg-inrTxt">
                      <span class="triangle"></span>
                      <?php if(!empty($val['msg'])) echo sanitize($val['msg']); ?>
                    </p>
                    <div style="font-size:.5em;"><?php if(!empty($val['send_time'])) echo sanitize($val['send_time']); ?></div>
                  </div>
                  
          <?php 
            }else{
          ?>

                  <div class="msg-cnt msg-right">
                    <div class="avatar">
                      <img src="<?php echo sanitize(showImg($myUserInfo['pic'])); ?>" alt="" class="avatar">
                    </div>
                    <p class="msg-inrTxt">
                      <span class="triangle"></span>
                      <?php if(!empty($val['msg'])) echo sanitize($val['msg']); ?>
                    </p>
                    <div style="font-size:.5em; text-align:right;"><?php if(!empty($val['send_time'])) echo sanitize($val['send_time']); ?></div>
                  </div>


          <?php 
                }
              }
            }else{
          ?>
                  <p style="text-align:center;line-height:20;">メッセージ投稿はまだありません</p>
          <?php 
            } 
          ?>

          <div class="area-send-msg">
            <form action="" method="post">
              <textarea name="msg" id="" cols="30" rows="3"></textarea>
              <input type="submit" value="送信" class="btn btn-send">
            </form>
          </div>

        </div>
        
    </section>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

    <script>
      $(function(){
          $('#js-scroll-bottom').animate({scrollTop: $('#js-scroll-bottom')[0].scrollHeight},'fast');
      });
    </script>
  </div>

  <!--フッター-->
  <?php  
    require('footer.php');
  ?>