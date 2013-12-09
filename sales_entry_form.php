<?php
include('settings.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

include('process_form.php');
include('theme/header.php');
//$fetch_data is calculated in process_form.php and is the data required
//to edit a record. It's a bit messy in the HTML below, checking if it needs
//to be inserted or not.
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <form action="sales_entry_form.php" method="post">
        <label for="datepicker">Date</label>
        <p>
          <input type="text" name= "datepicker" id="datepicker" <?php if(isset($fetch_data['event_date'])) { echo ' value="' . $fetch_data['event_date'] . '"'; } ?>/>
        </p>
          
        <select name="event_type">
          <option value="0">--Select--</option>
          <?php
          $sql = "SELECT * FROM `event_type` ORDER BY `name`";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()) {
            if (isset($fetch_data['event_type_id'])) {
              if ($row['id'] == $fetch_data['event_type_id']) {
                $selected = ' selected';
              } else {
                if (isset($selected)) {
                  unset($selected);
                }
              }
            }
            echo '<option value="' . $row['id'] . '"';
            if(isset($selected)) {
              echo $selected;
            }
            echo '>' . $row['name'] . '</option>';
          }
              
          ?>

        </select>
          
        <table class="table table-striped">
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
                echo '<td><input type="number" name="quantity' . $row['id'] . '" min="0" step="1"';
                if (isset($fetch_data['sales'][$row['id']])) {
                  echo 'value="' . $fetch_data['sales'][$row['id']] .'">';
                } else {
                  echo 'value="0">';
                }
                echo '</td>';
              echo '</tr>';
            }
                
            ?>
          </tbody>
        </table>
        <?php if (isset($event_id)): ?>
          <input type="hidden" name="update_event" value="<?php echo $event_id; ?>">
        <?php endif; ?>
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
