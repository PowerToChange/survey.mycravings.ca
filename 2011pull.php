<?php
  include 'blackbox.php';
  
  $count = 1;
  $offset = 200;
  while ($count > 0){
    $params = array(
      "group" => 6,
      "offset" => $offset,
      "rowCount" => 100,
      "return" => "custom_10,custom_11,custom_12,custom_13,custom_14,custom_15,custom_16,custom_17,custom_18,custom_19," .
                  "custom_30,custom_32,custom_37,custom_38,custom_39,custom_40,custom_41,custom_42,custom_43"
    );
    $result = civicrm_call("Contact", "get", $params);
    $count = $result["count"];
    //var_dump($result);
  
    foreach($result["values"] as $id => $res){
      $status = "4";
      $priority = "5";
      //Priority
      switch($res["custom_37"]){
        case "6":
          $priority = "4";
          break;
        case "5":
          $priority = "3";
          break;
        case "4":
        case "3":
          $priority = "2";
          break;
        case "2":
        case "1":
          $priority = "1";
          break;
      }
    
      //Status
      if($res["custom_38"]){
        switch($res["custom_38"]){
          case "1":
            $status = "4";
            break;
          case "2":
            $status = "3";
            break;
          case "3":
            $status = "2";
            break;
        }
      }
  
      $crave = "";
      if($res["custom_10"]){
        $crave = implode("",$res["custom_10"]);
        $crave = "" . $crave . "";
      }
  
      $mag = "";
      if($res["custom_11"]){
        $mag = implode("",$res["custom_11"]);
        $mag = "" . $mag . "";
      }
  
      $who = "";
      if($res["custom_12"]){
        $who = implode("",$res["custom_12"]);
        $who = "" . $who . "";
      }
  
      //MeaningEscapeGod
  
    
      $survey = array(
        "custom_127" => $crave,
        "custom_128" => $mag,
        "custom_129" => $who,
        "custom_130" => $res["custom_17"],
        "custom_131" => $res["custom_13"],
        "custom_132" => $res["custom_15"],
        "custom_133" => $res["custom_16"],
        "custom_134" => $res["custom_30"],
        "custom_135" => $res["custom_42"],
        "priority_id" => $priority,
        "status_id" => $status,
        "source_contact_id" => 1,
        "target_contact_id" => $res["id"],
        "activity_type_id" => 45, // Fall 2011
        "subject" => 'Fall 2011 Survey',
      );
      //var_dump($survey);
     
      $year= "";
      if($res["custom_19"] && $res["custom_19"] != "Other"){
        $year = strtolower($res["custom_19"]);
      }
      if($res["custom_39"]){
        $faculty = $res["custom_39"];
      }
      else {
        $faculty = $res["custom_18"];
      }

      $international = "no";
      if($res["custom_40"] == "1"){
        $international = "yes";
      }
    
      $contact = array(
        "id" => $res["id"],
        "contact_sub_type" => "Student",
        "custom_57" => $year,
        "custom_61" => $international,
        "custom_60" => $res["custom_32"],
        "custom_59" => $faculty
      );
      //var_dump($contact);

      $activity = civicrm_call("Activity", "create", $survey);
      if ($activity["is_error"] == 1) { echo $res["id"] . " Activity Error: " . $activity["error_message"] . "\n"; }
      $studentDemo = civicrm_call("Contact", "create", $contact);
      if ($studentDemo["is_error"] == 1) { echo $res["id"] . " Contact Error: " . $studentDemo["error_message"] . "\n"; }
      if($res["custom_43"]){
        $noteData = array(
          "entity_id" => $res["id"],
          "note" => $res["custom_43"]
        );
        //var_dump($noteData);
        $note = civicrm_call("Note", "create", $noteData);
        if ($note["is_error"] == 1) { echo $res["id"] . " Note Error: " . $note["error_message"] . "\n"; }
        //var_dump($note);
      }

      //var_dump($activity);
      //var_dump($studentDemo);
      echo "Completed ID " . $res["id"] . "\n";
    }
    $offset = $offset + 100;
    echo "Offset: " . $offset . "\n";
  }
  echo "Done\n";
?>