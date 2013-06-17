<?php
  include '../blackbox.php';

  function notes(){
    $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, "pulse");
    if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
    }
  
    //todo: switcsh person_id to involved student civi_id
    $query = "SELECT n.content, p.civicrm_id, n.created_at, s.connect_id FROM notes n, sept2012_contacts s, cim_hrdb_person p WHERE n.noteable_id=s.id AND n.noteable_type='SurveyContact' AND p.person_id=n.person_id";
    $query = $query . " AND n.noteable_id = 646";
    echo $query;
    $noteNum = 0;
    if($result = $mysqli->query($query)){
      while ($row = $result->fetch_assoc()){
        $noteParams = array(
          "entity_id" => $row["connect_id"],
          "subject" => "2012 Followup",
          "note" => $row["content"],
          "modified_date" => $row["created_at"],
          "contact_id" => $row["civicrm_id"]
        );
        //var_dump($noteParams);
        $noteReturn = civicrm_call("Note", "create", $noteParams);
        if ($noteReturn["is_error"] == 1) { echo "ERROR: " . $noteReturn["error_message"] . "\n"; }
        else { 
          echo "Note created for ID: " . $row["connect_id"] . "\n";
          $noteNum = $noteNum + 1;
        }
      }
    }
    echo $noteNum . " notes added\n";
  }


  function activities(){
    $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, "pulse");
    if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
    }

    $rQuery = "SELECT a.activity_type_id, p.civicrm_id, a.created_at, s.connect_id FROM activities a, sept2012_contacts s, cim_hrdb_person p WHERE a.reportable_id = s.id AND p.person_id = a.reporter_id AND a.reportable_type='SurveyContact'";
    $rQuery = $rQuery . " AND s.id = 646";
    $rejNum = 0;
    if($rResult = $mysqli->query($rQuery)){
      while ($row = $rResult->fetch_assoc()){
        $actParams = array(
          "source_contact_id" => $row["civicrm_id"],
          "target_contact_id" => $row["connect_id"],
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
          echo "Rejoiceable created for ID: " . $row["connect_id"] . "\n";
          $rejNum = $rejNum + 1;
        }
      }
    }
    echo $rejNum . " rejoiceables added\n";
  }

  function other(){
    $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, "pulse");
    if (mysqli_connect_errno()) {
      printf("Connect failed: %s\n", mysqli_connect_error());
      exit();
    }

    //Next Steps
    $sQuery = "SELECT s.connect_id, s.status, s.result, s.nextStep, p.civicrm_id FROM sept2012_contacts s, cim_hrdb_person p WHERE s.person_id = p.person_id";
    $sQuery = $sQuery . " AND s.id = 646";
    $septNum = 0;
    if($sResult = $mysqli->query($sQuery)){
      while ($row = $sResult->fetch_assoc()){
        $nextParams = array(
          "contact_id" => $row["connect_id"],
          "custom_124" => $row["nextStep"]
        );
        //var_dump($nextParams);
        $conReturn = civicrm_call("Contact", "create", $nextParams);
        if ($conReturn["is_error"] == 1) { echo "ERROR: " . $conReturn["error_message"] . "\n"; }
        else { 
          echo "Next Step created for ID: " . $row["connect_id"] . "\n";
          $septNum = $septNum + 1;
        }

        switch ($row["status"]) {
          case 2:
          case 3:
            $status = 2;
            break;
          case 1:
          case 4:
            $status = 3;
          default:
            $status = 4;
            break;
        }
        switch ($row["result"]) {
          case 7:
            $index = 1;
            break;
          case 6:
            $index = 2;
            break;
          case 5:
            $index = 3;
            break;
          case 4:
            $index = 4;
            break;
          case 3:
            $index = 8;
            break;
          case 2:
            $index = 9;
            break;
          case 1:
            $index = 10;
            break;
          default:
            $index = 0;
            break;
        }
  
        $actParams = array(
          "contact_id" => $row["connect_id"]
        );
        $actReturn = civicrm_call("Activity", "get", $actParams);
        //var_dump($actReturn);
        if($actReturn["count"] > 0){
          foreach($actReturn["values"] as $id => $res){
            if($res["activity_type_id"] == 32){
              $statusParams = array(
                "id" => $res["id"],
                "status_id" => $status,
                "engagement_level" => $index,
                "assignee_contact_id" => $row["civicrm_id"]
              );
              $statusReturn = civicrm_call("Activity", "update", $statusParams);
              if ($statusReturn["is_error"] == 1) { echo "ERROR: " . $statusReturn["error_message"] . "\n"; }
              else { echo "Activity updated for ID: " . $row["connect_id"] . "\n"; }
            }
          }
        }
      }
    }
  }

  //notes();
  //activities();
  //other();

?>