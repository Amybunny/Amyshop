<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行メール送信ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//================================
// 画面処理
//================================

//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));

  //変数にPOST情報を代入
  $email = $_POST['email'];

  //未入力チェック
  validRequired($email,'email');

  if(empty($err_msg)){
    debug('未入力チェックOK');

    //emailの形式チェック
    validEmail($email,'email');
    //emailの最大文字数チェック
    validMaxLen($email,'email');

    if(empty($err_msg)){
      debug('バリデーションOKです。');


        $dbh = dbConnect();
        $data = array(':email' => $email);
        $sql = 'SELECT count(*) FROM user WHERE email = :email AND delete_flg = 0';
        $stmt = queryPost($dbh,$data,$sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        //emailがDBに登録されている場合
        if($stmt && array_shift($result)){
          debug('クエリ成功。DB登録あり。');
          $_SESSION['msg_success'] = SUC03;
          $auth_key = makeRandKey();//認証キー作成

          //認証に必要な情報をセッションへ保存
          $_SESSION['auth_key'] = $auth_key;
          $_SESSION['auth_email'] = $email;
          $_SESSION['auth_key_limit'] = time()+(60*30);//現在時刻より30分後のUNIXタイムスタンプを入れる
          debug('セッション変数の中身：'.print_r($_SESSION,true));
          
          header("Location:passRemindReceive.php");//認証キー入力ページ
        
        }else{
          debug('クエリに失敗したかDBに登録のないemailが入力されました。');
          $err_msg['common'] = MSG07;
        }

    }
  }
}

?>
<?php
$siteTitle = 'ぱすわーどさいはっこう | そうしん';
require('head.php')
?>

<body class="page-signup page-1colum">
    <style>
        #notice{
            margin-bottom: 10px;
        }
    </style>

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
                    <p class="notice">ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送り致します。</p>
                    <label>
                        Email
                        <input type="text" name="email">
                    </label>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="送信する">
                    </div>
                </form>
            </div>
            <a class="common" href="mypage.php">&lt; マイページに戻る</a>
        </section>
    </div>

    <!--フッター-->
    <?php  
      require('footer.php')
    ?>