<?php 

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

debug('ログインしているユーザー：'.session_id());

//================================
// 画面処理
//================================
if(!empty($_POST)){
  debug('POST送信があります。');
  $dbh =dbConnect();
  $data = array(':us_id' => $_SESSION['user_id']);
  $sql1 = 'UPDATE user SET delete_flg = 1 WHERE id = :us_id';
  $stmt = queryPost($dbh,$data,$sql1);

  debug('退会しました。');

  if($stmt){
    $_SESSION = array();
    if(!empty($_COOKIE[session_name()])){
      setcookie(session_name(),'',time()-42000,'/');
      debug('クッキー削除');
    }
    session_destroy();
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('トップページへ遷移します。');
    header("Location:index.php");
  }else{
    debug('クエリが失敗しました。');
    $err_msg['common'] = MSG07;
  }
}
debug('POST送信がありません');
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php 
$siteTitle = 'たいかい';
require('head.php')
?>

  <body class="page-withdraw page-1colum">
    <style>
      .form .btn{
        float: none;
      }
      .form{
        text-align: center;
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
          <form method="post" action="withdraw.php" class="form">
            <h2 class="title">退会</h2>
            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="退会する" name="submit">
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