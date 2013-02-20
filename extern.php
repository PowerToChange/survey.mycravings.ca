<?php
  include 'blackbox.php';
  
  if($_POST){
    try {
      new_contact($_POST);
      echo "success";
    }
    catch(Exception $e){
      echo $e->getMessage();
    }
  }
?>