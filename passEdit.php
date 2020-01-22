<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード変更画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
//debug('取得したユーザー情報:'.print_r($userData,true));

if(!empty($_POST)){
  //debug('POST送信があります。');
  //debug('POST情報:'.print_r($_POST,true));

  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];

  validRequired($pass_old,'pass_old');
  validRequired($pass_new,'pass_new');
  validRequired($pass_new_re,'pass_new_re');

  if(empty($err_msg)){
    //debug('未入力チェックOK。');

    //古いパスワードのチェック
    validPass($pass_old,'pass_old');
    //新しいパスワードのチェック
    validPass($pass_new,'pass_new');

    //古いパスワードとDBパスワードを照合(DBに入っているデータと同じであれば、
    //半角英数字チェックや最大文字数チェックは行わなくても問題ない)
    if(!password_verify($pass_old,$userData['pass'])){
      $err_msg['pass_old'] = MSG13;
    }

    //新しいパスワードと古いパスワードが同じかチェック
    if($pass_old === $pass_new){
      $err_msg['pass_new'] = MSG14;
    }

    //パスワードとパスワード再入力が合っているかチェック(ログイン画面では最大、最小チェックもしていたが、
    //パスワードのほうでチェックしているので実は必要ない)
    validMatch($pass_new,$pass_new_re,'pass_new_re');

    if(empty($err_msg)){
      //debug('バリデーションOK。');

      try {
        $dbh = dbConnect();
        $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
        $sql = 'UPDATE user SET pass = :pass WHERE id = :id';
        $stmt = queryPost($dbh, $data, $sql);

        if($stmt){
          $_SESSION['msg_success'] = SUC01;
          header("Location:mypage.php");//マイページへ
        }else{
          $err_msg['common'] = MSG07;
        }

      } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }
  }
}





$siteTitle = 'ぱすわーどへんこう';
require('head.php')
?>

<body class="page-passEdit page-2colum page-logined">
    <style>
        .form{
            margin-top: 50px;
        }
    </style>

    <!--ヘッダー-->
    <?php  
      require('header.php')
    ?>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">
      <h1 class="page-title">パスワード変更</h1>
      <!--Main-->
      <section id="main">
        <div class="form-container">
          <form method="post" class="form">
            



            <div class="area-msg">
              <?php echo phpErrMsg('common'); ?>
            </div>




            <label class = '<?php errClass('pass_old'); ?>'>
              現在のパスワード　
              <div class="area-msg">
                <?php echo phpErrMsg('pass_old'); ?>
              </div>
              <input type="password" name="pass_old" value = <?php echo getFormData('pass_old'); ?>>
            </label>

            <label class = '<?php errClass('pass_new'); ?>'>
              新しいパスワード　
              <div class="area-msg">
                <?php echo phpErrMsg('pass_new'); ?>
              </div>
              <input type="password" name="pass_new" value = <?php echo getFormData('pass_new'); ?>>
            </label>

            <label class = '<?php errClass('pass_new_re'); ?>'>
              新しいパスワード（再入力）　
              <div class="area-msg">
                <?php echo phpErrMsg('pass_new_re'); ?>
              </div>
              <input type="password" name="pass_new_re" value = <?php echo getFormData('pass_new_re'); ?>>
            </label>

            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="変更する">
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
    <?php  
      require('footer.php')
    ?>