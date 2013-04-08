<?php
  include '../blackbox.php';

  $contacts = array();
  $count = 0;

  if (($handle = fopen("mhubinfo.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $params = array(
        "ID" => $data[16], "EmailID" => $data[17], "PhoneID" => $data[18],
        "Contact" => array("first_name" => $data[0], "last_name" => $data[1], "gender_id" => $data[4], "contact_type" => "Individual",
          "custom_57" => $data[11], "custom_59" => $data[13], "custom_60" => $data[12], "custom_61" => $data[14]),
        "Email" => array("email" => $data[3]),
        "Phone" => array("phone" => $data[2]),
        "School" => array("contact_id_b" => $data[10]),
        "Survey" => array("custom_64" => $data[5], "custom_65" => $data[7], "custom_66" => $data[8],
          "custom_67" => $data[9], "custom_126" => $data[6], "custom_83" => $data[15], "custom_120" => 'Manual Import From Mission Hub')
      );
      $contacts[] = $params;
    }
    fclose($handle);
  }
  //var_dump($contacts);
  //$contacts = array_slice($contacts, 0, 5);

  foreach ($contacts as $contact) {    
    if($contact["ID"]){
      try{
        $contactInfo = array_merge($contact["Contact"], array("id" => $contact["ID"]));
        $updateContact = civicrm_call("Contact", "update", $contactInfo);
        if ($updateContact["is_error"] == 1) { throw new Exception($updateContact["error_message"]); }
  
        $primaryParams = array(
          "contact_id" => $contact["ID"], 
          "isPrimary" => "1"
        );
        if(!($contact["PhoneID"]) && $contact["Phone"]["phone"]){
          //echo $contact["ID"] . " needs new phone \n";
          $phoneParams = array_merge($contact["Phone"], $primaryParams);
          $phoneReturn = civicrm_call("Phone", "create", $phoneParams);
          if ($phoneReturn["is_error"] == 1) { throw new Exception($phoneReturn["error_message"]); }
          //var_dump($phoneReturn);
  
        }
        if(!($contact["EmailID"]) && $contact["Email"]["email"]){
          //echo $contact["ID"] . " needs new email \n";
          $emailParams = array_merge($contact["Email"], $primaryParams);
          $emailReturn = civicrm_call("Email", "create", $emailParams);
          //var_dump($emailReturn);
          if ($emailReturn["is_error"] == 1) { throw new Exception($emailReturn["error_message"]); }
        }
  
        $relInfo = civicrm_call("Relationship", "get", array("contact_id_a" => $contact["ID"], "contact_id_b" => $contact["School"]["contact_id_b"]));
        if($relInfo["count"] == 0){
          //echo $contact["ID"] . " needs new relationship\n";
          $schoolParams = array(
            "relationship_type_id" => 10, // Student Currently Attending
            "contact_id_a" => $contact["ID"],
            "contact_id_b" => $contact["School"]["contact_id_b"] 
          );
          $relReturn = civicrm_call("Relationship", "create", $schoolParams);
          if ($relReturn["is_error"] == 1) { throw new Exception($relReturn["error_message"]); }
          //var_dump($relReturn);
        }
  
        $surveyParams = array(
          "source_contact_id" => 1,
          "target_contact_id" => $contact["ID"],
          "activity_type_id" => 32, // petition
          "subject" => 'Mission Hub Survey 2012',
          "status_id" => 2,  // completed
          "campaign_id" => 2 // September 2012 launch
        );
        $sParams = array_merge($contact["Survey"], $surveyParams);
        $surveyReturn = civicrm_call("Activity", "create", $sParams);
        if ($surveyReturn["is_error"] == 1) { throw new Exception($surveyReturn["error_message"]); }
        //var_dump($surveyReturn);

        echo $contact["ID"] . " Complete!\n";
      }
      catch (Exception $e){
        echo $contact["ID"] . " Error: " . $e->getMessage() . "\n";
      }
      
    }
    else{
      try{
        //New contact
        $newContact = civicrm_call("Contact", "create", $contact["Contact"]);
        if ($newContact["is_error"] == 1) { throw new Exception($newContact["error_message"]); }
        $id = $newContact["id"];
  
        $primaryParams = array(
          "contact_id" => $id, 
          "isPrimary" => "1"
        );
  
        if($contact["Email"]["email"]){
          $emailParams = array_merge($contact["Email"], $primaryParams);
          $emailReturn = civicrm_call("Email", "create", $emailParams);
          //var_dump($emailReturn);
          if ($emailReturn["is_error"] == 1) { throw new Exception($emailReturn["error_message"]); }
        }
  
        if($contact["Phone"]["phone"]){
          $phoneParams = array_merge($contact["Phone"], $primaryParams);
          $phoneReturn = civicrm_call("Phone", "create", $phoneParams);
          if ($phoneReturn["is_error"] == 1) { throw new Exception($phoneReturn["error_message"]); }
          //var_dump($phoneReturn);
        }
  
        $schoolParams = array(
          "relationship_type_id" => 10, // Student Currently Attending
          "contact_id_a" => $id,
          "contact_id_b" => $contact["School"]["contact_id_b"] 
          );
        $relReturn = civicrm_call("Relationship", "create", $schoolParams);
        if ($relReturn["is_error"] == 1) { throw new Exception($relReturn["error_message"]); }
        //var_dump($relReturn);
  
        $surveyParams = array(
          "source_contact_id" => 1,
          "target_contact_id" => $id,
          "activity_type_id" => 32, // petition
          "subject" => 'Mission Hub Survey 2012',
          "status_id" => 2,  // completed
          "campaign_id" => 2 // September 2012 launch
        );
        $sParams = array_merge($contact["Survey"], $surveyParams);
        $surveyReturn = civicrm_call("Activity", "create", $sParams);
        if ($surveyReturn["is_error"] == 1) { throw new Exception($surveyReturn["error_message"]); }
        //var_dump($surveyReturn);
        echo $id . " Complete!\n";
      }
      catch (Exception $e){
        echo $id . " Error: " . $e->getMessage() . "\n";
      }
    }
  }
?>