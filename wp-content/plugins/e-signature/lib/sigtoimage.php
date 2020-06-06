<?php
function imageto($json){
require_once 'signature-to-image.php';


$img = sigJsonToImage($json);


// Output to browser
header('Content-Type: image/png');
imagepng($img);
// Destroy the image in memory when complete
imagedestroy($img);
}

$userid=isset($_GET['uid'])?$_GET['uid']:$_GET['owner_id'];


$doc_id=$_GET['doc_id'];
$is_pdf= isset($_GET['is_pdf'])? $_GET['is_pdf'] : false; 

//$esig_nonce = $_GET['esig_verify'];


  ob_start();
  $file_name ='../assets/temps/'.$userid.'-'.$doc_id .'.txt' ; 
  $json = file_get_contents($file_name) ;
  unlink($file_name);
  ob_clean();
  

 imageto($json,$is_pdf);
    
?>

