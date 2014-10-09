<?php

header('Content-Type: text/html');
//Fill your email account and your password
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


$qry_str = "?start_date=2014-03-01&end_date=2014-11-01";

//$qry_str = "?updated_after=1371482503.646541";

$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
	'Authorization: UserToken ' . $access_key,
    );
$ch2 = curl_init();

$geturl = 'https://cloudapi.beddit.com/api/v1/user/' . $user_Id. '/sleep'. $qry_str;
//echo $geturl;
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
//echo 'Operation completed without any errors';
}
echo "\n";
//echo $result;

$data = json_decode($result);
echo '<table border="1" width="900">';
echo '<tbody>';

		echo '<tr bgcolor="#A9F5F2" bordercolor="green">';
        echo '<td align="center">Date </td>';
        echo '<td align="center">Sleep duration (h)</td>';
		echo '<td align="center">Tags</td>';
		echo '<td align="center">Sleep latency (min)</td>';
		echo '<td align="center">Resting Heart rate </td>';
		echo '<td align="center" >Score amount of sleep</td>';
        //echo '<td align="center">Score Sleep latency</td>';
		echo '<td align="center">Away Episode Count</td>';
		
        echo '</tr>';

$resting_heart_rate_sum=0;
$stage_duration_S_sum=0;
$number_of_sleeps=0;
$sleep_with_error=0;
foreach($data as $record)
{
		
		if ($record->properties->stage_duration_S == 0)
		{
			echo '<tr bgcolor="#F8E0E0">';
			$sleep_with_error++; 
		}
		else
		{
			$number_of_sleeps++;
			echo '<tr>';
		}

        echo '<td align="center">' . $record->date . '</td>';
        
        $hour = $record->properties->stage_duration_S/3600;
        $min = round((round($record->properties->stage_duration_S/3600,2)-intval($hour))*60,0);
        
        echo '<td align="center">' . intval($hour) .':'. sprintf("%02d", $min) .'</td>';
		$stage_duration_S_sum += round($record->properties->stage_duration_S/3600,2);
		
		echo '<td align="center">';
		
		for ($i=0; $i<count($record->tags); $i++) 
		{
			echo $record->tags[$i] . " ";
		}
		echo '</td>';
		
		
		if (isset($record->properties->sleep_latency)==true)
		{
			echo '<td align="center">' . $record->properties->sleep_latency/60 . '</td>';
		}
		else
		{
			echo '<td align="center">NA</td>';
		}
		if (isset($record->properties->sleep_latency)==true)
		{
			echo '<td align="center">' . round($record->properties->resting_heart_rate,0) . '</td>';
			$resting_heart_rate_sum+= $record->properties->resting_heart_rate;		
		}
		else
		{
			echo '<td align="center">NA</td>';
		}
		echo '<td align="center">' . $record->properties->score_amount_of_sleep . '</td>';
       //echo '<td align="center">' . $record->properties->score_sleep_latency . '</td>';
		echo '<td align="center">' . $record->properties->away_episode_count . '</td>';		
        echo '</tr>';
	
	//echo $record->date ." ". $record->properties->score_amount_of_sleep;
	
	//echo "\n";
	

}
echo '<tr bgcolor="#A9F5F2" bordercolor="green">';
        echo '<td align="center">Sleep Duration Mean </td>';
        echo '<td align="center">Resting Heart Rate Mean</td>';
		echo '<td align="center">Number of Sleeps</td>';
		echo '<td align="center">Sleep in Error (min)</td>';
		echo '</tr>';
echo '<tr>';
echo '<td align="center">' . round($stage_duration_S_sum/$number_of_sleeps,2) . '</td>';
echo '<td align="center">' . round($resting_heart_rate_sum/$number_of_sleeps,2) . '</td>';
echo '<td align="center">' . $number_of_sleeps . '</td>';
echo '<td align="center">' . $sleep_with_error . '</td>';
echo '</tr>';

echo '</tbody>';
echo '</table>';


}

//echo $result;

?>
