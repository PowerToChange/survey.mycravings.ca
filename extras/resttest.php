<?php
  include '../blackbox.php';

  $actParams = array(
    "contact_type" => "Individual",
    "first_name" => "RTest7",
    "last_name" => "RTest7",
    "custom_7" => "Testing"
  );
  //var_dump($actParams);
  $return = civicrm_call("Contact", "create", $actParams);
  var_dump($return);
  if ($return["is_error"] == 1) { echo "ERROR: " . $return["error_message"] . "\n"; }

?>