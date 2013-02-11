<?php
  include 'civi_constants.php';

  $postData = array(
    "json" => "1",
    "PHPSESSID" => "",
    "api_key" => "1",
    "key" => KEY,
  );

  function http_call($params){
    $ch = curl_init(RESTURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POST,count($params));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
    $reply = curl_exec($ch);
    curl_close($ch);

    return json_decode($reply, TRUE);
  }

  function login(){
    global $postData;
    if($postData["PHPSESSID"] == ""){
      $loginData = array(
        "q" => "civicrm/login",
        "key" => KEY,
        "json" => "1",
        "name" => USERNAME,
        "pass" => PASSWORD
        );
      $return = http_call($loginData);
      $postData["PHPSESSID"] = $return["PHPSESSID"];
    }
  }

  function civicrm_call($entity, $action, $params){
    global $postData;
    login();

    $addData = array(
      "entity" => $entity,
      "action" => $action
      );

    $allParams = array_merge($postData, $addData, $params);
    return http_call($allParams);
    
  }

  function new_contact($params){
    $contact = civicrm_call("Contact", "create", $params["Contact"]);
    $id = $contact["values"]["contact_id"];

    $primaryParams = array(
      "contact_id" => $id, 
      "isPrimary" => "1"
      );

    $emailParams = array_merge($params["Email"], $primaryParams);
    civicrm_call("Email", "create", $emailParams);

    $phoneParams = array_merge($params["Phone"], $primaryParams);
    civicrm_call("Phone", "create", $phoneParams);

    $relParams = array(
      "relationship_type_id" => 10, // Student Currently Attending
      "contact_id_a" => $id,
      "contact_id_b" => $params["School"]["contact_id_b"] 
      );
    civicrm_call("Relationship", "create", $schoolParams);

    $date = "some special format date";
    $surveyParams = array(
      "source_contact_id" => 1,
      "target_contact_id" => $id,
      "activity_type_id" => 32, // petition
      "activity_date_time" => $date,
      "subject" => 'Mission Hub Survey 2012',
      "status_id" => 2,  // completed
      "campaign_id" => 2 // September 2012 launch
      );

  }

  function sortByOrg($a, $b) {
    return strcmp($a['organization_name'], $b['organization_name']);
}

  function get_schools(){
    global $postData;
    login();
    $schoolData = array(
      "entity" => "Contact",
      "action" => "get",
      "rowCount" => "1000",
      "contact_sub_type" => "School",
      "return" => "organization_name"
      );

    $allParams = array_merge($postData, $schoolData);
    $reply = http_call($allParams);
    $return = $reply["values"];
    usort($return, "sortByOrg");
    return $return;
  }


  //civicrm_call("Contact", "get", array("id" => "50000"));

?>
