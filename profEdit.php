<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//ログイン認証
//require('auth.php');

//================================
// 画面処理
//================================
//DBからユーザーデータを取得
if(!empty($_SESSION['user_id'])){
  $dbFormData = getUser($_SESSION['user_id']);
  debug('取得したユーザー情報：'.print_r($dbFormData,true));
}

if(!empty($_POST)){
  //debug('POST送信があります。');
  //debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  $username = $_POST['username'];
  $tel = $_POST['tel'];
  $zip = $_POST['zip'];
  $addr = $_POST['addr'];
  $age = $_POST['age'];
  $email = $_POST['email'];
  $pic= (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'name') : '';
  $pic = ( empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;


  debug('$picの中身：'.$pic);

  if($dbFormData['username'] !== $username){
    validMaxLen($username,'username');
  }

  if($dbFormData['tel'] !== $tel){
    validTel($tel,'tel');
  }
    
  if($dbFormData['addr'] !== $addr){
    validMaxLen($addr,'addr');
  }

  if($dbFormData['zip'] !== $zip){
    validZip($zip,'zip');
  }

  if($dbFormData['age'] !== $age){
    validMaxLen($age,'age');
    validNumber($age,'age');
  }

  if($dbFormData['email'] !==$email){
    validMaxLen($email,'email');
    if(empty($err_msg['email'])){
      validEmailDup($email);
    }
    validEmail($email,'email');
    validRequired($email,'email');
  }

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    $dbh = dbConnect();

    $data = array(':u_name'=>$username, ':tel'=>$tel, ':zip'=>$zip, ':addr'=>$addr, ':age'=>$age, ':email'=>$email, ':pic'=>$pic, ':u_id'=>$dbFormData['id']);

    $sql = 'UPDATE user SET username=:u_name, tel=:tel, zip=:zip, addr=:addr, age=:age, email=:email, pic=:pic
    WHERE id=:u_id';

    $stmt = queryPost($dbh, $data, $sql);

    if($stmt){
      $_SESSION['msg_success'] = SUC02;
      debug('マイページへ遷移します。');
      header("Location:mypage.php"); //マイページへ
    }else{
      $err_msg['common'] = MSG08;
    }
  }
}else{
    debug('POST送信がありません。');
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');


$siteTitle = 'ぷろふぃーるへんしゅう';
require('head.php')
?>

<body class="page-profEdit page-2colum page-logined">

    <!--ヘッダー-->
  <?php  
    require('header.php')
  ?>

  <!--メインコンテンツ-->
  <div id="contents" class="site-width">
    <h1 class="page-title">プロフィール編集</h1>

    <!--Main-->
    <section id="main">
      <div class="form-container">
        <form class="form" method="post" enctype="multipart/form-data">


          <div class="area-msg">
            <?php phpErrMsg('common'); ?>
          </div>




          <label class="<?php errClass('username');?>">
            名前　
            <input type="text" name="username" value="<?php echo getFormData('username');?>">
          </label>
          <div class="area-msg">
            <?php phpErrMsg('username'); ?>
          </div>




          <label class="<?php errClass('tel');?>">
            TEL<span style="font-size:12px;margin-left:5px;">※ハイフン無しでご入力ください</span>　
            <input type="text" name="tel" value="<?php echo getFormData('tel');?>">
          </label>
          <div class="area-msg">
            <?php phpErrMsg('tel'); ?>
          </div>




          <label class="<?php errClass('zip');?>">
            郵便番号　
            <input type="text" name="zip" value="<?php echo getFormData('zip');?>">
          </label>
          <div class="area-msg">
            <?php phpErrMsg('zip'); ?>
          </div>




          <label class="<?php errClass('addr');?>">
            住所　
            <input type="text" name="addr" value="<?php echo getFormData('addr');?>">
          </label>
          <div class="area-msg">
            <?php phpErrMsg('addr'); ?>
          </div>




          <label style="text-align: left" class="<?php errClass('age');?>">
            年齢　
            <input type="number" name="age" value="<?php echo getFormData('age');?>">
          </label>
          <div class="area-msg">
            <?php phpErrMsg('age'); ?>
          </div>




          <label class="<?php errClass('email');?>">
            Email　
            <input type="text" name="email" value="<?php echo getFormData('email');?>">
          </label>
          <div class="area-msg">
            <?php phpErrMsg('email'); ?>
          </div>




          <label class="area-drop prof-img <?php errClass('pic');?>" >
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input type="file" name="pic" class="input-file">
            <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img prof" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
              ドラッグ＆ドロップ
          </label>
          <div class="area-msg">
            <?php phpErrMsg('pic'); ?>
          </div>


          <div class="btn-conatiner">
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