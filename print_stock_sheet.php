<?php
include('settings.php');
$wine = array_merge(array_values($white_wine_ids),array_values($red_wine_ids)); //set in settings.php
//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

//include('process_stock_items.php');
include('theme/header.php');
//$fetch_data is calculated in _update_stock_items.php and is the data required
//to edit a record. It's a bit messy in the HTML below, checking if it needs
//to be inserted or not.
?>


<div class="container">
  <div class="row">
    <div class="span4">
      <p>Date</p>
    </div>
    <div class="span4">
      <p>Stocktaker(s)</p>
    </div>
  </div>
  <div class="row">
    <div class="span12" style="float:right">
      <h1>Stock Sheet</h1>
        <table class="table table-striped">
          <thead>
            <th>Item</th>
            <th>Stockroom</th>
            <th>Fridge</th>
            <th>Total</th>
          </thead>
          <tbody>
          <?php
            $sql = "SELECT * FROM `stock` ORDER BY weight";

            if(!$result = $db->query($sql)){
                die('There was an error running the query [' . $db->error . ']');
            }

            while($row = $result->fetch_assoc()){
             if (in_array($row['id'],$wine)) {
                continue;
              }
              echo '<tr>';
                echo '<td>' . htmlentities($row['name']) . '</td>';
                echo '<td> </td>';
                echo '<td> </td>';
                echo '<td> </td>';
              echo '</tr>';
            }
                
            ?>

          </tbody>
        </table>
				
			</div><!--end span12-->
			
  </div><!--end Row-->
  

<?php
include('theme/footer.php');

?>
