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
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POST,count($params));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
    $reply = curl_exec($ch);
    if(!$reply){
      throw new Exception(curl_error($ch));
    }
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

  function new_contact($params) {
    if(!$params["Contact"]["first_name"] && !$params["Contact"]["last_name"] && !$params["Contact"]["email"]){
      $params["Contact"]["first_name"] = "NoValue";
      $params["Contact"]["last_name"] = date("Y-m-d H:i:s"); 
    }
    $contact = $params["Contact"];
    $survey = $params["Survey"];
    $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, DBNAME);
    if (mysqli_connect_errno()) {
      throw new Exception($mysqli->connect_error);
    }

    if($stmt = $mysqli->prepare("INSERT INTO `contacts` (`first_name`, `last_name`, `gender_id`, `email`, `phone`, `year`, `year_other`, `faculty`,
      `residence`, `international`, `notes`, `submitter`, `school`, `general`, `warmup_other`, `love`, `love_other`, `want`, `want_other`, `power`,
      `magazine`, `guage`, `journey`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'started')")){
      $stmt->bind_param("sssssssssssssssssssssss", $contact["first_name"], $contact["last_name"], $contact["gender_id"], $params["Email"]["email"],
        $params["Phone"]["phone"], $contact["custom_57"], $contact["custom_58"], $contact["custom_59"], $contact["custom_60"], $contact["custom_61"],
        $survey["custom_83"], $survey["custom_120"], $params["School"]["contact_id_b"], $survey["custom_64"], $survey["custom_126"],
        $survey["custom_116"], $survey["custom_118"], $survey["custom_117"], $survey["custom_119"], $survey["custom_115"], $survey["custom_65"],
        $survey["custom_66"], $survey["custom_67"]);
      $stmt->execute();
      $stmt->close();
      $sqlId = $mysqli->insert_id;
    }

    try {
      $connectId = new_contact_calls($params);
      $stmtString = "UPDATE `contacts` SET `status`='success', `civicrm_id`='" . $connectId . "' WHERE `id`=" . $sqlId;
      if(!$mysqli->query($stmtString)){
        throw new Exception($mysqli->error);
      }
    }
    catch (Exception $e){
      $stmtString = "UPDATE `contacts` SET `status`='failed', `result`='" . $e->getMessage() . "' WHERE `id`=" . $sqlId;
      if(!$mysqli->query($stmtString)){
        throw new Exception($mysqli->error);
      }
    }

  }

  function new_contact_calls($params){
    $cParams = array(
      "contact_type" => "Individual",
      );
    $conParams = array_merge($params["Contact"], $cParams);
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
      $emailReturn = civicrm_call("Email", "create", $emailParams);
      //var_dump($emailReturn);
      if ($emailReturn["is_error"] == 1) { throw new Exception($emailReturn["error_message"]); }
    }

    if($params["Phone"]["phone"]){
      $phoneParams = array_merge($params["Phone"], $primaryParams);
      $phoneReturn = civicrm_call("Phone", "create", $phoneParams);
      if ($phoneReturn["is_error"] == 1) { throw new Exception($phoneReturn["error_message"]); }
      //var_dump($phoneReturn);
    }

    $schoolParams = array(
      "relationship_type_id" => 10, // Student Currently Attending
      "contact_id_a" => $id,
      "contact_id_b" => $params["School"]["contact_id_b"] 
      );
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
    $surveyReturn = civicrm_call("Activity", "create", $sParams);
    if ($surveyReturn["is_error"] == 1) { throw new Exception($surveyReturn["error_message"]); }
    //var_dump($surveyReturn);

    return $id;
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
?>
