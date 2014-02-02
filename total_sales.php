<?php
include('settings.php');
include('functions/functions.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

//Process $_GET vars
if (isset($_GET)) {
  if (isset($_GET["event_type"]) && $_GET["event_type"] !=NULL) {
    $event_type_id = filter_var($_GET['event_type'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($event_type_id, FILTER_VALIDATE_INT)) {
      unset($event_type_id);
    }
    $event_type_ids = get_event_type_ids();
    if (isset($event_type_id) && !in_array($event_type_id,$event_type_ids)) {
      unset($event_type_id);
    } 
  } 
}
//echo $event_type_id;


include('theme/header.php');
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Sales</h1>
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
            echo '<option value="' . $row['id'] . '">' . htmlentities($row['name']) . '</option>';
            if ($row['id'] == $event_type_id) {
              $event_type_name = htmlentities($row['name']);
            }
          }
          ?>
        </select>
      <noscript><input class="select-event-id" type="submit"></noscript>
    </form>
    
    <div class="event-record-header">
      <?php 
        //Show total sales for an event type. e.g. film
        if(isset($event_type_id)) {
          echo  '<h2>' . get_event_type_name($event_type_id) . '</h2>';
          echo '<div class="total">&pound;' . number_format(get_sales_total_event_type($event_type_id),2, '.', '') . '</div>';
          theme_total_sales_table($event_type_id);
        } else {
          //Show all sales
          echo  '<h2>All Sales</h2>';
          //echo get_sales_total_event_type(0);
          echo '<div class="total">&pound;' . number_format(get_sales_total_event_type(0),2, '.', '') . '</div>';
          theme_total_sales_table();
        }
      ?>
    </div>
    <div class="events-list">
      <h2>Calculated from the following events</h2>
       <?php
          if(isset($event_type_id)) {
            echo theme_events_list($event_type_id); 
          } else {
            echo theme_events_list();
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
