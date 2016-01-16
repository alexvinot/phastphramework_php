<?php
session_start();
$thevalueoffield = $defaultvalue;
$uid = 0;
if (isset($_SESSION['uid'] )) $uid = $_SESSION['uid'];
$ds = DIRECTORY_SEPARATOR;
if (isset($_SESSION['upload_directory'])) $storeFolder = $_SESSION['upload_directory'];
// if (isset($_REQUEST['upload_directory'])) $storeFolder = $_POST['upload_directory'];
else $storeFolder = 'uploads';
if ( !empty($_FILES) && $uid ) {
	$tempFile = $_FILES['file']['tmp_name'];
	$targetPath = dirname( __FILE__ ).$ds.$storeFolder.$ds;
    $targetFile =  $targetPath. $_FILES['file']['name']; 
    move_uploaded_file($tempFile, $targetFile);
	echo 'DID IT!';
}
else echo 'FAILED!';
?>    
