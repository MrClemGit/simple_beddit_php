<?php

header('Content-Type: text/plain');
//Fill your 
$postfields = array('grant_type'=>'password', 'username'=>'XXXXX@XXXXX.XXXX' ,'password'=>'XXXXXXXX');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://cloudapi.beddit.com/api/v1/auth/authorize');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$result = curl_exec($ch);

if(curl_exec($ch) === false) {
echo 'Curl error: ' . curl_error($ch);
} else {
//echo 'Operation completed without any errors';


$auth = json_decode($result);
$access_key = $auth->access_token;
$user_Id = $auth->user;


$qry_str = "?start_date=2014-01-01&end_date=2014-08-01";
//$qry_str = "?updated_after=1371482503.646541";

$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
	'Authorization: UserToken ' . $access_key,
    );
$ch2 = curl_init();

$geturl = 'https://cloudapi.beddit.com/api/v1/user/' . $user_Id. '/sleep'. $qry_str;
echo $geturl;
curl_setopt($ch2, CURLOPT_URL, $geturl); 
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers );
curl_setopt($ch2, CURLOPT_HEADER, 0);

curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
$result = curl_exec($ch2);
echo "\n";

if(curl_exec($ch2) === false) {
echo 'Curl error: ' . curl_error($ch2);
} else {
echo 'Operation completed without any errors';
}
echo "\n";
echo $result;





}

//echo $result;

?>
