<?php
  include '../blackbox.php';

  $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, "pulse");
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  $query = "SELECT * FROM contacts";
  //$query = $query . " WHERE civicrm_id IS NOT NULL";
  if($result = $mysqli->query($query)){
    while ($row = $result->fetch_assoc()){
      $inter = ($row["international"]) ? "yes" : "no";
      $contact = array(
        "first_name" => $row["first_name"], "last_name" => $row["last_name"], "contact_type" => "Individual",
        "gender_id" => $row["gender_id"], "custom_124" => $row["next_step_id"], "custom_144" => $row["active"],
        "custom_145" => $row["what_i_am_trusting_god_to_do_next"], "custom_61" => $inter
      );
      if($row["mobile_phone"]) {
        $contact["api.phone.create[phone]"] = $row["mobile_phone"];
      }
      if($row["email"]) {
        $contact["api.email.create[email]"] = $row["email"];
      }
      //var_dump($contact);

      $conReturn = civicrm_call("Contact", "create", $contact);
      if ($conReturn["is_error"] == 1) { echo "ERROR: " . $conReturn["error_message"] . "\n"; }
      else {
        echo "Contact created with id " . $conReturn["id"] . "\n";
        $getSchoolId = array(
          "contact_type" => "Organization",
          "external_identifier" => $row["campus_id"]
        );
        $getSchooolReturn = civicrm_call("Contact", "get", $getSchoolId);
        if($getSchooolReturn){
          $schoolParams = array(
            "relationship_type_id" => 10, // Student Currently Attending
            "contact_id_a" => $conReturn["id"],
            "contact_id_b" => $getSchooolReturn["id"]
          );
          $relReturn = civicrm_call("Relationship", "create", $schoolParams);
          if ($relReturn["is_error"] == 1) { echo "ERROR: " . $relReturn["error_message"] . "\n"; }
          else { echo "School created\n"; }
        }
        else { echo "Failed to create school relationship\n"; }

        //specials
        disc_notes($mysqli, $row["id"], $conReturn["id"]);
        disc_rejoice($mysqli, $row["id"], $conReturn["id"]);
        exit;
      }
    }
  }


  function disc_notes($mysqli, $id, $civi_contact){
    //todo: switcsh person_id to involved student civi_id
    $query = "SELECT n.content, p.civicrm_id, n.created_at FROM notes n, cim_hrdb_person p WHERE n.noteable_type='Contact' AND p.person_id=n.person_id";
    $query = $query . " AND n.noteable_id = " . $id;
    //echo $query . "\n";
    $noteNum = 0;
    if($result = $mysqli->query($query)){
      while ($row = $result->fetch_assoc()){
        $noteParams = array(
          "entity_id" => $civi_contact,
          "subject" => "2012 Discover",
          "note" => $row["content"],
          "modified_date" => $row["created_at"],
          "contact_id" => $row["civicrm_id"]
        );
        //var_dump($noteParams);
        $noteReturn = civicrm_call("Note", "create", $noteParams);
        if ($noteReturn["is_error"] == 1) { echo "ERROR: " . $noteReturn["error_message"] . "\n"; }
        else { 
          echo "Note created for ID: " . $civi_contact . "\n";
          $noteNum = $noteNum + 1;
        }
      }
    }
    echo $noteNum . " notes added to " . $id . "\n";
  }


  function disc_rejoice($mysqli, $id, $civi_contact){
    $rQuery = "SELECT a.activity_type_id, p.civicrm_id, a.created_at FROM activities a, cim_hrdb_person p WHERE p.person_id = a.reporter_id AND a.reportable_type='Contact'";
    $rQuery = $rQuery . " AND a.reportable_id = " . $id;
    $rejNum = 0;
    if($rResult = $mysqli->query($rQuery)){
      while ($row = $rResult->fetch_assoc()){
        $actParams = array(
          "source_contact_id" => $row["civicrm_id"],
          "target_contact_id" => $civi_contact,
          "activity_type_id" => 47, // Rejoiceable
          "subject" => "Rejoiceable",
          "status_id" => 2,  // completed
          "activity_date_time" => $row["created_at"],
          "custom_143" => $row["activity_type_id"]
        );
        //var_dump($actParams);
        $rejReturn = civicrm_call("Activity", "create", $actParams);
        if ($rejReturn["is_error"] == 1) { echo "ERROR: " . $rejReturn["error_message"] . "\n"; }
        else { 
          echo "Rejoiceable created for ID: " . $civi_contact . "\n";
          $rejNum = $rejNum + 1;
        }
      }
    }
    echo $rejNum . " rejoiceables added\n";
  }

?>