<?php
  include '../blackbox.php';

  /*$contact = array(
    "first_name" => "testNest1",
    "last_name" => "testNest1",
    "contact_type" => "Individual",
    "api.email.create[email]" => "blah@blah.bad"
  );
  $return = civicrm_call("Contact", "create", $contact);
  var_dump($return);*/

  /*$act = array(
  	"id" => 103178,
    "assignee_contact_id" => 58516
  );
  $return2 = civicrm_call("Activity", "update", $act);
  var_dump($return2);*/

  /*$contact = array(
    "contact_id" => 42872,
    "api.activity.get[activity_type_id]" => 32
  );
  $return = civicrm_call("Contact", "get", $contact);
  var_dump($return);*/


  //To get Activities
  $act = array(
    "id" => 49799,
    "activity_type_id" => 32,
    "return.custom_64" => 1,
    "return.assignee_contact_id" => 1,
    "return.target_contact_id" => 1
  );
  //"id,custom_64,custom_65,custom_66,custom_67,custom_75,custom_76,custom_77,custom_78,custom_83,custom_115,custom_116,custom_117,custom_118,custom_119,custom_120,custom_126"
  $return = civicrm_call("Activity", "get", $act);
  var_dump($return);

  foreach($return["values"] as $act_id => $activity){
    $con = array(
      "contact_id" => $activity["target_contact_id"][0],
      "return" => "first_name,last_name,gender,phone,email,custom_57,custom_58,custom_59,custom_60,custom_61"
    );
    $conRet = civicrm_call("Contact", "get", $con);
    var_dump($conRet);

    $rel = array(
      "contact_id_a" => $activity["target_contact_id"][0],
      "relationship_type_id" => 10 // Student Currently Attending
    );
    $relRet = civicrm_call("Relationship", "get", $rel);
    var_dump($relRet);
  }
?>