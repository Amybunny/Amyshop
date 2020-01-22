<?php 
//================================
// ログ
//================================
error_reporting(E_ALL);
date_default_timezone_set('Asia/Tokyo');
ini_set('display_errors', 'On');
ini_set('log_errors','on');
ini_set('error_log','php.log');


//================================
// デバッグ
//================================
//デバッグフラグ
$debug_flg = true;

//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
session_save_path("C:/var/tmp/"); 
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_lifetime',60*60*24*30);
session_start();
session_regenerate_id();

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}


//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワード(再入力)が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '電話番号の形式が違います');
define('MSG11', '郵便番号の形式が違います');
define('MSG12', '年齢の形式が違います');
define('MSG13', '現在のパスワードが違います');
define('MSG14', '古いパスワードと同じです');
define('MSG15', '文字で入力してください');
define('MSG16', '正しくありません');
define('MSG17', '有効期限が切れています');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');

//================================
// ログイン認証
//================================
function isLogin(){
  //ログインしている場合
  if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');

    //現在日時が最終ログイン日時＋有効期限を超えていた場合
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
      debug('ログイン有効期限オーバーです');

      //セッションを削除（ログアウトする）
      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限内です');
      return true;
    }
  }else{
    debug('未ログインユーザーです');
    return false;
  }
}

//================================
// データベース
//================================
//DB接続関数
function dbConnect(){
  $dsn = 'mysql:dbname=freemarket;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}

//SQL実行関数
function queryPost($dbh,$data,$sql){
  $stmt = $dbh->prepare($sql);
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    return 0;
  }
  //debug('クエリ成功。');
  return $stmt;
}

//ユーザー情報取得関数
function getUser($u_id){
  //debug('ユーザー情報を取得します。');
  try{
    $dbh = dbConnect();
    $data = array(':u_id' => $u_id);
    $sql = 'SELECT * FROM user WHERE id = :u_id';
    $stmt = queryPost($dbh, $data, $sql);
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//カテゴリデータ取得関数
function getCategory(){
  //debug('カテゴリー情報を取得します。');
  try {
    $dbh = dbConnect();
    $sql = 'SELECT * FROM category';
    $data = array();
    $stmt = queryPost($dbh, $data, $sql);
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//トップページの商品情報取得関数
function getProductList($currentMinNum = 1, $category, $sort, $span = 20){
  try{
    $dbh = dbConnect();
    $data = array();
    //件数用SQL
    $sql = 'SELECT id FROM product';
    if(!empty($category)) $sql .= ' WHERE category = '.$category;
    //debug('件数用SQL:'.$sql);
    if(!empty($sort)){
      switch($sort){
        case 1:
          $sql .= ' ORDER BY price ASC';
          break;
        case 2:
          $sql .= ' ORDER BY price DESC';
          break;
      }
    }
    $stmt = queryPost($dbh,$data,$sql);
    $rst['total'] = $stmt->rowCount();
    $rst['total_page'] = ceil($rst['total']/$span);
    if(!$stmt){
      return false;
    }

    //ページング用SQL
    $sql = 'SELECT * FROM product';
    if(!empty($category)) $sql .= ' WHERE category = '.$category;
    if(!empty($sort)){
      switch($sort){
        case 1:
          $sql .= ' ORDER BY price ASC';
          break;
        case 2:
          $sql .= ' ORDER BY price DESC';
          break;
      }
    }
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();
    $stmt = queryPost($dbh,$data,$sql);
    if($stmt){
      // クエリ結果のデータを全レコードを格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }
  }catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//商品詳細ページの商品情報取得関数
function getProductOne($p_id){
  try{
    $dbh = dbConnect();
    $data = array(':p_id' => $p_id);
    $sql = 'SELECT p.id, p.p_name, p.comment, p.price, p.pic1, p.pic2, p.pic3, p.user_id, p.create_date, p.update_date, c.name as category from product as p 
            left join category as c on p.category = c.id where p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
    $stmt = queryPost($dbh, $data, $sql);
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  }catch(Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//メッセージ情報取得関数
function getMsgsAndBoard($id){
  debug('msg情報を取得します');
  debug('掲示板ID：'.$id);
  try{
    $dbh = dbConnect();
    $data = array(':id' => $id);
    $sql = 'SELECT p_id,sale_user,buy_user,msg,messages.id as msg_id, send_time,to_user,from_user from board left join messages on messages.board_id=board.id where board.id = :id';
    $stmt = queryPost($dbh, $data, $sql);
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

//お気に入り情報取得関数
function isLike($u_id,$p_id){
  debug('お気に入り情報があるか確認します');
  debug('ユーザーID：'.$u_id);
  debug('商品ID：'.$p_id);
  try{
    $dbh = dbConnect();
    $data = array(':u_id' => $u_id, ':p_id' => $p_id);
    $sql = 'SELECT * from likes where product_id = :p_id and u_id = :u_id';
    $stmt = queryPost($dbh, $data, $sql);
    if($stmt->rowCount()){
      debug('お気に入りです');
      return true;
    }else{
      debug('特に気に入っていません');
      return false;
    }
  }catch(Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//自分の商品情報取得関数
function getMyProducts($u_id){
  debug('自分の商品情報を取得します');
  try{
    $dbh = dbConnect();
    $data = array(':u_id' => $u_id);
    $sql = 'SELECT * from product where user_id = :u_id';
    $stmt = queryPost($dbh, $data, $sql);
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

//自分のメッセージ情報取得関数
function getMyMsgsAndBoard($u_id){
  debug('自分のmsg情報を取得します');
  try{
    $dbh = dbConnect();
    $data = array(':id' => $u_id);
    $sql = 'SELECT * from board as b where b.sale_user = :id or b.buy_user = :id';
    $stmt = queryPost($dbh, $data, $sql);
    $rst = $stmt->fetchAll();
    if(!empty($rst)){
      foreach($rst as $key => $val){

        $sale_user = $val['sale_user'];
        $buy_user = $val['buy_user'];
        if($sale_user == $_SESSION['user_id']){
          $to_user_id = $buy_user;
        }else{
          $to_user_id = $sale_user;
        }
        $data = array(':to_user_id' => $to_user_id);
        $sql = 'SELECT username from user where id = :to_user_id';
        $stmt = queryPost($dbh, $data, $sql);
        $to_user_name = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach($to_user_name as $hoge => $huga){
          $rst[$key]['partner_name'] = $huga;
        }

        $data = array(':id' => $val['id']);
        $sql = 'SELECT * from messages where board_id = :id order by send_time desc';
        $stmt = queryPost($dbh, $data, $sql);
        $rst[$key]['msg'] = $stmt->fetchAll();

      }
    }
    if($stmt){
      return $rst;
    }else{
      return false;
    }
  }catch(Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}


//自分のお気に入り情報取得関数
function getMyLike($u_id){
  debug('自分のお気に入り情報を取得します');
  try{
    $dbh = dbConnect();
    $data = array(':u_id' => $u_id);
    $sql = 'SELECT product_id as p_id, p_name, price, pic1 from likes as l left join product as p on l.product_id = p.id where l.u_id = :u_id';
    $stmt = queryPost($dbh, $data, $sql);
    if($stmt){
      return $stmt->fetchAll();
    }else{
      return false;
    }
  }catch(Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}


//================================
// バリデーション関数
//================================
//配列$err_msgを用意
$err_msg = array();

//バリデーション関数(未入力チェック)
function validRequired($str,$key){
  if(empty($str)){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//バリデーション関数(Email形式チェック)
function validEmail($str,$key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}

//バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  try{
    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM user WHERE email = :email';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh, $data, $sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  }catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}

//バリデーション関数(同値チェック)
function validMatch($str1,$str2,$key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}

//バリデーション関数(半角チェック)
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}

//バリデーション関数(最小文字数チェック)
function validMinLen($str,$key,$min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}

//バリデーション関数(最大文字数チェック)
function validMaxLen($str,$key,$max = 256){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}

//電話番号形式チェック
function validTel($str,$key){
  if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}

//郵便番号形式チェック
function validZip($str,$key){
  if(!preg_match("/^\d{7}$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}

//半角数字チェック
function validNumber($str,$key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}

//パスワードチェック
function validPass($str,$key){
  validHalf($str,$key);
  validMaxLen($str,$key);
  validMinLen($str,$key);
}

//固定長チェック
function validLength($str,$key,$len = 8){
  if(mb_strlen($str) !== $len){
    global $err_msg;
    $err_msg[$key] = $len.MSG14;
  }
}

//================================
// その他
//================================

//SESSIONを1回だけ取得できる
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    debug('$dataの中身：'.print_r($data,true));
    $_SESSION[$key] = '';
    return $data;
  }
}

//認証キー生成
function makeRandKey($length  = 8){
  static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789';
  $str = '';
  for ($i = 0; $i < $length; ++$i){
    $str .= $chars[mt_rand(0,61)];
  }
  return $str;
}

//メール送信
function sendMail($from,$to,$subject,$comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){

    mb_language("Japanese");
    mb_internal_encoding("UTF-8");

    $result = mb_send_mail($to,$subject,$comment,"From:".$from);
    if($result){
      debug('メールを送信しました。');
    }else{
      debug('【エラー発生】メールの送信に失敗しました。');
    }
  }
}

//phpエラーメッセージ表示
function phpErrMsg($str){
  global $err_msg;
  if(!empty($err_msg[$str])) echo $err_msg[$str];
}

//入力内容保持
function dspIpt($str){
  if(!empty($_POST[$str])) echo $_POST[$str];
}

//エラークラス付与
function errClass($str){
  global $err_msg;
  if(!empty($err_msg[$str])) echo 'err';
}

//フォーム入力保持関数
function getFormData($str ,$flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;

  if(isset($method[$str])){
    return $method[$str];
  }elseif(empty($method[$str]) && !empty($dbFormData[$str])){
    return $dbFormData[$str];
  }
}

// サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

//画像表示用関数
function showImg($path){
  if(empty($path)){
    return 'img/sample-img.png';
  }else{
    return $path;
  }
}

//GETパラメータ付与
// $del_key : 付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array()){
  if(!empty($_GET)){
    $str = '?';
    foreach($_GET as $key => $val){
      if(!in_array($key,$arr_del_key,true)){ //取り除きたいパラメータじゃない場合にurlにくっつけるパラメータを生成
        $str .= $key.'='.$val.'&';
      }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
  }
}

function pagination($currentPageNum, $totalPageNum,$link = '', $pageColNum = 5){
  if($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  }elseif($currentPageNum == ($totalPageNum-1) && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  }elseif($currentPageNum == 2 && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  }elseif($currentPageNum == 1 && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}

//画像処理
function uploadImg($file,$key){
  debug('画像アップロード処理開始');

  $path = 'uploads/'.$file[$key];
  move_uploaded_file($file['tmp_name'],$path);
  debug('ファイルは正常にアップロードされました。');
  debug('ファイルパス：'.$path);

  return $path;
}


?>