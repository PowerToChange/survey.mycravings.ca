<?php
  include 'blackbox.php';

  $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, DBNAME);

  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  if($mysqli->query("DROP TABLE schools") === TRUE){
    echo "table successfully dropped\n";
  }

  $createTable = "CREATE TABLE `schools` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `contact_id` int(11) DEFAULT NULL,
    `name` varchar(256) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
  if($mysqli->query($createTable) === TRUE){
    echo "table created\n";
  }

  $result = get_schools();
  foreach ($result as $school => $data) {
    $stmt = "INSERT INTO schools (contact_id, name) VALUES (" . $data["contact_id"] . ", \"" . $data["organization_name"] . "\")";
    if($mysqli->query($stmt) === TRUE){
      echo "row created\n";
    }
  }

  $mysqli->close();

?>