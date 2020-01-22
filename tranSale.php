<?php 

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　販売履歴画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
//debugLogStart();

//ログイン認証
require('auth.php');

$siteTitle = 'はんばいりれき';
require('head.php')
?>

  <body class="page-transale page-2colum page-logined">

    <!--ヘッダー-->
    <?php  
      require('header.php')
    ?>

    <!--メインコンテンツ-->
    <div id="contents" class="site-width">

      <h1 class="page-title">販売履歴</h1>

      <!--Main-->
      <section id="main">
        <a class="common" href="mypage.php">&lt; マイページに戻る</a>
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