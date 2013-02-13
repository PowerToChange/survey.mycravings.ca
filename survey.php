<?php
  include 'blackbox.php';

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
  <title>Power to Change <?php echo $title; ?> Survey</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="style.css">
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
  <script src="jquery.validate.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#surveyTitle").change(function () {
        $("#generalDiv").addClass("hidden");
        $("#powerDiv").addClass("hidden");
        $("#loveDiv").addClass("hidden");
        var good_div = "#" + $(this).val() + "Div";
        $(good_div).removeClass("hidden");
      });

      $.validator.addMethod("phoneUS", function(phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, ""); 
        return this.optional(element) || phone_number.length > 9 &&
        phone_number.match(/^(1-?)?\.?(\([2-9]\d{2}\)|[2-9]\d{2})-?\.?[2-9]\d{2}-?\.?\d{4}$/);
        }, "Please specify a valid phone number");

      $.validator.addMethod("notYear", function(year, element) {
        return !year || year && !($("input[name=\"Contact[custom_57]\"]:checked").val());
        }, "Do not give two years");


      $("#surveyForm").validate({
        rules : {
          "Email[email]": {
            email: true
          },
          "Phone[phone]": {
            phoneUS: true
          },
          "Contact[custom_58]": {
            notYear: true
          }
        }
      });
    });
  </script>
</head>
<body>
  <h1>Power to Change - Students</h1>

  <form name="input" id="surveyForm" action="submit.php" method="post">
  <select id="surveyTitle" name="s">
    <option value="general" <?php if($disp_gen) {echo "selected"; } ?>>General Survey</option>
    <option value="power" <?php if($disp_power) {echo "selected"; } ?>>Power Survey</option>
    <option value="love" <?php if($disp_love) {echo "selected"; } ?>>Love Survey</option>
  </select>
  <br style="clear:both">

  <!--<input type="hidden" name="s" value="<?php echo $_GET["s"]; ?>">-->

  <div class="questions">
    <div id="generalDiv" class="<?php if(!$disp_gen) echo "hidden"?>">
      <p>The one thing I crave most is:</p>
      <input type="radio" name="Survey[custom_64]" value="warmup-fun">fun</input>
      <input type="radio" name="Survey[custom_64]" value="warmup-relationship">relationship</input>
      <input type="radio" name="Survey[custom_64]" value="warmup-money">money</input>
      <input type="radio" name="Survey[custom_64]" value="warmup-grades">good grades</input>
      <!-- What to do about the other section? -->
    </div>

    <div id="loveDiv" class="<?php if(!$disp_love) echo "hidden"?>">
      <p>In my everyday experience, love is:</p>
      <input type="radio" name="Survey[custom_116]" value="love-elusive">elusive</input>
      <input type="radio" name="Survey[custom_116]" value="love-complicated">complicated</input>
      <input type="radio" name="Survey[custom_116]" value="love-disappointing">disappointing</input>
      <input type="radio" name="Survey[custom_116]" value="love-exciting">exciting</input>
      <label for="loveOther">Other:</label>
      <input type="text" id="loveOther" name="Survey[custom_118]">

      <p>I want love to be:</p>
      <input type="radio" name="Survey[custom_117]" value="want-lasting">lasting</input>
      <input type="radio" name="Survey[custom_117]" value="want-meaningful">meaningful</input>
      <input type="radio" name="Survey[custom_117]" value="want-easy">easy</input>
      <label for="wantOther">Other:</label>
      <input type="text" id="wantOther" name="Survey[custom_119]">
    </div>

    <div id="powerDiv" class="<?php if(!$disp_power) echo "hidden"?>">
      <p>I would rather:</p>
      <input type="radio" name="Survey[custom_115]" value="power-corporation">run a major corporation</input>
      <input type="radio" name="Survey[custom_115]" value="power-technology">invent the next big technology</input>
      <input type="radio" name="Survey[custom_115]" value="power-backpack">backpack around the world</input>
      <input type="radio" name="Survey[custom_115]" value="power-family">build a solid family</input>
      <input type="radio" name="Survey[custom_115]" value="power-sport">be a sporting legend</input>
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
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="Contact[first_name]">
    
    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="Contact[last_name]">
    <br>

    <label for="male">Male</label>
    <input type="radio" id="male" name="Contact[gender_id]" value="2">
    <label for="female">Female</label>
    <input type="radio" id="female" name="Contact[gender_id]" value="1">
    <br>

    <label for="email">Email:</label>
    <input type="text" id="email" name="Email[email]">
    <br>
    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="Phone[phone]">
    <br>

    Year
    <label for="year1">1</label>
    <input type="radio" id="year1" name="Contact[custom_57]" value="1">
    <label for="year2">2</label>
    <input type="radio" id="year2" name="Contact[custom_57]" value="2">
    <label for="year3">3</label>
    <input type="radio" id="year3" name="Contact[custom_57]" value="3">
    <label for="year4">4</label>
    <input type="radio" id="year4" name="Contact[custom_57]" value="4">
    <label for="year5">5</label>
    <input type="radio" id="year5" name="Contact[custom_57]" value="5">
    <label for="yeargrad">Grad</label>
    <input type="radio" id="yeargrad" name="Contact[custom_57]" value="grad">
    <label for="yearother">Other Year:</label>
    <input type="text" id="yearother" name="Contact[custom_58]">
    <br>

    <label for="faculty">Faculty/Degree:</label>
    <input type="text" id="faculty" name="Contact[custom_59]">
    <label for="residence">On Campus Residence:</label>
    <input type="text" id="residence" name="Contact[custom_60]">
    <br>

    <label for="inter">I am an international student</label>
    <input type="checkbox" id="inter" name="Contact[custom_61]" value="yes" default="no">
    <br>
    <label for="notes" style="vertical-align:top">Notes:</label>
    <textarea id="notes" name="Survey[custom_83]" rows="4" cols="50"></textarea>

    <br><br>
    <label for="submitter">Submitter:</label>
    <input type="text" id="submitter" name="Survey[custom_120]" value="<?php echo $_GET["submitter"]; ?>" required><br>
    <label for="school">School:</label>
    <select id="school" name="School[contact_id_b]" required>
      <option value="none" disabled selected>Select</option>
      <?php
        $mysqli = new mysqli(DBLOCATION, DBUSER, DBPASS, DBNAME);
        $result = $mysqli->query("SELECT * FROM schools");
        while ($row = mysqli_fetch_array($result)) {
          $selected = ($row["contact_id"] == $_GET["school"]) ? "selected" : "";
          echo "<option value=\"" . $row["contact_id"] . "\" " . $selected . ">" . $row["name"] . "</option>\n";
        }
      ?>
    </select>


  </div>

  <div style="clear:both;"></div>
  <input type="submit" value="Submit">
  </form>

</body>
</html>