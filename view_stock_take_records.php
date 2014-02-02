<?php
/*
 * If no stock_take_record_id is available
 * then this page returns a selection list
 * so we can select a record to examine
 * 
 * If a stock_take_record_id is sent via a $_GET variable then we show 
 * an individual record.
 * 
 * We use this code to also delete records and return us to this page
 * with the drop down list.
 * 
 * If we want to edit a record it sends us to the original
 * form_stock_take.php, where we create or update stock take records.
 * 
*/

include('settings.php');
/*we need:
 * get_stock_take_record_details
 * theme_stock_take_table
 * delete_stock_take_record
 * from...
*/
include('functions/functions.php'); //

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

//Delete a sales/event record if requested
//We need to delete the sales record and the event record as both are 
//related
if (isset($_POST) && !empty($_POST)) {
  if (isset($_POST["stock-take-record-id"]) && isset($_POST["delete"])) {
    $stock_take_record_id = filter_var($_POST["stock-take-record-id"], FILTER_SANITIZE_NUMBER_INT);
    $delete = filter_var($_POST["delete"], FILTER_SANITIZE_STRING);
    if (filter_var($stock_take_record_id, FILTER_VALIDATE_INT) && $delete == 'Delete') {
      if (delete_stock_take_record($stock_take_record_id)) {
        $msg = '
        <div class="alert alert-success">Stock Record ' . $stock_take_record_id . ' successfully deleted</div>';
        unset($stock_take_record_id);
      } else {
        $msg = '<div class="alert alert-danger">Stock Record NOT deleted. Try again.</div>';
        unset($stock_take_record_id);
      }

    }
    //print_r($fetch_data);
  } else {
    //Redirect to home
    header('Location: ' . $domain);
  }
} 



//Process $_GET vars
if (isset($_GET)) {
  if (isset($_GET["stock-take-record-id"])) {
    $stock_take_record_id = filter_var($_GET['stock-take-record-id'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($stock_take_record_id, FILTER_VALIDATE_INT)) {
      unset($stock_take_record_id);
    }
    /*if (isset($event_type) && in_array($event_type,$event_type_ids)) {
      //check it is a valid value in the database.
      //The value should be one of the id's in the event_type table
    } else {
      unset($event_type);
    }*/
  } 
}
//echo $stock_take_record_id;





include('theme/header.php');
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Stock Take Record<?php if(!isset($stock_take_record_id)) { echo "s"; } ?></h1>
      <?php
        if (isset($msg)) {
          echo $msg;
        }
      ?>
      <form action="view_stock_take_records.php" method="get"> 
        <select name="stock-take-record-id" onchange='this.form.submit()'>
          <option value="0">--Select--</option>
          <?php
          $sql = "SELECT id,date,who FROM stock_take_record";
          $sql .= " ORDER BY date DESC";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()){
            print_r($row);
            echo '<option value="' . $row['id'] . '">' . date("jS F, Y",strtotime($row['date'])) . ": " . $row['who'] . '</option>';
            if ($row['id'] == $stock_take_record_id) {
              $supplier = $row['who'];
              $record_date = date("d-m-Y",strtotime($row['date']));
            }
          }
          ?>
        </select>
      <noscript><input class="select-incoming-stock-take-record-id" type="submit"></noscript>
    </form>
    <div class="event-record-header">
      <?php 
        if (!isset($incoming_stock_record_id)) {
          //Show list of available events grouped by type if no single event is selected
          echo theme_stock_take_list();
        }
      ?>
    <?php 
      if (isset($stock_take_record_id)) {
        $stock_take_record_details = get_stock_take_record_details($stock_take_record_id); ?>
        <div class="row">
          <div class="span4">
              <h1><?php echo  $stock_take_record_details['who']; ?></h1>
          </div>
          <!-- Edit Button-->
          <div class="span1" style="margin-top: 17px;">
            <form action="form_stock_take.php" method="post">
              <input type="hidden" name="stock-take-record-id" value="<?php echo $stock_take_record_id; ?>">
              <input class="event-id" type="submit" value="Edit">
           </form>
          </div>
          <!-- Delete Button-->
          <div class="span1" style="margin-top: 17px;">
            <form action="view_stock_take_records.php" method="post">
              <input type="hidden" name="stock-take-record-id" value="<?php echo $stock_take_record_id; ?>">
              <input name ="delete" class="event-id" type="submit" value="Delete" onclick="return ConfirmDelete();">
           </form>
          </div>
        </div>
        <div class="date"><?php echo date("l jS F Y", strtotime($stock_take_record_details['date'])); ?></div>
        <div class="notes"><?php echo $stock_take_record_details['notes']; ?></div>


      <?php } ?>
    </div>
    <!--Event Sales Record table -->
    <?php
      if (isset($stock_take_record_id)) {
        
        $html = theme_stock_take_table($stock_take_record_id);
        echo $html;
        //get_event_details ($stock_take_record_id);
      } 
    ?>
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

