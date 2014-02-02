<?php
include('settings.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

include('process_sales_entry_form.php');
include('theme/header.php');
//$fetch_data is calculated in process_sales_entry_form.php and is the data required
//to edit a record. It's a bit messy in the HTML below, checking if it needs
//to be inserted or not.
//print_r($fetch_data);
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <div class="alert alert-error">Are the all prices up to date?</div>
      <h1>Enter Sales</h1>
      <?php
        if (isset($msg)) {
          echo $msg;
        }
      ?>
      <form action="form_sales_entry.php" method="post">
        <label for="datepicker">Date</label>
        <p>
          <input type="text" name= "datepicker" id="datepicker" <?php if(isset($fetch_data['event_date'])) { echo ' value="' . $fetch_data['event_date'] . '"'; } ?>/>
        </p>
        
        <label for="event_type">Event</label>
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
            echo '>' . htmlentities($row['name']) . '</option>';
          }
              
          ?>

        </select>
          
        <table class="table table-striped">
          <thead>
            <th>Item</th>
            <th>Cost (now)</th>
            <th>Sold at</th>
            <th>Number sold</th>
          </thead>
          <tbody>
          <?php
            $sql = "SELECT * FROM `stock` ORDER BY weight";

            if(!$result = $db->query($sql)){
                die('There was an error running the query [' . $db->error . ']');
            }

            while($row = $result->fetch_assoc()){
              //If we are repopulating the form because we are editing, then we replace the 
              //current retail pricve with the price it originally sold at.
              if (isset($fetch_data['retail_price'][$row['id']])) { 
               $row['retail_price'] = $fetch_data['retail_price'][$row['id']];
              }
              
              echo '<tr>';
                echo '<td>' . htmlentities($row['name']) . '</td>';
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
