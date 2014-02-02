<?php
include('settings.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

include('process_stock_take.php');
include('theme/header.php');
//$fetch_data is calculated in process_stock_take.php and is the data required
//to edit a record. It's a bit messy in the HTML below, checking if it needs
//to be inserted or not.
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Edit Stock Take Record</h1>
      <?php
        if (isset($msg)) {
          echo $msg;
        }
      ?>
       
      <form action="form_stock_take.php" method="post">
        <div class="row">
          <div class="span4">
            <label for="datepicker">Date</label>
            <p>
              <input type="text" name= "datepicker" id="datepicker" <?php if(isset($fetch_data['date'])) { echo ' value="' . $fetch_data['date'] . '"'; } ?>/>
            </p>
          </div>
          <div class="span4">
            <label for="who">Stock Taker</label>
            <p>
              <input type="text" name= "who" id="who" <?php if(isset($fetch_data['who'])) { echo ' value="' . htmlspecialchars($fetch_data['who']) . '"'; } ?>/>
            </p>
          </div>
        </div>
        <div class="row">
          <div class="span9">
            <label for="notes">Notes</label>
              <textarea class="form-control span6" rows="3" id="notes" name="notes"><?php if(isset($fetch_data['notes'])) { echo htmlspecialchars($fetch_data['notes']); } ?></textarea>
          </div>
        </div>
          
        <table class="table table-striped">
          <thead>
            <th>Item</th>
            <th>Cost</th>
            <th>Sells at</th>
            <th>Amount in stock</th>
            <th>Shrinkage</th>
          </thead>
            <tbody>
              <?php
                $sql = "SELECT * FROM `stock` ORDER BY weight";

                if(!$result = $db->query($sql)){
                    die('There was an error running the query [' . $db->error . ']');
                }

                while($row = $result->fetch_assoc()){
                  //print_r($row);
                  echo '<tr>';
                    echo '<td>' . htmlentities($row['name']) . '</td>';
                    echo '<td>' . $row['cost_price'] . '</td>';
                    echo '<td>' . $row['retail_price'] . '</td>';
                    echo '<td><input type="number" name="quantity' . $row['id'] . '" id="quantity' . $row['id'] . '" min="0" step="1" onchange="updateDifference(' . $row['id'] . ')"';
                    if (isset($fetch_data['stock'][$row['id']])) {
                      echo 'value="' . $fetch_data['stock'][$row['id']] .'">';
                    } else {
                      echo 'value="0">';
                    }
                    echo '</td>';
                    echo '<td><input type="number" name="difference' . $row['id'] . '" id="difference' . $row['id'] . '" step="1" onchange="updateStockAmount(' . $row['id'] . ')"';
                    if (isset($fetch_data['shrinkage'][$row['id']])) {
                      echo 'value="' . $fetch_data['shrinkage'][$row['id']] .'">';
                    } else {
                      echo 'value="0">';
                    }
                    echo '</td>';
                  echo '</tr>';
                }
              ?>
            </tbody>
          </table>
          <?php if (isset( $stock_take_record_id)): ?>
            <input type="hidden" name="update_stock_take_record" value="<?php echo  $stock_take_record_id; ?>">
          <?php endif; ?>
          <input type="submit" value="Update">
        </form>
			</div><!--end span10-->
			
      <!--Sidebar-->
			<div class="span2" style="float:left">
        <?php include("theme/sidebar.php");?>
			</div><!--end Sidebar-->
			
  </div><!--end Row-->
</div><!--end Container-->

<?php
include('theme/footer.php');
?>
<script>
  //When you enter a number in the 'Actual stocklevel' field this will 
  //calculate the difference
  function updateDifference(id) {
    var actualElement = document.getElementById("quantity" + id);
    var originalActual = document.getElementById("quantity" + id).defaultValue;
    var actual = document.getElementById("quantity" + id).value;
    var originalDifference = document.getElementById("difference" + id).defaultValue;
    var difference = document.getElementById("difference" + id);
    
    var actualChange = actual - originalActual;
    difference.value = Number(originalDifference) - Number(actualChange);
    //Note a change by changeing the background color
    if (difference.value != Number(originalDifference)) {
      difference.style.backgroundColor = '#fcf8e3';
      actualElement.style.backgroundColor = '#fcf8e3';
    }
    else 
    {
      difference.style.backgroundColor = 'transparent';
      actualElement.style.backgroundColor = 'transparent';
    }
    
  }
  
  function updateStockAmount(id) {
    var differenceElement = document.getElementById("difference" + id);
    var originalDifference = document.getElementById("difference" + id).defaultValue;
    var difference = document.getElementById("difference" + id).value;
    var actual = document.getElementById("quantity" + id);
    var originalActual = document.getElementById("quantity" + id).defaultValue;

    
    
    var differenceChange = difference - originalDifference;
    actual.value = Number(originalActual) - Number(differenceChange);
    //Note a change by changeing the background color
    if (actual.value != Number(originalActual)) {
      differenceElement.style.backgroundColor = '#fcf8e3';
      actual.style.backgroundColor = '#fcf8e3';
    }
    else 
    {
      differenceElement.style.backgroundColor = 'transparent';
      actual.style.backgroundColor = 'transparent';
    }
  }
</script>
