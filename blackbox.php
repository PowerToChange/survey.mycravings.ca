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
    $cParams = array(
      "contact_type" => "Individual",
      "contact_sub_type" => "Student",
      "source" => "Input Form"
      );
    $conParams = array_merge($params["Contact"], $cParams);
    if(!$conParams["first_name"] && !$conParams["last_name"] && !$conParams["email"]){
      $conParams["first_name"] = "NoValue";
    }
    //var_dump($conParams);
    $contact = civicrm_call("Contact", "create", $conParams);
    if ($contact["is_error"] == 1) { throw new Exception($contact["error_message"]); }
    $id = $contact["id"];
    //var_dump($contact);

    $primaryParams = array(
      "contact_id" => $id, 
      "isPrimary" => "1"
      );

    if($params["Email"]["email"]){
      $emailParams = array_merge($params["Email"], $primaryParams);
      //var_dump($emailParams);
      $emailReturn = civicrm_call("Email", "create", $emailParams);
      //var_dump($emailReturn);
      if ($emailReturn["is_error"] == 1) { throw new Exception($emailReturn["error_message"]); }
    }

    if($params["Phone"]["phone"]){
      $phoneParams = array_merge($params["Phone"], $primaryParams);
      //var_dump($phoneParams);
      $phoneReturn = civicrm_call("Phone", "create", $phoneParams);
      //if ($phoneReturn["is_error"] == 1) { throw new Exception($phoneReturn["error_message"]); }
      var_dump($phoneReturn);
    }


    $schoolParams = array(
      "relationship_type_id" => 10, // Student Currently Attending
      "contact_id_a" => $id,
      "contact_id_b" => $params["School"]["contact_id_b"] 
      );
    //var_dump($relParams);
    $relReturn = civicrm_call("Relationship", "create", $schoolParams);
    if ($relReturn["is_error"] == 1) { throw new Exception($relReturn["error_message"]); }
    //var_dump($relReturn);

    $surveyParams = array(
      "source_contact_id" => 1,
      "target_contact_id" => $id,
      "activity_type_id" => 32, // petition
      "subject" => 'Cravings Survey 2013',
      "status_id" => 2,  // completed
      "campaign_id" => 2 // September 2012 launch
      );
    $sParams = array_merge($params["Survey"], $surveyParams);
    //var_dump($sParams);
    $surveyReturn = civicrm_call("Activity", "create", $sParams);
    if ($surveyReturn["is_error"] == 1) { throw new Exception($surveyReturn["error_message"]); }
    //var_dump($surveyReturn);
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

  function new_high_school_contact($params){
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
      "relationship_type_id" => 12, // High School Student starting at
      "contact_id_a" => $id,
      "contact_id_b" => $params["School"]["contact_id_b"] 
      );
    civicrm_call("Relationship", "create", $schoolParams);

  }


  //civicrm_call("Contact", "get", array("id" => "50000"));

?>
