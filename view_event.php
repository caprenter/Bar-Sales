<?php
include('settings.php');
include('functions/event_id_functions.php');
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
      <form action="view_event.php" method="get"> 
        <select name="eventID" onchange='this.form.submit()'>
          <option value="0">--Select--</option>
          <?php
          $sql = "SELECT  event_record.*, event_type.name FROM event_record  
                  JOIN event_type on event_record.event_type_id = event_type.id";
          //if (isset($event_id)) {
          //  $sql .= " WHERE event_record.id = $event_id"; 
          //}       
          $sql .= " ORDER BY event_record.id";
                  //ORDER BY date DESC";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()){
            print_r($row);
            echo '<option value="' . $row['id'] . '">' . $row['name'] . ': ' . date("d-m-Y",strtotime($row['date'])) . '</option>';
            if ($row['id'] == $event_id) {
              $event_name = $row['name'];
              $event_date = date("d-m-Y",strtotime($row['date']));
            }
          }
          ?>
        </select>
      <noscript><input class="select-event-id" type="submit"></noscript>
    </form>
    <div class="event-record-header">
    <?php 
      if (isset($event_id)) {
        $event_details = get_event_details($event_id); ?>
        <h1><?php echo $event_details['name']; ?></h1>
        <div class="date"><?php echo date("jS F Y", strtotime($event_details['date'])); ?></div>
        <div class="total"><?php echo '&pound;' . number_format(get_sales_total($event_id),2, '.', ''); ?></div>
      <?php } ?>
    </div>
    <!--Event Sales Record table -->
    <?php
      if (isset($event_id)) {
        
        $html = theme_sales_table($event_id);
        echo $html;
        get_event_details ($event_id);
      }
    ?>
  </div><!--end span10-->
			
    <!--Sidebar-->
    <div class="span2" style="float:left">
      
      <?php include("theme/sidebar.php");?>
      
    </div><!--end Sidebar-->
        
  </div><!--end Row-->




<?php
include('theme/footer.php');


?>
