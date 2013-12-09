<?php
function get_event_details ($event_id) {
  global $db;
  $sql = "SELECT  event_record.*, event_type.name FROM event_record  
          JOIN event_type on event_record.event_type_id = event_type.id
          WHERE event_record.id = $event_id"; 

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
    $event_name = $row['name'];
    $event_date = $row['date'];
  }
  return array( "name" => $event_name,
                "date" => $event_date
                );
}
    
function theme_sales_table ($event_id) { 
  global $db;
    $html = '<table class="table table-striped">
              <thead>
                <th>Item</th>
                <th>Cost</th>
                <th>Sells at</th>
                <th>Number sold</th>
                <th>Total</th>
              </thead>
              <tbody>';

  $sql = "SELECT * FROM sales_record 
          JOIN stock on sales_record.stock_id = stock.id
          WHERE event_record_id = $event_id
          ORDER BY weight";

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
   // print_r($row);
   /* Array ( [event_record_id] => 22 [stock_id] => 1 [number_sold] => 4 [price] => 2.80 [id] => 1 [name] => Sam Smiths Organic Lager [cost_price] => 2.80 [retail_price] => 2.80 ) Array ( [event_record_id] => 22 [stock_id] => 2 [number_sold] => 6 [price] => 2.80 [id] => 2 [name] => Sam Smiths Organic Cider [cost_price] => 2.80 [retail_price] => 2.80 )*/
    $html .= '<tr>';
      $html .= '<td>' . $row['name'] . '</td>';
      $html .= '<td>' . $row['cost_price'] . '</td>';
      $html .= '<td>' . $row['retail_price'] . '</td>';
      $html .= '<td>' . $row['number_sold'] . '</td>';
      $html .= '<td>' . number_format($row['retail_price'] * $row['number_sold'],2, '.', '') . '</td>';
    $html .= '</tr>';
  }
      

  $html .= '</tbody>';
  $html .= '</table>';
  return $html;
  //echo '&pound;' . number_format($total,2, '.', '');
}

/* Shows all sales from the bar
 * 
 * If given an event type ID it will filter to that event
 * 
 * name: theme_total_sales_table
 * @param
 * @return
 * 
 */

function theme_total_sales_table ($event_type_id = FALSE) { 
  global $db;
    $html = '<table class="table table-striped">
              <thead>
                <th>Item</th>
                <th>Number sold</th>
                <th>Total</th>
              </thead>
              <tbody>';
  //Get every sales record which has the following tables:
  //event_record_id,	stock_id,	number_sold,	price
  $sql = "SELECT * FROM sales_record 
          JOIN stock on sales_record.stock_id = stock.id
          ORDER BY weight";
          
  if ($event_type_id !=NULL) {
    //We need to get all the event_record_ids for that event.
    //echo $event_type_id;
    $sql = "SELECT * 
            FROM sales_record 
            LEFT JOIN stock ON sales_record.stock_id = stock.id
            LEFT JOIN event_record ON sales_record.event_record_id = event_record.id
            WHERE event_record.event_type_id = $event_type_id
            ORDER BY weight";  
    //echo $sql;
  }
  //Then run through the records calculating total numbers sold and the 
  //total price NB sale prices may vary for the same stock item
  $totals = array();
  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()) {
    //Suppress error notices on these new array keys
    if (!isset($totals[$row['stock_id']]["total_sold"])) {
      $totals[$row['stock_id']]["total_sold"] ="";
    }
     if (!isset($totals[$row['stock_id']]["total_value"])) {
      $totals[$row['stock_id']]["total_value"] = "";
    }
    
    //Add the totals up
    $totals[$row['stock_id']]["total_sold"] += $row['number_sold'];
    $totals[$row['stock_id']]["total_value"] += $row['number_sold'] * $row['retail_price'] ;
    //Store the name of the stock item
    if (!isset($totals[$row['stock_id']]["name"])) {
      $totals[$row['stock_id']]["name"] = $row['name'];
    }
  }
  //print_r($totals);
  foreach ($totals as $total) {
  $html .= '<tr>';
      $html .= '<td>' . $total['name'] . '</td>';
      $html .= '<td>' . $total['total_sold'] . '</td>';
      $html .= '<td>' . number_format($total['total_value'],2, '.', '') . '</td>';
    $html .= '</tr>';
  }

    $html .= '</tbody>';
  $html .= '</table>';
  echo $html;
  //return $html;
  //echo '&pound;' . number_format($total,2, '.', '');
}

/*
 * 
 * name: get_sales_total
 * @param $event_id INT Id of an event in the database
 * @return $total Float Total value of sales for this event.
 * 
 */

function get_sales_total ($event_id) { 
  global $db;

  $sql = "SELECT * FROM sales_record 
          JOIN stock on sales_record.stock_id = stock.id
          WHERE event_record_id = $event_id";

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  $total = 0;
  while($row = $result->fetch_assoc()){
    $total = $total + $row['retail_price'] * $row['number_sold'];
  }
  return $total;
}

function get_event_type_name($event_type_id) {
  global $db;

  $sql = "SELECT name FROM event_type
          WHERE id = $event_type_id";

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
    $event_name = $row['name'];
  }
  return $event_name;
}

/* Delete a sales record of a given event_id
 * 
 * name: delete_sales_record();
 * @param $event_id INT Id of an event_record
 * @return
 * 
 */

function delete_sales_record($event_id) {
  global $db;
  //If an event_record has no sales it won't be removed this way
  $sql = "DELETE event_record, sales_record 
          FROM event_record JOIN sales_record 
          WHERE event_record.id=sales_record.event_record_id 
          AND event_record.id=$event_id;";

          
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  } else {
    if ($db->affected_rows > 0) { //event_record and sales_record were deleted
      return TRUE;
    } else { //Nothing was deleted - try to remove just the event_record
      $sql = "DELETE FROM event_record 
              WHERE event_record.id=$event_id;";
      if (!$result = $db->query($sql)) {
        die('There was an error running the query [' . $db->error . ']');
      } else {
        if ($db->affected_rows > 0) { //event_record was deleted 
          return TRUE;
        } else { //Nothing was deleted 
          return FALSE;
        }
      }
    }
  }
}
?>
