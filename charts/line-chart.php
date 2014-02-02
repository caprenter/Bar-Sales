<?php
include('../settings.php');
include('../functions/functions.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

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

if (!isset($event_type_id)) {
  $event_type_id = FALSE; 
}
$data_array = get_chart_data($event_type_id);

function get_chart_data($event_type_id = FALSE) { 
  global $db;
  global $white_wine_ids;
  global $red_wine_ids;
  global $red_wine_bottle_id;
  global $white_wine_bottle_id;
  $red_ml = 0; //Red Wine mililitres sold
  $white_ml = 0; //White Wine mililitres sold
  $stock = array();
          
  $sql = "SELECT * FROM stock ORDER BY weight";
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
   while($row = $result->fetch_assoc()){
    //Get id's and names of stock
    $stock[$row['id']] = $row['name'];
  }
  
  //First line of data array need by google charts. 
  //Like:['Event','Sam Smiths Indian Pale Ale','Sam Smiths Organic Lager','Sam Smiths Organic Cider','SM White Wine','MD White Wine','LG White Wine','White Wine (Bottle)','Sam Smiths Organic Pale Ale','SM Red Wine','MD Red Wine','LG Red Wine','Red Wine (Bottle)','Sam Smiths Taddy Porter','Saltaire Blonde','Juice (small carton)','Juice (large carton, sold by the glass)','Fentemans','Cans (e.g. Coke)'],
  $data_array = "['Event',";
  foreach ($stock as $id => $name) {
    $data_array .= "'" . $name . "',";
  }
  $data_array = rtrim($data_array,',');
  $data_array .= "],";
  //echo $data_array; die;

  //Get every sales record which has the following tables:
  //event_record_id,	stock_id,	number_sold,	price
  $sql = "SELECT * 
            FROM sales_record 
            LEFT JOIN stock ON sales_record.stock_id = stock.id
            LEFT JOIN event_record ON sales_record.event_record_id = event_record.id";
  if ($event_type_id !=NULL) {          
    $sql .= " WHERE event_record.event_type_id = $event_type_id";
  }
  $sql .= " ORDER BY date ASC, weight";  
  
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  
  while($row = $result->fetch_assoc()) {
    //print_r($row) . '<br/>';
    $events[$row['event_record_id']] = $row['date'];
    //Suppress error notices on these new array keys
    if (!isset($totals[$row['event_record_id']][$row['stock_id']]["total_sold"])) {
      $totals[$row['event_record_id']][$row['stock_id']]["total_sold"] = 0;
    }
    
    //Add the totals up
    $totals[$row['event_record_id']][$row['stock_id']]["total_sold"] += $row['number_sold'];
  }
  
  foreach ($totals as $event_id => $total) {
    if (count($totals) == 1) { //only one event, so make a zero node
      $data_array .= PHP_EOL . "['00-00-0000',"; //event date
      foreach ($stock as $stock_id => $name) {
        $data_array .=  0 . ",";
      }
      $data_array = rtrim($data_array,',');
      $data_array .= "],";
    }
    //Make a row for the data_array with the sales data for the events
    //$data_array .= "['" . $row['date'] . "',";
    $data_array .= PHP_EOL . "['" . $events[$event_id] . "',"; //event date
    foreach ($stock as $stock_id => $name) {
      if (isset($total[$stock_id])) {
        $data_array .=  $total[$stock_id]["total_sold"] . ",";
      } else {
        $data_array .=  0 . ",";
      }
    }
    $data_array = rtrim($data_array,',');
    $data_array .= "],";
  }
  $data_array = rtrim($data_array,',');
  //echo $data_array; die;
  return $data_array;

  //IMPORTANT
  mysqli_data_seek($result, 0); //allows us to re-use the buffered $result
    
}
?>
  
  <html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          //['Year', 'Sales', 'Expenses'],
          //['2004',  1000,      400],
          //['2005',  1170,      460],
          //['2006',  660,       1120],
          //['2007',  1030,      540]
          <?php echo $data_array; ?> 
        ]);

        var options = {
          title: "Company Performance",
          chartArea: {  width: "50%", height: "90%" }
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
    <?php echo $data_array; ?> 
  </body>
</html>
