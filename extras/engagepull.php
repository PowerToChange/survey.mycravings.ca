<?php
  include '../blackbox.php';

  $contacts = array();
  if (($handle = fopen("egoodinfo.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $params = array(
        "rejoice" => $data[9], "point_person" => $data[5], "interaction" => $data[8], "note" =>$data[10], "ID" => $data[11],
        "Contact" => array("first_name" => $data[1], "last_name" => $data[2], "contact_type" => "Individual",
          "custom_124" => $data[6], "custom_144" => "No", "custom_145" => $data[7]),
        "Email" => array("email" => $data[4]),
        "Phone" => array("phone" => $data[3]),
        "School" => array("contact_id_b" => $data[0])
      );
      $contacts[] = $params;
    }
    fclose($handle);
  }
  //var_dump($contacts);

  foreach ($contacts as $contact){    
    if($contact["ID"]){
      $id = $contact["ID"];
      $contactInfo = array_merge($contact["Contact"], array("id" => $id));
      unset($contactInfo["first_name"]);
      unset($contactInfo["last_name"]);
      $updateContact = civicrm_call("Contact", "update", $contactInfo);
      if ($updateContact["is_error"] == 1) { echo "ERROR: " . $updateContact["error_message"] . "\n"; }
  
      $schoolParams = array(
        "relationship_type_id" => 10, // Student Currently Attending
        "contact_id_a" => $contact["ID"],
        "contact_id_b" => $contact["School"]["contact_id_b"] 
      );
      $relInfo = civicrm_call("Relationship", "get", $schoolParams);
      if($relInfo["count"] == 0){
        //echo $contact["ID"] . " needs new relationship\n";
        $relReturn = civicrm_call("Relationship", "create", $schoolParams);
        if ($relReturn["is_error"] == 1) { echo "ERROR: " . $relReturn["error_message"] . "\n"; }
        //var_dump($relReturn);
      }

      if($contact["rejoice"] == 1){
        $actParams = array(
          "source_contact_id" => $id,
          "target_contact_id" => 1,
          "activity_type_id" => 47, // Rejoiceable
          "subject" => "Rejoiceable",
          "status_id" => 2,  // completed
          "activity_date_time" => "2012-04-30",
          "custom_143" => 3
        );
        //var_dump($actParams);
        $rejReturn = civicrm_call("Activity", "create", $actParams);
        if ($rejReturn["is_error"] == 1) { echo "ERROR: " . $rejReturn["error_message"] . "\n"; }
      }
        
      if($contact["point_person"]){
        $pointParams = array(
          "entity_id" => $id,
          "subject" => "Jan-April 2012 Engagement Point Person",
          "modified_date" => "2012-04-30",
          "note" => $contact["point_person"]
        );
        $pointReturn = civicrm_call("Note", "create", $pointParams);
        if ($pointReturn["is_error"] == 1) { echo "ERROR: " . $pointReturn["error_message"] . "\n"; }
      }

      if($contact["interaction"]){
        $interParams = array(
          "entity_id" => $id,
          "subject" => "Jan-April 2012 Engagement Approx Interaction Number",
          "modified_date" => "2012-04-30",
          "note" => $contact["interaction"]
        );
        $interReturn = civicrm_call("Note", "create", $interParams);
        if ($interReturn["is_error"] == 1) { echo "ERROR: " . $interReturn["error_message"] . "\n"; }
      }

      if($contact["note"]){
        $noteParams = array(
          "entity_id" => $id,
          "subject" => "Jan-April 2012 Engagement Note",
          "modified_date" => "2012-04-30",
          "note" => $contact["note"]
        );
        $noteReturn = civicrm_call("Note", "create", $noteParams);
        if ($noteReturn["is_error"] == 1) { echo "ERROR: " . $noteReturn["error_message"] . "\n"; }
      }  
  
      echo $contact["ID"] . " Complete!\n";
    } 
    else {
      //New contact
      $newContact = civicrm_call("Contact", "create", $contact["Contact"]);
      if ($newContact["is_error"] == 1) { echo "ERROR: " . $newReturn["error_message"] . "\n"; }
      $id = $newContact["id"];
    
      $primaryParams = array(
        "contact_id" => $id, 
        "isPrimary" => "1"
      );
      if($contact["Email"]["email"]){
        $emailParams = array_merge($contact["Email"], $primaryParams);
        $emailReturn = civicrm_call("Email", "create", $emailParams);
        //var_dump($emailReturn);
        if ($emailReturn["is_error"] == 1) { echo "ERROR: " . $emailReturn["error_message"] . "\n"; }
      }
  
      if($contact["Phone"]["phone"]){
        $phoneParams = array_merge($contact["Phone"], $primaryParams);
        $phoneReturn = civicrm_call("Phone", "create", $phoneParams);
        if ($phoneReturn["is_error"] == 1) { echo "ERROR: " . $phoneReturn["error_message"] . "\n"; }
        //var_dump($phoneReturn);
      }
  
      $schoolParams = array(
        "relationship_type_id" => 10, // Student Currently Attending
        "contact_id_a" => $id,
        "contact_id_b" => $contact["School"]["contact_id_b"] 
        );
      $relReturn = civicrm_call("Relationship", "create", $schoolParams);
      if ($relReturn["is_error"] == 1) { echo "ERROR: " . $relReturn["error_message"] . "\n"; }
      //var_dump($relReturn);

      if($contact["rejoice"] == 1){
        $actParams = array(
          "source_contact_id" => $id,
          "target_contact_id" => 1,
          "activity_type_id" => 47, // Rejoiceable
          "subject" => "Rejoiceable",
          "status_id" => 2,  // completed
          "activity_date_time" => "2012-04-30",
          "custom_143" => 3
        );
        //var_dump($actParams);
        $rejReturn = civicrm_call("Activity", "create", $actParams);
        if ($rejReturn["is_error"] == 1) { echo "ERROR: " . $rejReturn["error_message"] . "\n"; }
      }
        
      if($contact["point_person"]){
        $pointParams = array(
          "entity_id" => $id,
          "subject" => "Jan-April 2012 Engagement Point Person",
          "modified_date" => "2012-04-30",
          "note" => $contact["point_person"]
        );
        $pointReturn = civicrm_call("Note", "create", $pointParams);
        if ($pointReturn["is_error"] == 1) { echo "ERROR: " . $pointReturn["error_message"] . "\n"; }
      }

      if($contact["interaction"]){
        $interParams = array(
          "entity_id" => $id,
          "subject" => "Jan-April 2012 Engagement Approx Interaction Number",
          "modified_date" => "2012-04-30",
          "note" => $contact["interaction"]
        );
        $interReturn = civicrm_call("Note", "create", $interParams);
        if ($interReturn["is_error"] == 1) { echo "ERROR: " . $interReturn["error_message"] . "\n"; }
      }

      if($contact["note"]){
        $noteParams = array(
          "entity_id" => $id,
          "subject" => "Jan-April 2012 Engagement Note",
          "modified_date" => "2012-04-30",
          "note" => $contact["note"]
        );
        $noteReturn = civicrm_call("Note", "create", $noteParams);
        if ($noteReturn["is_error"] == 1) { echo "ERROR: " . $noteReturn["error_message"] . "\n"; }
      }

      echo $id . " Complete!\n";
    }
  }

?>