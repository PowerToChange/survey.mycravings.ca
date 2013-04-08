<?php
  include '../blackbox.php';


  $url = "https://www.missionhub.com/apis/v3/answers?secret=" . MHUBV3_SECRET;
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 600);
  $reply = curl_exec($ch);
  if(!$reply){
    throw new Exception(curl_error($ch));
  }
  curl_close($ch);

  $mhub = json_decode($reply, TRUE);
  var_dump($mhub);
  //$file = "mhubjson.txt";
  //file_put_contents($file, json_encode($mhub));
 ?>