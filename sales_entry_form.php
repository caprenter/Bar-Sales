<?php
include('settings.php');
include('process_form.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

include('theme/header.php');
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <form action="sales_entry_form.php" method="post">

        <p>Date: <input type="text" name= "datepicker" id="datepicker" /></p>
          
        <select name="event_type">
          <option value="0">--Select--</option>
          <?php
          $sql = "SELECT * FROM `event_type` ORDER BY `name`";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()){
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
          }
              
          ?>

        </select>
          
        <table>
          <thead>
            <th>Item</th>
            <th>Cost</th>
            <th>Sells at</th>
            <th>Number sold</th>
          </thead>
          <tbody>
          <?php
            $sql = "SELECT * FROM `stock`";

            if(!$result = $db->query($sql)){
                die('There was an error running the query [' . $db->error . ']');
            }

            while($row = $result->fetch_assoc()){
              echo '<tr>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['cost_price'] . '</td>';
                echo '<td>' . $row['retail_price'] . '</td>';
                echo '<td><input type="number" name="quantity' . $row['id'] . '" min="0" step="1" value="0"></td>';
              echo '</tr>';
            }
                
            ?>
          </tbody>
        </table>

        <input type="submit">
        </form>
				
			</div><!--end span10-->
			
      <!--Sidebar-->
			<div class="span2" style="float:left">
        
        <?php include("theme/sidebar.php");?>
        
			</div><!--end Sidebar-->
			
  </div><!--end Row-->




<?php
include('theme/footer.php');
?>
