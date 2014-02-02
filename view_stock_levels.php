<?php
include('settings.php');
include('functions/functions.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

include('theme/header.php');
$last_stock_take_data = last_stock_take_data(date("Y-m-d"));
//$last_stock_take_data = last_stock_take_data("2013-12-21"); //Fixed date: use for testing
$date = date("Y-m-d", strtotime($last_stock_take_data['date']));
$received = stock_received_since($date);
//print_r($received); 
$sold = stock_sold_since($date);
//print_r($sold);
$last_stock_take_data = last_stock_take_data($date);
//print_r($last_stock_take_data['stock']);
?>

<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <div class="alert alert-error">Only update stock levels if all sales and incoming stock records are up to date.</div>
      <h1>Stock Levels</h1>
      <p>Last Stock Take Date: <?php echo date("jS F Y", strtotime($last_stock_take_data['date'])); ?></p>
      <div class="event-record-header">
        <!--Stock Level Table -->
        <form action="form_stock_take.php" method="post">
          <input type="hidden" name= "datepicker" id="datepicker" value="<?php echo date('m/d/Y'); ?>" />
          <?php
            echo theme_stock_level_table($received,$sold,$last_stock_take_data['stock']);
          ?>
          <p>Wine: Number of bottles sold are calculated from sales by the glass.</p>
          <h3>Only update stock levels if all sales and incoming stock records are up to date.</h3>
          <div class="row">
            <div class="span4">
              <label for="who">Stock Taker</label>
              <p>
                <input type="text" name= "who" id="who" />
              </p>
            </div>
          </div><!-- row-->
          <div class="row">
            <div class="span9">
              <label for="notes">Notes</label>
                <textarea class="form-control span5" rows="3" id="notes" name="notes"></textarea>
            </div>
          </div><!--row-->
          <input type="submit" value="Update Stock Levels">
        </form>
      </div>
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
//Thanks: http://stackoverflow.com/questions/9139075/confirm-message-before-delete
function ConfirmDelete()
{
  var x = confirm("Are you sure you want to delete?");
  if (x)
      return true;
  else
    return false;
}
</script>

<script>
  //When you enter a number in the 'Actual stocklevel' field this will 
  //calculate the difference
  function updateDifference(id) {
    var actual = document.getElementById("quantity" + id).value;
    var calculated = document.getElementById("stockActual" + id);
    var difference = document.getElementById("difference" + id);
    
    difference.value =  actual - Number(calculated.innerHTML);
    if (difference.value < 0) 
    {
      difference.style.backgroundColor = '#f2dede';
    }
    else if (difference.value == 0) 
    {
      difference.style.backgroundColor = 'transparent';
    }
    else 
    {
      difference.style.backgroundColor = '#d9edf7';
    }
  }
</script>

<?php 
    
function stock_received_since($date) {
  global $db;
  $sql = "SELECT id FROM incoming_stock_record WHERE date >= '$date';";
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  if ($result->num_rows !=0) {
    while($row = $result->fetch_assoc()){
      $stock_record_ids[] = $row['id'];
    }
    $stock_record_list = implode(",",$stock_record_ids);
    //echo $stock_record_list;
    $sql = "SELECT stock_id, SUM(number_received) AS totalReceived FROM stock_in WHERE incoming_stock_record_id IN ($stock_record_list) GROUP BY stock_id";
    if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
    }
    while($row = $result->fetch_assoc()){
      $received[$row['stock_id']] = $row['totalReceived'];
    }
    return $received;
  }
}

function stock_sold_since($date) {
  global $db;
  $sql = "SELECT id FROM event_record WHERE date >= '$date';";
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  //print_r($result);
  if ($result->num_rows !=0) {
    while($row = $result->fetch_assoc()){
      $sales_record_ids[] = $row['id'];
    }
    $sales_record_list = implode(",",$sales_record_ids);
    //echo $sales_record_list;
    $sql = "SELECT stock_id, SUM(number_sold) AS totalSold FROM sales_record WHERE event_record_id IN ($sales_record_list) GROUP BY stock_id";
    if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
    }
    while($row = $result->fetch_assoc()){
      $sold[$row['stock_id']] = $row['totalSold'];
    }
    return $sold;
  }
}

function last_stock_take_data($date) {
  //Get the most recent stock take record
  global $db;
  $sql = "SELECT id,date FROM stock_take_record WHERE date <= '$date' ORDER BY date DESC LIMIT 1;";
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  if ($result->num_rows !=0) {
    while($row = $result->fetch_assoc()){
      $stock_take_record_id = $row['id']; //Remember this is limited to one, should be the most recent
      $last_stock_take_data['date'] = $row['date'];
    }
    
    $sql = "SELECT stock_id, amount FROM stock_level WHERE stock_take_record_id = $stock_take_record_id";
    if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
    }
    while($row = $result->fetch_assoc()){
      $stock[$row['stock_id']] = $row['amount'];
    }
    $last_stock_take_data['stock'] = $stock;
    return $last_stock_take_data;
  }
}
?>
