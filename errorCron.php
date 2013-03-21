<?php

  require('mandrill/Mandrill.php');
  require('civi_constants.php');

  $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, DBNAME);
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }

  if($result = $mysqli->query("SELECT count(*) as num FROM contacts WHERE status!=\"success\"")){
    $row = $result->fetch_assoc();
    if($row['num'] > 0){
      $request_json = '{"type":"messages","call":"send","message":{"text": "There are errors in the survey.mycravings.ca submissions", "subject": "SURVEY ERROR", "from_email": "team@mycravings.ca", "from_name": "SURVEY ERROR", "to":[{"email": "colby.warkentin@p2c.com", "name": "Colby Warkentin"}],"headers":{"...": "..."},"track_opens":true,"track_clicks":true,"auto_text":true,"url_strip_qs":true,"metadata":["..."]}}';
      $ret = Mandrill::call((array) json_decode($request_json));
      print_r($ret);
    }
  }

?>