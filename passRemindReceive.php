<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行認証キー入力ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();


//SESSIONに認証キーがあるか確認、なければリダイレクト
if(empty($_SESSION['auth_key'])){
  header('Location:passRemindSend.php');
}

//================================
// 画面処理
//================================
//post送信されていた場合
if(!empty($_POST)){
  debug('認証キーのPOST送信があります。');
  debug('認証キーPOST情報：'.print_r($_POST,true));

  //変数に認証キーを代入
  $auth_key = $_POST['token'];

  //未入力チェック
  validRequired($auth_key,'token');

  if(empty($err_msg)){
    debug('認証キー未入力チェックOK');

    //固定長チェック
    validLength($auth_key,'token');
    //半角チェック
    validHalf($auth_key,'token');

    if(empty($err_msg)){
      debug('認証キーバリデーションOK');

      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['common'] = MSG16;
      }
      if(time() > $_SESSION['auth_key_limit']){
        $err_msg['common'] = MSG17;
      }

      if(empty($err_msg)){
        debug('認証OK');

        $pass = makeRandKey(); //パスワード生成
        debug('生成したパスワード：'.print_r($pass,true));

        $dbh = dbConnect();
        $data = array(':email' => $_SESSION['auth_email'],':pass' => password_hash($pass,PASSWORD_DEFAULT));
        $sql = 'UPDATE user SET pass = :pass WHERE email = :email AND delete_flg = 0';
        $stmt = queryPost($dbh,$data,$sql);
        
        if($stmt){
          debug('クエリ成功');

            //セッション削除
            session_unset();
            $_SESSION['msg_success']= SUC03;
            debug('セッション変数の中身：'.print_r($_SESSION,true));

            header("Location:login.php");
        
          }else{
            debug('クエリに失敗しました');
            $err_msg['common'] = MSG07;
          }
        
      }
    }
  }
}



?>


<?php 
$siteTitle = 'ぱすわーどさいはっこう | にんしょう';
require('head.php')
?>

<body class="page-signup page-1colum">

    <!--ヘッダー-->
    <?php  
      require('header.php')
    ?>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

      <!--Main-->
      <section id="main">

        <div class="form-container">

          <form action="" class="form" method="post">
            <p class="notice">ご指定のメールアドレスにお送りした【パスワード再発行認証メール】内にある「認証キー」をご入力ください。</p>
            
            <div class="area-msg">
              <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>

            <label class="<?php if(!empty($err_msg['token'])) echo 'err'; ?>">
              認証キー
              <input type="text" name="token" value = "<?php echo getFormData('tolen'); ?>">
            </label>

            <div class="area-msg">
              <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
            </div>

            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="変更画面へ">
            </div>

          </form>
        </div>
        <a class="common" href="passRemindSend.php">&lt; パスワード再発行メールを再度送信する</a>
      </section>
    </div>

    <!--フッター-->
    <?php  
      require('footer.php')
    ?>