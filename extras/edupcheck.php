<?php
  include '../blackbox.php';

  $num = 0;
  $fp = fopen('edupinfo.csv', 'w');
  if (($handle = fopen("engageinfo.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $phoneID = "";
      if($data[3]){
        $phoneInfo = civicrm_call("Phone", "get", array("phone" => $data[3]));
        if($phoneInfo["count"] > 0){
          foreach ($phoneInfo["values"] as $pid => $presults) {
            if($phoneID){
              $phoneID = $phoneID . "-" . $presults["contact_id"];
            }
            else {
              $phoneID = $presults["contact_id"];
            }
          }
        }
      }
      $emailID = "";
      if($data[4]){
        $emailInfo = civicrm_call("Email", "get", array("email" => $data[4]));
        if($emailInfo["count"] > 0){
          foreach ($emailInfo["values"] as $eid => $eresults) {
            if($emailID){
              $emailID = $emailID . "-" . $eresults["contact_id"];
            }
            else {
              $emailID = $eresults["contact_id"];
            }
          }
        }
      }

      if($emailID || $phoneID){
        $num = $num + 1;
      }
      $data[11] = $phoneID;
      $data[12] = $emailID;
      //var_dump($data);
      fputcsv($fp, $data);
      echo "hit count: " . $num . "\n";
    }
    fclose($handle);
  }
    fclose($fp);
?>