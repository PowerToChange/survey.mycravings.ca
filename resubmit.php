<?php
  include 'blackbox.php';

  $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, DBNAME);

  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  if($result = $mysqli->query("SELECT * FROM contacts WHERE status!=\"success\"")){
    while ($row = $result->fetch_assoc()){
      $sqlId = $row["id"];

      $params = array(
        "Contact" => array("first_name" => $row['first_name'], "last_name" => $row['last_name'], 
          "gender_id" => $row['gender_id'], "custom_57" => $row['year'], "custom_58" => $row['year_other'],
          "custom_59" => $row['faculty'], "custom_60" => $row['residence'], "custom_61" => $row['international']),
        "Email" => array("email" => $row['email']),
        "Phone" => array("phone" => $row['phone']),
        "School" => array("contact_id_b" => $row['school']),
        "Survey" => array("custom_64" => $row['general'], "custom_126" => $row['want_other'], "custom_116" => $row['love'],
          "custom_118" => $row['love_other'], "custom_117" => $row['want'], "custom_119" => $row['want_other'],
          "custom_115" => $row['power'], "custom_65" => $row['magazine'], "custom_66" => $row['guage'], 
          "custom_67" => $row['journey'], "custom_83" => $row['notes'], "custom_120" => $row['submitter'])
      );
      //var_dump($params);

      try {
        $connectId = new_contact_calls($params);
        echo "success\n";
        $stmtString = "UPDATE `contacts` SET `status`='success', `civicrm_id`='" . $connectId . "' WHERE `id`=" . $sqlId;
        if(!$mysqli->query($stmtString)){
          throw new Exception($mysqli->error);
        }
      }
      catch (Exception $e){
        echo "failure\n";
        $stmtString = "UPDATE `contacts` SET `status`='failed', `result`='" . $e->getMessage() . "' WHERE `id`=" . $sqlId;
        if(!$mysqli->query($stmtString)){
          throw new Exception($mysqli->error);
        }
      }
    }
  }

  $mysqli->close();

?>