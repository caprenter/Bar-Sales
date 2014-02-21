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
  if (isset($_GET["month"]) && $_GET["month"] !=NULL) {
    $month = filter_var($_GET['month'],FILTER_SANITIZE_STRING);
    /*if (!filter_var($month, FILTER_VALIDATE_INT)) {
      unset($month);
    }
    * * */
    //$event_ids = get_event_ids();
    //if (isset($month) && !in_array($month,$months)) {
    //  unset($month);
    //} 
    
  } 
}
//echo $month;


include('theme/header.php');
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Monthly Sales</h1>
      <form action="" method="get"> 
        <select name="month" onchange='this.form.submit()'>
          <option value="0">--Select--</option>
          <option value="">All</option>;
          <?php
          $months = array();
          $event_ids = array();
          $sql = "SELECT id, date FROM event_record ORDER BY date";

          if(!$result = $db->query($sql)){
              die('There was an error running the query [' . $db->error . ']');
          }

          while($row = $result->fetch_assoc()){
            //print_r($row);
            $month_slug = date("M Y",strtotime($row['date']));
            if (!isset($months[$month_slug])) {
              $months[$month_slug] = date("Y-m-01",strtotime($row['date']));
            }
            $event_ids[] = $row['id'];
          }
          print_r($months);
          $months = array_unique($months);
          print_r($months);
          foreach ($months as $key=>$value) {
            echo '<option value="' . $value . '">' . htmlentities($key) . '</option>';
          }
          ?>
        </select>
      <noscript><input class="select-event-id" type="submit"></noscript>
    </form>
    
    <div class="event-record-header">
      <?php 
        //Show total sales for an event type. e.g. film
        if(isset($month)) {
          //echo $month; die;
          //print_r($event_ids);
          echo  '<h2>' . date("M Y",strtotime($month)) . '</h2>';
          echo '<div class="total">&pound;' . number_format(get_sales_total_by_month($month),2, '.', '') . '</div>';
          theme_total_sales_table(NULL,$month);
        } else {
          //Show all sales
          echo  '<h2>All Sales</h2>';
          //echo get_sales_total_by_month(0);
          echo '<div class="total">&pound;' . number_format(get_sales_total_event_type(0),2, '.', '') . '</div>';
          theme_total_sales_table();
        }
      ?>
    </div>
    <div class="events-list">
      <h2>Calculated from the following events</h2>
       <?php
          if(isset($month)) {
            echo theme_events_list(NULL,$month); 
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
  <div>
    <div><?php include('charts/chart.php'); ?></div>
  </div>
</div>
<?php
include('theme/footer.php');
?>
