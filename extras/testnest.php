<?php
  include '../blackbox.php';

  $contact = array(
    "first_name" => "testNest1",
    "last_name" => "testNest1",
    "contact_type" => "Individual",
    "api.email.create[email]" => "blah@blah.bad"
  );
  $return = civicrm_call("Contact", "create", $contact);
  var_dump($return);

  /*$act = array(
  	"id" => 103178,
  	"asignees[0]" => 58516
  );
  $return2 = civicrm_call("Activity", "update", $act);
  var_dump($return2);*/
?>