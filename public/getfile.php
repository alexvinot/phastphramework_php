<?php


session_start();
  // get photo from files folder for security
  // output jpg or whatever
function sparkles($input_to_clean) {
   $tokens = array(
      'javascript',
      "\\'",
      "'",
      '..',
      '!',
      ',',
      '/'
   );
   
   foreach ($input_to_clean as &$val)
   {
      if (is_array($val))
      {
         $val = sparkles($val);
      }
      else
      {
         $val = trim(str_ireplace($tokens, '', stripslashes(urldecode($val))));
         $val = trim($val, '/');
         $val = trim($val, '.');
      }
   }

   return (array)$input_to_clean;
}

if (isset($_SESSION['login_time']) && isset($_SESSION['uid']) && $_SESSION['uid'] && isset($_REQUEST['f'])) {
    // also need to check for permissions of which files, groups, etc but for now, it's just gotta be logged in
    $root = '../files/';
    $r = sparkles($_REQUEST);
    $f = $r['f'];
    $file = $root . $f;
    $file = str_replace('~', '/', $file);
    if (!file_exists($file) || !is_file($file)) $file = "px/white.gif";
    
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}
?>