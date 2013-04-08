<?php
  include '../blackbox.php';
  
  //$file = "mhubjson.txt";
  //$contacts = json_decode(file_get_contents($file), true);
  //var_dump($contacts);
  //echo "Count: " . count($contacts["people"]) . "\n";
  //$fp = fopen('mhubcontacts.csv', 'w');
  //foreach ($contacts["people"] as $id => $contact) {
    //echo $contact["id"] . "\n";
  //  fputcsv($fp, array($contact["id"]));
  //}
  //fclose($fp);

  $missing = array();
  $count = 0;

  if (($handle = fopen("missingmhub.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $missing[] = $data[0];
    }
    fclose($handle);
  }
  //var_dump($missing);
  //$missing = array_slice($missing, 0, 5);

  $contacts = array();
  foreach ($missing as $id) {
    $ch = curl_init("https://www.missionhub.com/api/v1/contacts/" . $id . ".json?access_token=" . MHUBV1_TOKEN . "&org_id=6411");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    $reply = curl_exec($ch);
    //var_dump($reply);
    if(!$reply){
      throw new Exception(curl_error($ch));
    }
    curl_close($ch);
    $mhub = json_decode($reply, TRUE);
    //var_dump($mhub);
  
    $person = $mhub["people"][0]["person"];
    $survey = $mhub["people"][0]["form"];

    $phoneID = "";
    $phoneInfo = civicrm_call("Phone", "get", array("phone" => $person["phone_number"]));
    if($phoneInfo["count"] > 0){
      foreach ($phoneInfo["values"] as $pid => $presults) {
        if($phoneID){
          $phoneID = $phoneID . "," . $presults["contact_id"];
        }
        else {
          $phoneID = $presults["contact_id"];
        }
      }
    }
    $emailID = "";
    $emailInfo = civicrm_call("Email", "get", array("email" => $person["email_address"]));
    if($emailInfo["count"] > 0){
      foreach ($emailInfo["values"] as $eid => $eresults) {
        if($emailID){
          $emailID = $emailID . "," . $eresults["contact_id"];
        }
        else {
          $emailID = $eresults["contact_id"];
        }
      }
    }
  
    if(count($survey) == 13){
      $contact = array(
        "mhub_id" => $id,
        "first_name" => $person["first_name"],
        "last_name" => $person["last_name"],
        "phone" => $person["phone_number"],
        "email" => $person["email_address"],
        "gender" => $person["gender"],
        "crave" => $survey[1]["a"],
        "mag" => $survey[2]["a"],
        "guage" => $survey[3]["a"],
        "journey" => $survey[4]["a"],
        "campus" => $survey[6]["a"],
        "year" => $survey[7]["a"],
        "residence" => $survey[8]["a"],
        "major" => $survey[9]["a"],
        "international" => $survey[10]["a"],
        "notes" => $survey[12]["a"],
        "civiEmailId" => $emailID,
        "civiPhoneId" => $phoneID
      );
    }
    else if(count($survey) == 14){
      $contact = array(
        "mhub_id" => $id,
        "first_name" => $person["first_name"],
        "last_name" => $person["last_name"],
        "phone" => $person["phone_number"],
        "email" => $person["email_address"],
        "gender" => $person["gender"],
        "crave" => $survey[2]["a"],
        "mag" => $survey[3]["a"],
        "guage" => $survey[4]["a"],
        "journey" => $survey[5]["a"],
        "campus" => $survey[7]["a"],
        "year" => $survey[8]["a"],
        "residence" => $survey[9]["a"],
        "major" => $survey[10]["a"],
        "international" => $survey[11]["a"],
        "notes" => $survey[13]["a"],
        "civiEmailId" => $emailID,
        "civiPhoneId" => $phoneID
      );
    }
    else {
      $contact = array(
        "mhub_id" => $id,
        "first_name" => "error"
      );
    }

    $contacts[] = $contact;
    echo $id . " Completed " . $count . "\n";
    $count = $count + 1;
  }

  $fp = fopen('mhubinfo.csv', 'w');
  foreach ($contacts as $contact) {
    fputcsv($fp, $contact);
  }
  fclose($fp);

  //var_dump($contacts);

?>