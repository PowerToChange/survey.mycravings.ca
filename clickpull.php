<?php
  include 'blackbox.php';

  $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, CLICKDB);
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  $num = 0;
  $query = "SELECT * FROM yourls2_url WHERE clicks>0 and (keyword like '%satisfaction%' OR ".
  	"keyword like '%sneakpeekmag%' OR keyword like '%chatsaboutjesus%' OR keyword like '%manvsvendingmachine%')";

  if($result = $mysqli->query($query)){
    while ($row = $result->fetch_assoc()){
      $url = "http://p2c.com/" . $row["keyword"];

      $conParams = array(
        "custom_80" => $url
      );
      $conReturn = civicrm_call("Contact", "get", $conParams);
      if($conReturn["count"] > 0){
        $id = current(array_keys($conReturn["values"]));
        $actParams = array(
          "source_contact_id" => 1,
          "target_contact_id" => $id,
          "activity_type_id" => 46, // Link Click
          "subject" => $url,
          "status_id" => 2  // completed
        );
        $dupReturn = civicrm_call("Activity", "get", $actParams);
        if($dupReturn["count"] == 0){
          $actReturn = civicrm_call("Activity", "create", $actParams);
          if ($actReturn["is_error"] == 1) { echo "ERROR: " . $surveyReturn["error_message"] . "\n"; }
          else { 
            echo "Activity created for ID: " . $id . " with " . $url . "\n";
            $num = $num + 1;
          }
          //var_dump($actReturn);
        }
        else { echo "Activity already exists for " . $url . "\n"; }
      }
      else { echo "ERROR: " . $url . " didn't have a corresponding id\n"; }
    }
  }

  echo $num . " activities added";
?>