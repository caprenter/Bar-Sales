<?php
include('settings.php');
include('functions/event_id_functions.php');
//Process $_GET vars
if (isset($_GET)) {
  if (isset($_GET["event_type"])) {
    $event_type_id = filter_var($_GET['event_type'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($event_type_id, FILTER_VALIDATE_INT)) {
      unset($event_type_id);
    }
    /*if (isset($event_type) && in_array($event_type,$event_type_ids)) {
      //check it is a valid value in the database.
      //The value should be one of the id's in the event_type table
    } else {
      unset($event_type);
    }*/
  } 
}
//echo $event_type_id;
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
      <form action="" method="get"> 
        <select name="event_type" onchange='this.form.submit()'>
          <option value="0">--Select--</option>
          <option value="">All</option>;
          <?php
          $sql = "SELECT  * FROM event_type ORDER BY name";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()){
            print_r($row);
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
            if ($row['id'] == $event_type_id) {
              $event_type_name = $row['name'];
            }
          }
          ?>
        </select>
      <noscript><input class="select-event-id" type="submit"></noscript>
    </form>
    
    <div class="event-record-header">
      <?php 
        if(isset($event_type_id)) {
          echo  '<h1>' . get_event_type_name($event_type_id) . '</h1>';
          theme_total_sales_table($event_type_id);
        } else {
          echo  '<h1>All Sales</h1>';
          theme_total_sales_table();
        }
      ?>
    </div>
    <!--Sales Record table -->
  </div><!--end span10-->
			
    <!--Sidebar-->
    <div class="span2" style="float:left">
      
      <?php include("theme/sidebar.php");?>
      
    </div><!--end Sidebar-->
        
  </div><!--end Row-->
</div>
<?php
include('theme/footer.php');
?>
