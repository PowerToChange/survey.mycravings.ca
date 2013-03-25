<?php
  include 'blackbox.php';
  
  if($_POST && $_POST["externpass"] == CRAVEKEY){
    try {
      new_contact($_POST);
      echo "success";
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
  }
?>