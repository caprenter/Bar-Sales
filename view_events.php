<?php
include('settings.php');
include('functions/functions.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

//Delete a sales/event record if requested
//We need to delete the sales record and the event record as both are 
//related
if (isset($_POST) && !empty($_POST)) {
  if (isset($_POST["event-id"]) && isset($_POST["delete"])) {
    $event_id = filter_var($_POST["event-id"], FILTER_SANITIZE_NUMBER_INT);
    $delete = filter_var($_POST["delete"], FILTER_SANITIZE_STRING);
    if (filter_var($event_id, FILTER_VALIDATE_INT) && $delete == 'Delete') {
      if (delete_sales_record($event_id)) {
        $msg = '
        <div class="alert alert-success">Event ' . $event_id . ' successfully deleted</div>';
        unset($event_id);
      } else {
        $msg = '<div class="alert alert-danger">Event NOT deleted. Try again.</div>';
        unset($event_id);
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
  if (isset($_GET["eventID"])) {
    $event_id = filter_var($_GET['eventID'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($event_id, FILTER_VALIDATE_INT)) {
      unset($event_id);
    }
    /*if (isset($event_type) && in_array($event_type,$event_type_ids)) {
      //check it is a valid value in the database.
      //The value should be one of the id's in the event_type table
    } else {
      unset($event_type);
    }*/
  } 
}
//echo $event_id;





include('theme/header.php');
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Event Record<?php if(!isset($event_id)) { echo "s"; } ?></h1>
      <?php
        if (isset($msg)) {
          echo $msg;
        }
      ?>
      <form action="view_events.php" method="get"> 
        <select name="eventID" onchange='this.form.submit()'>
          <option value="0">--Select--</option>
          <?php
          $sql = "SELECT event_record.*, event_type.name FROM event_record  
                  JOIN event_type on event_record.event_type_id = event_type.id";
          $sql .= " ORDER BY date DESC";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()){
            //print_r($row);
            echo '<option value="' . $row['id'] . '">' . date("jS F, Y",strtotime($row['date'])) . ': ' . htmlentities($row['name']) . '</option>';
            if ($row['id'] == $event_id) {
              $event_name = htmlentities($row['name']);
              $event_date = date("d-m-Y",strtotime($row['date']));
            }
          }
          ?>
        </select>
      <noscript><input class="select-event-id" type="submit"></noscript>
    </form>
    <div class="event-record-header">
    <?php 
      if (!isset($event_id)) {
        //Show list of available events grouped by type if no single event is selected
        echo theme_events_list();
      }
    ?>
      
      
    <?php 
      //If a single event is requested show the data on that event
      if (isset($event_id)) {
        $event_details = get_event_details($event_id); ?>
        <div class="row">
          <div class="span4">
              <h1><?php echo $event_details['name']; ?></h1>
          </div>
          <!-- Edit Button-->
          <div class="span1 pull-right" style="margin-top: 17px;">
            <form action="form_sales_entry.php" method="post">
              <input type="hidden" name="event-id" value="<?php echo $event_id; ?>">
              <input class="event-id edit" type="submit" value="Edit">
           </form>
          </div>
          <!-- Delete Button-->
          <div class="span1 pull-right" style="margin-top: 17px;">
            <form action="view_events.php" method="post">
              <input type="hidden" name="event-id" value="<?php echo $event_id; ?>">
              <input name ="delete" class="event-id delete" type="submit" value="Delete" onclick="return ConfirmDelete();">
           </form>
          </div>
        </div>
        <div class="date"><?php echo date("l jS F Y", strtotime($event_details['date'])); ?></div>
        <div class="total"><?php echo '&pound;' . number_format(get_sales_total($event_id),2, '.', ''); ?></div>
      <?php } ?>
    </div>
    <!--Event Sales Record table -->
    <?php
      if (isset($event_id)) {
        
        $html = theme_sales_table($event_id);
        echo $html;
        //get_event_details ($event_id);
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

