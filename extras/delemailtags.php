<?php
  include '../blackbox.php';

  $params = array(
    "tag" => 20,
    "rowCount" => 2000
  );
  $result = civicrm_call("Contact", "get", $params);
  echo "Count: " . $result["count"] . "\n";

  $params2 = array(
    "tag" => 21,
    "rowCount" => 2000
  );
  $result2 = civicrm_call("Contact", "get", $params2);
  echo "Count2: " . $result2["count"] . "\n";

  $params3 = array(
    "tag" => 21,
    "rowCount" => 2000,
    "offset" => 2000
  );
  $result3 = civicrm_call("Contact", "get", $params3);
  echo "Count3: " . $result3["count"] . "\n";

  $conArray = array_merge($result["values"], $result2["values"], $result3["values"]);
  echo "Final Count: " . count($conArray) . "\n\n";
  //var_dump($conArray);
  
  $num=0;
  foreach($conArray as $id => $res){
    if($res["email_id"]){
      $eParams = array(
        "email_id" => $res["email_id"]
      );
      $eRes = civicrm_call("Email", "delete", $eParams);
      if ($eRes["is_error"] == 1) { echo "ERROR: " . $eRes["error_message"] . "\n"; }
      else { 
        echo "Email " . $res["email"] . " deleted on contact " . $res["id"] . "\n"; 
        $num = $num + 1;
      }
    }
  }
  echo $num . " emails deleted";
?>