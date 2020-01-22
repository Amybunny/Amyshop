<?php 

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

if(!empty($_POST)){

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_retype'];

  validRequired($email,'email');
  validRequired($pass,'pass');
  validRequired($pass_re,'pass_retype');

  if(empty($err_msg)){

    validEmail($email,'email');
    validMaxLen($email,'email');
    validEmailDup($email);

    validMatch($pass,$pass_re,'pass');

    if(empty($err_msg)){

      validHalf($pass,'pass');
      validMinLen($pass,'pass');
      validMaxLen($pass,'pass');

      if(empty($err_msg)){

        try{
          $dbh = dbConnect();
          $sql = 'INSERT INTO user (email,pass,login_time,create_date) 
                  VALUES(:email,:pass,:login_time,:create_date)';
          $data = array(':email' => $email, 
                        ':pass' => password_hash($pass, PASSWORD_DEFAULT), 
                        ':create_date' => date('Y-m-d H:i:s'),
                        ':login_time'  => date('Y-m-d H:i:s'));
          $stmt = queryPost($dbh,$data,$sql);
          debug('ユーザー登録しました。');

          if($stmt){
            $sesLimit = 60*60;
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            $_SESSION['user_id'] = $dbh->lastInsertId();
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            header("Location:mypage.php");//マイページへ
          }else{
            error_log('クエリに失敗しました。');
            debug('クエリに失敗しました。');
            $err_msg['common'] = MSG07;
          }

        } catch (Exception $e) {
          error_log('エラー発生：' . $e->getMessage());
          $err_msg['common'] = MSG07;
        }
      }
    }
  }
}
?>

<?php 
$siteTitle = 'ゆーざーとうろく';
require('head.php');
?>


<body class="page-signup page-1colum">
    
    <!--ヘッダー-->
    <?php  
      require('header.php');
    ?>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

      <!--Main-->
      <section id="main">

        <div class="form-container">

          <form  method="post" class="form">
            <h2 class="title">ユーザー登録</h2>

            <div class="area-msg">
              <?php phpErrMsg('common'); ?>
            </div>

            <label>
              Email
              <div class="area-msg">
                <?php phpErrMsg('email'); ?>
              </div>
              <input type="text" name="email" value="<?php dspIpt('email'); ?>">
            </label>

            <label>
              パスワード <span style="font-size: 12px">※英数字6文字以上</span>
              <div class="area-msg">
                <?php phpErrMsg('pass'); ?>
              </div>
              <input type="password" name="pass"  value="<?php dspIpt('pass');?>">
            </label>

            <label>
              パスワード（再入力）
              <div class="area-msg">
                <?php phpErrMsg('pass_retype'); ?>
              </div>
              <input type="password" name="pass_retype" 
                     value="<?php dspIpt('pass_retype'); ?>">
            </label>

            <div class="btn-container">
              <input type="submit" class="btn btn-mid" value="登録する">
            </div>
          </form>
        </div>
      </section>
    </div>
    

    <!--フッター-->
    <?php  
      require('footer.php')
    ?>