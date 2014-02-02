<?php
include('settings.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

include('process_incoming_stock.php');
include('theme/header.php');
//$fetch_data is calculated in process_incoming_stock.php and is the data required
//to edit a record. It's a bit messy in the HTML below, checking if it needs
//to be inserted or not.
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <div class="alert alert-warning">Are the all prices up to date?</div>
      <h1>Enter Stock</h1>
      <?php
        if (isset($msg)) {
          echo $msg;
        }
      ?>
      <form action="form_incoming_stock.php" method="post">
        <div class="row">
          <div class="span4">
            <label for="datepicker">Date</label>
            <p>
              <input type="text" name= "datepicker" id="datepicker" <?php if(isset($fetch_data['date'])) { echo ' value="' . $fetch_data['date'] . '"'; } ?>/>
            </p>
         </div>
         <div class="span4">
            <label for="supplier">Title/Supplier</label>
            <p>
              <input type="text" name= "supplier" id="supplier" <?php if(isset($fetch_data['supplier'])) { echo ' value="' . htmlspecialchars($fetch_data['supplier']) . '"'; } ?>/>
            </p>
        </div>
        </div>
        <div class="span9">
           <div class="row">
        <label for="notes">Notes</label>
        <textarea class="form-control span6" rows="3" name="notes"><?php if(isset($fetch_data['notes'])) { echo htmlspecialchars($fetch_data['notes']); } ?></textarea>
          </div>
        </div>
        <table class="table table-striped">
          <thead>
            <th>Item</th>
            <th>Cost</th>
            <th>Sells at</th>
            <th>Amount</th>
          </thead>
          <tbody>
          <?php
            $sql = "SELECT * FROM `stock` ORDER BY weight";

            if(!$result = $db->query($sql)){
                die('There was an error running the query [' . $db->error . ']');
            }

            while($row = $result->fetch_assoc()){
              //Don't allow people to input amounts for glasses of wine
              //on incoming stock. They should only add bottles.
              if (in_array($row['id'],$white_wine_ids)) {
                continue;
              }
              if (in_array($row['id'],$red_wine_ids)) {
                continue;
              }
              echo '<tr>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['cost_price'] . '</td>';
                echo '<td>' . $row['retail_price'] . '</td>';
                echo '<td><input type="number" name="quantity' . $row['id'] . '" min="0" step="1"';
                if (isset($fetch_data['stock'][$row['id']])) {
                  echo 'value="' . $fetch_data['stock'][$row['id']] .'">';
                } else {
                  echo 'value="0">';
                }
                echo '</td>';
              echo '</tr>';
            }
                
            ?>
          </tbody>
        </table>
        <?php if (isset($stock_record_id)): ?>
          <input type="hidden" name="update_stock_record" value="<?php echo $stock_record_id; ?>">
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
