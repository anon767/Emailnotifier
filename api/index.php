<?php
function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
include("./config.php");
include("bootstrap.php");
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 22.11.17
 * Time: 20:56
 */
$router = new AltoRouter();
$router->setBasePath($basepath);

$router->map('GET', '/', function () {
    echo "foshizzlmanizzle";
});

$router->map('GET', '/check/[a:id].png', function ($id) {
    global $entityManager;
    $email = $entityManager->getRepository("Email")->findOneBy(["hash" => $id]);
 if($email->getUseragent() != getRealIpAddr().$_SERVER['HTTP_USER_AGENT'])
 {
    $email->setIsRead(1);
    $email->setTimestamp(time());
    $email->setUseragent(getRealIpAddr().$_SERVER['HTTP_USER_AGENT']);
    $entityManager->persist($email);
    $entityManager->flush();
 }
    header("Content-type: image/png");
    $im = imagecreatefrompng("./placeholder.png");
    imagepng($im);
    imagedestroy($im);    
});

$router->map('GET', '/info/[a:id]', function ($id) {
    global $entityManager;
    echo json_encode($entityManager->getRepository("Email")->findOneBy(["hash" => $id]));
});

$router->map('GET', '/email/new/[a:title]', function ($title) {
    global $entityManager;
    $hash = md5(rand(0, 1000) + "culz");
    $email = new Email();
    $email->setLink($title);
    $email->setHash($hash);
    $email->setIsRead(0);
    $email->setUseragent(getRealIpAddr().$_SERVER['HTTP_USER_AGENT']);
    $email->setTimestamp(0);
    $entityManager->persist($email);
    $entityManager->flush();
    echo $hash;
});
$match = $router->match();
if( $match && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] );
} else {
	echo "Ka-boom! It didn't work.";
}
