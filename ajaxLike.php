<?php 

require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

//================================
// Ajax処理
//================================
if(isset($_POST['productId']) && isset($_SESSION['user_id']) && isLogin()){
  debug('お気に入りのPOST送信があります');
  $p_id = $_POST['productId'];
  debug('商品ID:'.$p_id);

  $dbh = dbConnect();
  $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
  $sql = 'SELECT * from likes where product_id = :p_id and u_id = :u_id';
  $stmt = queryPost($dbh, $data, $sql);
  $resultCount = $stmt->rowCount();
  debug('$resultCount:'.$resultCount);
  
  if(!empty($resultCount)){
    $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
    $sql = 'DELETE from likes where product_id = :p_id and u_id = :u_id';
    $stmt = queryPost($dbh, $data, $sql);
  }else{
    $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
    $sql = 'INSERT into likes (product_id, u_id) values (:p_id, :u_id)';
    $stmt = queryPost($dbh, $data, $sql);
  }







}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>