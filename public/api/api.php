<?php
$out = '';
$out.= "the GET : " . PHP_EOL ;
$out.= json_encode($_GET);

$out.= PHP_EOL . PHP_EOL . "the POST : " . PHP_EOL ;
$out.= json_encode($_POST);

$out.= PHP_EOL . PHP_EOL . "the INPUT STREAM : " . PHP_EOL ;
$in_stream = file_get_contents("php://input");
$out.= $in_stream;

$out.= PHP_EOL . PHP_EOL . "the Client Info : " . PHP_EOL ;
$client = array(
    'HTTP_USER_AGENT'       => '', // $_SERVER['HTTP_USER_AGENT'],
    'HTTP_ACCEPT_ENCODING'  => '', // $_SERVER['HTTP_ACCEPT_ENCODING'],
    'QUERY_STRING'          => '', // $_SERVER['QUERY_STRING'],
    'REMOTE_ADDR'           => '', // $_SERVER['REMOTE_ADDR'],
    'REMOTE_PORT'           => '', // $_SERVER['REMOTE_PORT']
    'HTTP_REFERER'          => '', // $_SERVER['HTTP_REFERER']
);
foreach ($client as $k => $v) if (isset($_SERVER[$k])) $client[$k] = $_SERVER[$k];
$out.= json_encode($client);

if (!function_exists('getallheaders')) 
{ 
    function getallheaders() 
    { 
           $headers = ''; 
       foreach ($_SERVER as $name => $value) 
       { 
           if (substr($name, 0, 5) == 'HTTP_') 
           { 
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
           } 
       } 
       return $headers; 
    } 
}
$out.= PHP_EOL . PHP_EOL . "the Headers : " . PHP_EOL ;
$headers = getallheaders();
$out.= json_encode($headers);


$file = 'wcout.txt';
file_put_contents($file, $out);

echo str_replace(PHP_EOL, '<br>' . PHP_EOL, $out);;
?>
