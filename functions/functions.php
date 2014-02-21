<?php

/****** get_ functions fetch data *****/
/*
 * 
 * EVENTS
 * 
 * 
*/
function get_event_details ($event_id) {
  global $db;
  $sql = "SELECT  event_record.*, event_type.name FROM event_record  
          JOIN event_type on event_record.event_type_id = event_type.id
          WHERE event_record.id = $event_id"; 

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
    $event_name = htmlentities($row['name']);
    $event_date = $row['date'];
  }
  return array( "name" => $event_name,
                "date" => $event_date
                );
}

/*
 * 
 * INCOMING STOCK
 * 
 * 
*/
function get_incoming_stock_record_details($stock_record_id) {
    global $db;
  $sql = "SELECT * FROM incoming_stock_record
          WHERE id = $stock_record_id"; 

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
    $supplier = $row['supplier'];
    $date = $row['date'];
    $notes = $row['notes'];
  }
  return array( "supplier" => $supplier,
                "date" => $date,
                "notes" => $notes
                );
}
/*
 * 
 * STOCK TAKE
 * 
 * 
 */
function get_stock_take_record_details($stock_record_id) {
    global $db;
  $sql = "SELECT * FROM stock_take_record
          WHERE id = $stock_record_id"; 

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
    $who = $row['who'];
    $date = $row['date'];
    $notes = $row['notes'];
  }
  return array( "who" => $who,
                "date" => $date,
                "notes" => $notes
                );
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
          WHERE event_record_id = $event_id";

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  $total = 0;
  while($row = $result->fetch_assoc()){
    //$total = $total + $row['retail_price'] * $row['number_sold']; //Uses current retail prices that will change
    $total = $total + $row['price'] * $row['number_sold']; //Uses price at time of sale.
  }
  return $total;
}

/*
 * 
 * name: get_sales_total_event_type
 * @param $event_type INT Id of an event type in the database
 * @return $total Float Total value of sales for this event type.
 * 
 */

function get_sales_total_event_type ($event_type,$from = FALSE, $to = FALSE) { 
  global $db;
  //Get event_record_ids for this event type
  $sql = "SELECT id FROM event_record
          WHERE event_type_id = $event_type";
          //AND (date BETWEEN $from AND $to)
  if ($event_type == 0) { //Then we want all events, so select all event records
    $sql = "SELECT id FROM event_record";
  }
  //echo $sql;
  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  $total = 0;
  while($row = $result->fetch_assoc()){
    //$total = $total + $row['retail_price'] * $row['number_sold']; //Uses current retail prices that will change
    $total = $total + get_sales_total($row['id']); //Uses price at time of sale.
  }
  return $total;
}

/*
 * 
 * name: get_sales_total_event_type
 * @param $event_type INT Id of an event type in the database
 * @return $total Float Total value of sales for this event type.
 * 
 */

function get_sales_total_by_month ($from, $to = FALSE) { 
  global $db;
  $to = new DateTime( $from );
  $to = $to->format( 'Y-m-t' );
  //Get event_record_ids for this event type
  $sql = "SELECT id FROM event_record
          WHERE (date BETWEEN '$from' AND '$to')";
  //if ($event_type == 0) { //Then we want all events, so select all event records
  //  $sql = "SELECT id FROM event_record";
  //}
  //echo $sql;
  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  $total = 0;
  while($row = $result->fetch_assoc()){
    //$total = $total + $row['retail_price'] * $row['number_sold']; //Uses current retail prices that will change
    $total = $total + get_sales_total($row['id']); //Uses price at time of sale.
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
    $event_name = htmlentities($row['name']);
  }
  return $event_name;
}

/*Return an array of event type ids for sanitation*/
function get_event_type_ids() {
  global $db;
  $event_ids = array();
  $sql = "SELECT id FROM event_type";

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
    $event_ids[] = $row['id'];
  }
  return $event_ids;
}

/****** delete_ functions erm delete stuff *****/
/* Delete an incoming stock record 
 * 
 * name: delete_sales_record();
 * @param $event_id INT Id of an event_record
 * @return
 * 
 */

function delete_incoming_stock_record($incoming_stock_record_id) {
  global $db;
  //If an event_record has no sales it won't be removed this way
  $sql = "DELETE incoming_stock_record, stock_in
          FROM incoming_stock_record JOIN stock_in 
          WHERE incoming_stock_record.id=stock_in.incoming_stock_record_id
          AND incoming_stock_record.id=$incoming_stock_record_id;";

          
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  } else {
    if ($db->affected_rows > 0) { //event_record and sales_record were deleted
      return TRUE;
    } else { //Nothing was deleted - try to remove just the event_record
      $sql = "DELETE FROM incoming_stock_record 
              WHERE id=$incoming_stock_record_id;";
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

/* Delete a stock take record of a given stock_take_record_id
 * 
 * name: delete_stock_take_record();
 * @param $stock_take_record_id INT Id of an event_record
 * @return
 * 
 */

function delete_stock_take_record($stock_take_record_id) {
  global $db;
  //If a stock_take_record has no sales it won't be removed this way
  $sql = "DELETE stock_take_record, stock_level 
          FROM stock_take_record JOIN stock_level 
          WHERE stock_take_record.id=stock_level.stock_take_record_id 
          AND stock_take_record.id=$stock_take_record_id;";

          
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  } else {
    if ($db->affected_rows > 0) { //event_record and sales_record were deleted
      return TRUE;
    } else { //Nothing was deleted - try to remove just the event_record
      $sql = "DELETE FROM stock_take_record 
              WHERE stock_take_record.id=$stock_take_record_id;";
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

/****** theme_ functions theme output and return html *****/

function theme_stock_table ($incoming_stock_record_id) { 
  global $db;
    $html = '<table class="table table-striped">
              <thead>
                <th>Item</th>
                <th>Cost (now)</th>
                <th>Cost (then)</th>
                <th>Sells (now)</th>
                <th>Number Received</th>
              </thead>
              <tbody>';

  $sql = "SELECT * FROM stock_in 
          JOIN stock on stock_in.stock_id = stock.id
          WHERE incoming_stock_record_id = $incoming_stock_record_id
          ORDER BY weight";

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
   // print_r($row);
   /* Array ( [event_record_id] => 22 [stock_id] => 1 [number_sold] => 4 [price] => 2.80 [id] => 1 [name] => Sam Smiths Organic Lager [cost_price] => 2.80 [retail_price] => 2.80 ) Array ( [event_record_id] => 22 [stock_id] => 2 [number_sold] => 6 [price] => 2.80 [id] => 2 [name] => Sam Smiths Organic Cider [cost_price] => 2.80 [retail_price] => 2.80 )*/
    $html .= '<tr>';
      $html .= '<td>' . htmlentities($row['name']) . '</td>';
      $html .= '<td>' . $row['cost_price'] . '</td>';
      $html .= '<td>' . $row['bought_at'] . '</td>';
      
      $html .= '<td>' . $row['retail_price'] . '</td>'; //Current Retail price
      $html .= '<td>' . $row['number_received'] . '</td>';
      //$html .= '<td>' . number_format($row['retail_price'] * $row['number_sold'],2, '.', '') . '</td>';
    $html .= '</tr>';
  }
      

  $html .= '</tbody>';
  $html .= '</table>';
  return $html;
  //echo '&pound;' . number_format($total,2, '.', '');
}


function theme_stock_take_table ($stock_take_record_id) { 
  global $db;
  global $white_wine_ids;
  global $red_wine_ids;
  
    $html = '<table class="table table-striped">
              <thead>
                <th>Item</th>
                <th>Cost then</th>
                <th>Cost now</th>
                <th>Sells then</th>
                <th>Sells now</th>
                <th>Number Counted</th>
                <th>Shrinkage</th>
                <th>Shrinkage * Cost then</th>
              </thead>
              <tbody>';

  $sql = "SELECT *, stock_level.cost_price AS sl_cost, stock_level.retail_price AS sl_retail FROM stock_level
          JOIN stock on stock_level.stock_id = stock.id
          WHERE stock_take_record_id = $stock_take_record_id
          ORDER BY weight";
          
          //echo $sql;

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
    //print_r($row);
   /* Array ( [stock_take_record_id] => 22 [stock_id] => 1 [amount] => 7 [shrinkage] => 156 [cost_price] => 2.80 [retail_price] => 2.80 [id] => 1 [name] => Sam Smiths Organic Lager [weight] => 10 [sl_cost] => 2.80 [sl_retail] => 2.80 )*/
   
    //Don't allow people to input amounts for glasses of wine
    //on stock take. They should only account for bottles.
    if (in_array($row['id'],$white_wine_ids)) {
      continue;
    }
    if (in_array($row['id'],$red_wine_ids)) {
      continue;
    }
    $html .= '<tr>';
      $html .= '<td>' . htmlentities($row['name']) . '</td>';
      $html .= '<td>' . $row['sl_cost'] . '</td>';        //Stock cost at time of audit
      $html .= '<td>' . $row['cost_price'] . '</td>';     //Stock cost now
      $html .= '<td>' . $row['sl_retail'] . '</td>';      //Stock retail price at time of audit
       $html .= '<td>' . $row['retail_price'] . '</td>';  //Stock retail price now
      $html .= '<td>' . $row['amount'] . '</td>';
      $html .= '<td>' . $row['shrinkage'] . '</td>';
      $html .= '<td>' . $row['shrinkage'] * $row['sl_cost'] . '</td>';
      //$html .= '<td>' . number_format($row['retail_price'] * $row['number_sold'],2, '.', '') . '</td>';
    $html .= '</tr>';
  }
      

  $html .= '</tbody>';
  $html .= '</table>';
  return $html;
  //echo '&pound;' . number_format($total,2, '.', '');
}



/*
 * 
 * 
 * STOCK LEVEL
 * 
 * 
*/
function theme_stock_level_table ($received,$sold,$stock_take_data) { 
  global $db;
  global $white_wine_ids;
  global $red_wine_ids;
  global $red_wine_bottle_id;
  global $white_wine_bottle_id;
  $red_ml = 0; //Red Wine mililitres sold
  $white_ml = 0; //White Wine mililitres sold
  $wine = array_merge(array_values($white_wine_ids),array_values($red_wine_ids));
    $html = '<table class="table table-striped">
              <thead>
                <th>Item</th>
               <!--<th>Cost</th>
                <th>Sells at</th>-->
                <th>@Stock Take</th>
                <th>No. In</th>
                <th>No. Out</th>
                <th>Change</th>
                <th>Stock (calc)</th>
                <th>Stock (actual)</th>
                <th>Difference</th>
              </thead>
              <tbody>';

  $sql = "SELECT * FROM stock
          ORDER BY weight";

  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  while($row = $result->fetch_assoc()){
   // print_r($row);
    if (isset($received[$row['id']])) {
        $received_amount = $received[$row['id']];
    } else {
      $received_amount = 0;
    }
    if (isset($sold[$row['id']])) {
      $sold_amount = $sold[$row['id']];
    } else {
      $sold_amount = 0;
    }
    if (isset($stock_take_data[$row['id']])) {
      $stock_amount = $stock_take_data[$row['id']];
    } else {
      $stock_amount = 0;
    }
   /* Array ( [event_record_id] => 22 [stock_id] => 1 [number_sold] => 4 [price] => 2.80 [id] => 1 [name] => Sam Smiths Organic Lager [cost_price] => 2.80 [retail_price] => 2.80 ) Array ( [event_record_id] => 22 [stock_id] => 2 [number_sold] => 6 [price] => 2.80 [id] => 2 [name] => Sam Smiths Organic Cider [cost_price] => 2.80 [retail_price] => 2.80 )*/
   
   //Wine
   //If we are on  a wine ID then save it and work it out..
   if (in_array($row['id'],$white_wine_ids)) {
     //sold_amount will be a number of glasses
     //received_amount will be meaningless - we need to know  how many bottles we've bought
     //ids here are 17=SM, 18=MD, 16=LG
     $white_ml = sum_millilitres_wine($row['id'],$white_ml,$white_wine_ids,$sold_amount);
   }
   //Once we hit the White Wine results we need to turn the glasses sold
   //into a number of bottles opened, and add that to the number of bottles sold.
   if ($row['id'] == $white_wine_bottle_id) { //This is the ID for white wine bottles
      $white_bottles = calculate_number_of_bottles ($white_ml);
      $sold_amount += $white_bottles; //add the glasses sold to the bottles sold amount
    }
      
   
   
   if (in_array($row['id'],$red_wine_ids)) {
     //sold_amount will be a number of glasses
     //received_amount will be meaningless - we need to know  how many bottles we've bought
     //19=SM, 20=MD, 21=LG
     $red_ml = sum_millilitres_wine($row['id'],$red_ml,$red_wine_ids,$sold_amount);

    }
    if ($row['id'] == $red_wine_bottle_id) { //This is the ID for red wine bottles
      $red_bottles = calculate_number_of_bottles ($red_ml);
      $sold_amount += $red_bottles; //add the glasses sold to the bottles sold amount
    }
    //echo $white_ml;
    //echo $red_ml;
    $html .= '<tr>';
      $html .= '<td>' . htmlentities($row['name']) . '</td>';
      //$html .= '<td>' . $row['cost_price'] . '</td>';
      //$html .= '<td>' . $row['retail_price'] . '</td>';
      if (in_array($row['id'],$wine)) {
        $html .= '<td> </td><td> </td>';
      } else {
        $html .= '<td>' . $stock_amount . '</td>';        
        $html .= '<td>' . $received_amount . '</td>';
      }
      $html .= '<td>' . $sold_amount . '</td>';
      if (in_array($row['id'],$wine)) {
        $html .= '<td> </td><td> </td><td> </td><td> </td>';
      } else {
        $html .= '<td>' . ($received_amount - $sold_amount) . '</td>';
        $html .= '<td id="stockActual' . $row['id'] . '">' . ($stock_amount + $received_amount - $sold_amount) . '</td>';
        $html .= '<td><input class="actual" type="number" id="quantity' . $row['id'] . '" name="quantity' . $row['id'] . '" min="0" step="1" onchange="updateDifference(' . $row['id'] . ')"/></td>';
        $html .= '<td><input class="difference" id="difference' . $row['id'] . '" name="difference' . $row['id'] . '" readonly="readonly" value=""/></td>';
      }
    $html .= '</tr>';
  }
      

  $html .= '</tbody>';
  $html .= '</table>';
  return $html;
  //echo '&pound;' . number_format($total,2, '.', '');
}


    
function theme_sales_table ($event_id) { 
  global $db;
    $html = '<table class="table table-striped">
              <thead>
                <th>Item</th>
                <th>Cost (now)</th>
                <th>Sells (now)</th>
                <th>Cost (then)</th>
                <th>Sold (then)</th>
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
      $html .= '<td>' . htmlentities($row['name']) . '</td>';
      $html .= '<td>' . $row['cost_price'] . '</td>'; //Cost price today
      $html .= '<td>' . $row['retail_price'] . '</td>'; //Retail today
      $html .= '<td>' . $row['cost'] . '</td>'; //Cost price at time of event
      $html .= '<td>' . $row['price'] . '</td>'; //Retail price at the time of the event
      $html .= '<td>' . $row['number_sold'] . '</td>';
      $html .= '<td>' . number_format($row['price'] * $row['number_sold'],2, '.', '') . '</td>';
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

function theme_total_sales_table ($event_type_id = FALSE, $from = FALSE) { 
  global $db;
  global $white_wine_ids;
  global $red_wine_ids;
  global $red_wine_bottle_id;
  global $white_wine_bottle_id;
  $red_ml = 0; //Red Wine mililitres sold
  $white_ml = 0; //White Wine mililitres sold
    $html = '<table class="table table-striped">
              <thead>
                <th>Item</th>
                <th>Number sold</th>';
    if ($event_type_id !=NULL) { 
      $html .= '<th>Event Average</th>';
    }
    $html .= '<th>% of Sales</th>
              <th>Total</th>
              </thead>
              <tbody>';
  //Get every sales record which has the following tables:
  //event_record_id,	stock_id,	number_sold,	price
  $sql = "SELECT * FROM sales_record 
          JOIN stock on sales_record.stock_id = stock.id
          ORDER BY weight";
  if ($from != NULL) {
    $to = new DateTime( $from );
    $to = $to->format( 'Y-m-t' );
    //echo $to; die;
    $sql = "SELECT * 
            FROM sales_record 
            JOIN stock on sales_record.stock_id = stock.id
            LEFT JOIN event_record ON sales_record.event_record_id = event_record.id
            WHERE (event_record.date BETWEEN '$from' AND '$to')
            ORDER BY weight";
  }
  if ($event_type_id !=NULL) {
    //We need to get all the event_record_ids for that event.
    //echo $event_type_id;
    $sql = "SELECT * 
            FROM sales_record 
            LEFT JOIN stock ON sales_record.stock_id = stock.id
            LEFT JOIN event_record ON sales_record.event_record_id = event_record.id
            WHERE event_record.event_type_id = $event_type_id
            ORDER BY weight ";  
    //echo $sql;
  }
  //Then run through the records calculating total numbers sold and the 
  //total price NB sale prices may vary for the same stock item
  $totals = array();
  $all_sales = 0;
  if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
  }
  //printf("Number of rows: %d.\n", $result->num_rows);
  //Count the number of events this data is from
  while ($row = $result->fetch_assoc()) {
    //print_r($row);die;
    $event_ids[] = $row['event_record_id'];
  }
  if (isset($event_ids)) {
    if($event_ids != NULL) {
      $event_ids = array_unique($event_ids);
      //print_r($event_ids);
      echo count($event_ids) . ' event';
      if (count($event_ids) != 1) { echo 's'; }
    } else {
      unset($event_ids);
    }
  }
  //print_r($event_ids);
  //IMPORTANT
  mysqli_data_seek($result, 0); //allows us to re-use the buffered $result
  
  while($row = $result->fetch_assoc()) {
    //Suppress error notices on these new array keys
    if (!isset($totals[$row['stock_id']]["total_sold"])) {
      $totals[$row['stock_id']]["total_sold"] ="";
    }
    if (!isset($totals[$row['stock_id']]["total_value"])) {
      $totals[$row['stock_id']]["total_value"] = "";
    }
    
    //Add the totals up
    $all_sales += $row['number_sold'];
    $totals[$row['stock_id']]["total_sold"] += $row['number_sold'];
    $totals[$row['stock_id']]["total_value"] += $row['number_sold'] * $row['price'] ; //This is price recorded when sold
    //Store the name of the stock item
    if (!isset($totals[$row['stock_id']]["name"])) {
      $totals[$row['stock_id']]["name"] = htmlentities($row['name']);
    }
  }
  //echo $all_sales;
  //print_r($totals);
  
  //Wine
  //Work out how many bottles of wine the number of glasses sold equates to.
  foreach ($totals as $stock_id => $value) {
    if (in_array($stock_id,$white_wine_ids)) {
       //sold_amount will be a number of glasses
       //received_amount will be meaningless - we need to know  how many bottles we've bought
       //ids here are 17=SM, 18=MD, 16=LG
       //echo $stock_id;
       $white_ml = sum_millilitres_wine($stock_id,$white_ml,$white_wine_ids,$value['total_sold']);
    }
    if (in_array($stock_id,$red_wine_ids)) {
       //sold_amount will be a number of glasses
       //received_amount will be meaningless - we need to know  how many bottles we've bought
       //ids here are 19,20,21
       $red_ml = sum_millilitres_wine($stock_id,$red_ml,$red_wine_ids,$value['total_sold']);
       //echo $stock_id;
       //echo $red_ml.",";
    }
  }
  $white_bottles_by_glass = calculate_number_of_bottles ($white_ml);
  $red_bottles_by_glass = calculate_number_of_bottles ($red_ml);
  if (isset($total[$white_wine_bottle_id])) { //then we have also sold some bottles
    $all_white = $white_bottles_by_glass + $total[$white_wine_bottle_id]['total_sold'];
  } else {
    $all_white = $white_bottles_by_glass;
  }
  if (isset($total[$red_wine_bottle_id])) { //then we have also sold some bottles
    $all_red = $red_bottles_by_glass + $total[$red_wine_bottle_id]['total_sold'];
  } else {
    $all_red = $red_bottles_by_glass;
  }
    
  
  foreach ($totals as $total) {
  $html .= '<tr>';
      $html .= '<td>' . $total['name'] . '</td>';
      $html .= '<td>' . $total['total_sold'] . '</td>';
      if ($event_type_id !=NULL) {
        $html .= '<td>' . round($total['total_sold']/count($event_ids)) . '</td>';
      }
      $html .= '<td>' . round(100 * $total['total_sold'] / $all_sales) . '</td>';
      $html .= '<td>' . number_format($total['total_value'],2, '.', '') . '</td>';
    $html .= '</tr>';
    
  }
  if (isset($all_white)) { //then we have also sold some bottles
    $html .= '<tr class="wine white">';
      $html .= '<td>All White Wine</td>';
      $html .= '<td>' . $all_white . '</td>';
      if ($event_type_id !=NULL && isset($event_ids)) {
        $html .= '<td>' . round($all_white/count($event_ids)) . '</td>';
      }
      $html .= '<td> </td>';
      $html .= '<td> </td>';
    $html .= '</tr>';
  }
  if (isset($all_red)) { //then we have also sold some bottles
     $html .= '<tr class="wine red">';
      $html .= '<td>All Red Wine</td>';
      $html .= '<td>' . $all_red . '</td>';
      if ($event_type_id !=NULL && isset($event_ids)) {
        $html .= '<td>' . round($all_red/count($event_ids)) . '</td>';
      }
      $html .= '<td> </td>';
      $html .= '<td> </td>';
    $html .= '</tr>';
  }
    $html .= '</tbody>';
  $html .= '</table>';
  echo $html;
  //echo "Red Wine: " . $red_bottles_by_glass;
  //echo '<br/>';
  //echo "White Wine: " . $white_bottles_by_glass;
  //return $html;
  //echo '&pound;' . number_format($total,2, '.', '');
}



function theme_events_list($id=FALSE, $from = FALSE) {
  global $db;
  global $domain;
  $sql = "SELECT  event_record.*, event_type.name FROM event_record  
                  JOIN event_type on event_record.event_type_id = event_type.id";
          if ($id != FALSE) {
            $sql .= " WHERE event_type.id = $id"; 
            $sql .= " ORDER BY date DESC";
          } elseif ($from !=FALSE) {
            $to = new DateTime( $from );
            $to = $to->format( 'Y-m-t' );
            $sql .= " WHERE (event_record.date BETWEEN '$from' AND '$to')"; 
            $sql .= " ORDER BY date DESC";
          } else {
          //$sql .= " GROUP BY event_type.id";
            $sql .= " ORDER BY date DESC, event_type.id";
          }
  //echo $sql;
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  $html = '<ul class="all-events">';
  while($row = $result->fetch_assoc()){
    //print_r($row);
    $html .= '<li><a href="'. $domain .'view_events.php?eventID=' . $row['id'] . '">' . date("l jS F, Y",strtotime($row['date'])) . ': ' . htmlentities($row['name']) . '</a></li>';
  }
  $html .= '</ul>';
  return $html;
}

function theme_incoming_stock_list($id=FALSE) {
  global $db;
  $sql = "SELECT * FROM incoming_stock_record"; 

          if ($id != FALSE) {
            $sql .= " WHERE id = $id"; 
            $sql .= " ORDER BY date DESC";
          }  else {
          //$sql .= " GROUP BY event_type.id";
            $sql .= " ORDER BY date DESC";
          }
  //echo $sql;
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  $html = '<ul class="all-events">';
  while($row = $result->fetch_assoc()){
    //print_r($row);
    $html .= '<li>' . date("l jS F, Y",strtotime($row['date'])) . ': ' . htmlentities($row['supplier']) . ' [' . htmlentities(substr($row['notes'],0,100)) . ']</li>';
  }
  $html .= '</ul>';
  return $html;
}

function theme_stock_take_list($id=FALSE) {
  global $db;
  $sql = "SELECT * FROM stock_take_record"; 

          if ($id != FALSE) {
            $sql .= " WHERE id = $id"; 
            $sql .= " ORDER BY date DESC";
          }  else {
          //$sql .= " GROUP BY event_type.id";
            $sql .= " ORDER BY date DESC";
          }
  //echo $sql;
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  $html = '<ul class="all-events">';
  while($row = $result->fetch_assoc()){
    //print_r($row);
    $html .= '<li>' . date("l jS F, Y",strtotime($row['date'])) . ': ' . htmlentities($row['who']) . ' [' . htmlentities(substr($row['notes'],0,100)) . ']</li>';
  }
  $html .= '</ul>';
  return $html;
}

function sum_millilitres_wine ($id,$white_ml,$white_wine_ids,$sold_amount) {
  switch ($id) {
        case $white_wine_ids["small"]:
          $white_ml += 125 * $sold_amount;
          break;
        case $white_wine_ids["medium"]:
          $white_ml += 175 * $sold_amount;
          break;
        case $white_wine_ids["large"]:
          $white_ml += 250 * $sold_amount;
          break;
      }
  return $white_ml;
}
/*
 * 
 * name: calculate_number_of_bottles
 * @param $ml Number of mililitres
 * @return $bottles Number of bottles
 * 
 */

function calculate_number_of_bottles ($ml) {
  if ($ml !=0) {
    $bottles = ceil($ml/750);
    } else {
      $bottles = 0;
    }
  return $bottles;
}
?>
