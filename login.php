<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります。');

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    //バリデーション
    validRequired($email,'email');
    validRequired($pass,'pass');

    validEmail($email,'email');
    validMaxLen($email,'email');

    validHalf($pass,'pass');
    validMaxLen($pass,'pass');
    validMinLen($pass,'pass');

    
    if(empty($err_msg)){
        debug('バリデーションOKです。');
        $dbh = dbConnect();
        $data = array(':email' => $email);
        $sql = 'SELECT pass,id  FROM user WHERE email = :email AND delete_flg = 0';
        $stmt = queryPost($dbh , $data, $sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        debug('クエリ結果の中身：'.print_r($result,true));

        
        //パスワード照合
        if(!empty($result) && password_verify($pass,array_shift($result))){
            debug('パスワードがマッチしました。');

            $sesLimit = 60*60;
            $_SESSION['login_date'] = time();

            if($pass_save){
                debug('ログイン保持にチェックがあります。');
                $_SESSION['login_limit'] = $sesLimit * 24 * 30;
            }else{
                debug('ログイン保持にチェックはありません。');
                $_SESSION['login_limit'] = $sesLimit;
            }
            
            $_SESSION['user_id'] = $result['id'];

            debug('セッション変数の中身：'.print_r($_SESSION,true));
            debug('マイページへ遷移します。');
            header("Location:mypage.php");
        
        
        }else{
            debug('パスワードがアンマッチです。');
            $err_msg['common'] = MSG09;
        }
    }
}else{
    debug('post送信されていません');
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');


?>


<?php 
$siteTitle = 'ろぐいん';
require('head.php');
?>

<body class="page-login page-1colum">
    
  <!--ヘッダー-->
  <?php  
    require('header.php')
  ?>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">

    <!--メイン-->
    <section id="main">
      <div class="form-container">
        <form method="post" class="form">
          <h2 class="title">ログイン</h2>

          <div class="area-msg">
            <?php phpErrMsg('common'); ?>
          </div>

          <label>
              メールアドレス
              <div class="area-msg">
                <?php phpErrMsg('email'); ?>
              </div>
              <input type="text" name="email" value="<?php dspIpt('email');?>">
          </label>

          <label>
              パスワード
              <div class="area-msg">
                <?php phpErrMsg('pass'); ?>
              </div>
              <input type="password" name="pass" value="<?php dspIpt('pass');?>">
          </label>

          <label>
            <input type="checkbox" name="pass_save">
            次回ログインを省略する
          </label>

          <div class="btn-container">
            <input type="submit" class="btn btn-mid" value="ログイン">
          </div>
          パスワードを忘れた方は<a class="common" href="passRemindSend.php">コチラ</a>
        </form>
      </div>
    </section>
      
  </div>

  <!--フッター-->
  <?php  
    require('footer.php')
  ?>