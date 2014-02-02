<?php
include('settings.php');
include('functions/functions.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

//Delete a incoming stock  record if requested
//We need to delete the sales record and the event record as both are 
//related
if (isset($_POST) && !empty($_POST)) {
  if (isset($_POST["incoming-stock-record-id"]) && isset($_POST["delete"])) {
    $incoming_stock_record_id = filter_var($_POST["incoming-stock-record-id"], FILTER_SANITIZE_NUMBER_INT);
    $delete = filter_var($_POST["delete"], FILTER_SANITIZE_STRING);
    if (filter_var($incoming_stock_record_id, FILTER_VALIDATE_INT) && $delete == 'Delete') {
      if (delete_incoming_stock_record($incoming_stock_record_id)) {
        $msg = '
        <div class="alert alert-success">Incoming Stock Record ' . $incoming_stock_record_id . ' successfully deleted</div>';
        unset($incoming_stock_record_id);
      } else {
        $msg = '<div class="alert alert-danger">Incoming Stock Record NOT deleted. Try again.</div>';
        unset($incoming_stock_record_id);
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
  if (isset($_GET["incoming-stock-record-id"])) {
    $incoming_stock_record_id = filter_var($_GET['incoming-stock-record-id'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($incoming_stock_record_id, FILTER_VALIDATE_INT)) {
      unset($incoming_stock_record_id);
    }
    /*if (isset($event_type) && in_array($event_type,$event_type_ids)) {
      //check it is a valid value in the database.
      //The value should be one of the id's in the event_type table
    } else {
      unset($event_type);
    }*/
  } 
}
//echo $incoming_stock_record_id;





include('theme/header.php');
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Incoming Stock Record<?php if(!isset($incoming_stock_record_id)) { echo "s"; } ?></h1>
      <?php
        if (isset($msg)) {
          echo $msg;
        }
      ?>
      <form action="view_incoming_stock_records.php" method="get"> 
        <select name="incoming-stock-record-id" onchange='this.form.submit()'>
          <option value="0">--Select--</option>
          <?php
          $sql = "SELECT id,date,supplier FROM incoming_stock_record";
          $sql .= " ORDER BY date DESC";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()){
            print_r($row);
            echo '<option value="' . $row['id'] . '">' . date("jS F, Y",strtotime($row['date'])) . ": " . $row['supplier'] . '</option>';
            if ($row['id'] == $incoming_stock_record_id) {
              $supplier = $row['supplier'];
              $record_date = date("d-m-Y",strtotime($row['date']));
            }
          }
          ?>
        </select>
      <noscript><input class="select-incoming-incoming-stock-record-id" type="submit"></noscript>
    </form>
    <div class="event-record-header">
      <?php 
        if (!isset($incoming_stock_record_id)) {
          //Show list of available events grouped by type if no single event is selected
          echo theme_incoming_stock_list();
        }
      ?>
    <?php 
      if (isset($incoming_stock_record_id)) {
        $incoming_stock_record_details = get_incoming_stock_record_details($incoming_stock_record_id); ?>
        <div class="row">
          <div class="span4">
              <h1><?php echo  $incoming_stock_record_details['supplier']; ?></h1>
          </div>
          <!-- Edit Button-->
          <div class="span1" style="margin-top: 17px;">
            <form action="form_incoming_stock" method="post">
              <input type="hidden" name="incoming-stock-record-id" value="<?php echo $incoming_stock_record_id; ?>">
              <input class="event-id" type="submit" value="Edit">
           </form>
          </div>
          <!-- Delete Button-->
          <div class="span1" style="margin-top: 17px;">
            <form action="view_incoming_stock_records.php" method="post">
              <input type="hidden" name="incoming-stock-record-id" value="<?php echo $incoming_stock_record_id; ?>">
              <input name ="delete" class="event-id" type="submit" value="Delete" onclick="return ConfirmDelete();">
           </form>
          </div>
        </div>
        <div class="date"><?php echo date("l jS F Y", strtotime($incoming_stock_record_details['date'])); ?></div>
        <div class="notes"><?php echo $incoming_stock_record_details['notes']; ?></div>


      <?php } ?>
    </div>
    <!--Event Sales Record table -->
    <?php
      if (isset($incoming_stock_record_id)) {
        
        $html = theme_stock_table($incoming_stock_record_id);
        echo $html;
        //get_event_details ($incoming_stock_record_id);
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

