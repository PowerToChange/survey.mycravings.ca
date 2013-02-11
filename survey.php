<?php
  include 'blackbox.php';
  if($_POST){
    var_dump($_POST);
    EXIT;
  }


  switch ($_GET["s"]) {
    case 'love':
      $title = "Love";
      $disp_love = true;
      break;
    case 'power':
      $title = "Power";
      $disp_power = true;
      break;
    default:
      $title = "General";
      $disp_gen = true;
      break;
   } 

 ?>

<html>
<head>
  <title>Power to Change <?php echo $title ?> Survey</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <h1>Power to Change - Students</h1>
  <h2><?php echo $title; ?> Survey</h2>

  <form name="input" action="submit.php" method="post">
  <div class="questions">
    <div class="<?php if(!$disp_gen) echo "hidden"?>">
      <p>The one thing I crave most is:</p>
      <input type="radio" name="Survey[custom_64]" value="warmup-fun">fun</input>
      <input type="radio" name="Survey[custom_64]" value="warmup-relationship">relationship</input>
      <input type="radio" name="Survey[custom_64]" value="warmup-money">money</input>
      <input type="radio" name="Survey[custom_64]" value="warmup-grades">good grades</input>
      <!-- What to do about the other section? -->
    </div>

    <div class="<?php if(!$disp_love) echo "hidden"?>">
      <p>In my everyday experience, love is:</p>
      <input type="radio" name="SurveyMiss[love]" value="love-elusive">elusive</input>
      <input type="radio" name="SurveyMiss[love]" value="love-complicated">complicated</input>
      <input type="radio" name="SurveyMiss[love]" value="love-disappointing">disappointing</input>
      <input type="radio" name="SurveyMiss[love]" value="love-exciting">exciting</input>
      <!-- What to do about the other section? -->

      <p>I want love to be:</p>
      <input type="radio" name="SurveyMiss[want]" value="want-lasting">lasting</input>
      <input type="radio" name="SurveyMiss[want]" value="want-meaningful">meaningful</input>
      <input type="radio" name="SurveyMiss[want]" value="want-easy">easy</input>
      <!-- What to do about the other section? -->
    </div>

    <div class="<?php if(!$disp_power) echo "hidden"?>">
      <p>I would rather:</p>
      <input type="radio" name="SurveyMiss[power]" value="power-corporation">run a major corporation</input>
      <input type="radio" name="SurveyMiss[power]" value="power-technology">invent the next big technology</input>
      <input type="radio" name="SurveyMiss[power]" value="power-backpack">backpack around the world</input>
      <input type="radio" name="SurveyMiss[power]" value="power-family">build a solid family</input>
      <input type="radio" name="SurveyMiss[power]" value="power-sport">be a sporting legend</input>
    </div>


    <p>I'd like a <bold>FREE MAGAZINE BY PERSONAL DELIVERY</bold> to help me explore my craving for:</p>
    <input type="radio" name="Survey[custom_65]" value="magazine-spiritual">spiritual connection</input>
    <input type="radio" name="Survey[custom_65]" value="magazine-justice">a real justice</input>
    <input type="radio" name="Survey[custom_65]" value="magazine-love">love without conditions</input>
    <input type="radio" name="Survey[custom_65]" value="magazine-escape">escape from the dreariness of life</input>
    <input type="radio" name="Survey[custom_65]" value="magazine-success">achievement & success</input>
    <input type="radio" name="Survey[custom_65]" value="magazine-no">no, thanks</input>

    <p>How interested are you in having a chat about how to begin a journey with Jesus Christ?</p>
    <input type="radio" name="Survey[custom_66]" value="gauge-1">1</input>
    <input type="radio" name="Survey[custom_66]" value="gauge-2">2</input>
    <input type="radio" name="Survey[custom_66]" value="gauge-3">3</input>
    <input type="radio" name="Survey[custom_66]" value="gauge-4">4</input>
    <input type="radio" name="Survey[custom_66]" value="gauge-5">5</input>

    <p>On my spiritual journey I'd like to:</p>
    <input type="radio" name="Survey[custom_67]" value="journey-explore">explore the deeper meaning of my cravings</input>
    <input type="radio" name="Survey[custom_67]" value="journey-online">get connected to online resources about my cravings</input>
    <input type="radio" name="Survey[custom_67]" value="journey-p2c">hear more about Power to Change</input>
    <input type="radio" name="Survey[custom_67]" value="journey-grow">grow in my relationship with Jesus</input>
    <input type="radio" name="Survey[custom_67]" value="journey-nothing">do nothing right now</input>
  </div>

  <br><br><br>

  <div class="personal">
    First Name:<input type="text" name="Contact[first_name]">
    Last Name:<input type="text" name="Contact[last_name]">
    <input type="radio" name="Contact[gender]" value="male">Male</input>
    <input type="radio" name="Contact[gender]" value="female">Female</input>
    <br>

    Email:<input type="text" name="Email[email]">
    Phone:<input type="text" name="Phone[phone]">
    <br>

    <input type="radio" name="Contact[custom_57]" value="1">1</input>
    <input type="radio" name="Contact[custom_57]" value="2">2</input>
    <input type="radio" name="Contact[custom_57]" value="3">3</input>
    <input type="radio" name="Contact[custom_57]" value="4">4</input>
    <input type="radio" name="Contact[custom_57]" value="5">5</input>
    <input type="radio" name="Contact[custom_57]" value="grad">Grad</input>
    Other Year:<input type="text" name="Contact[custom_58]">
    <br>

    Faculty/Degree:<input type="text" name="Contact[custom_59]">
    On Campus Residence:<input type="text" name="Contact[custom_60]">
    <br>

    <input type="checkbox" name="Contact[custom_61]" value="yes" default="no">I am an international student

    <br><br>
    Submitter:<input type="text" name="submitter"><br>
    School: 
    <select name="School[contact_id_b]">
      <option value="none" disabled selected>Select</option>
      <?php
        $reply = get_schools();
        foreach ($reply as $school => $data) {
          echo "<option value=\"" . $data["contact_id"] . "\">" . $data["organization_name"] . "</option>\n";
        }
      ?>
    </select>


  </div>

  <div style="clear:both;"></div>
  <input type="submit" value="Submit">
  </form>

</body>
</html>