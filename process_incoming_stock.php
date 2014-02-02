<?php

if (isset($_POST) && !empty($_POST)) {
  //Either we are editing previous data and have been sent here from 
  //a stock record view
  if (isset($_POST["incoming-stock-record-id"])) {
    $stock_record_id = filter_var($_POST["incoming-stock-record-id"], FILTER_SANITIZE_NUMBER_INT);
    if (filter_var($stock_record_id, FILTER_VALIDATE_INT)) {
      //Get the info about this stock-record to pre-populate the form.
      $fetch_data = populate_form_with_saved_data($stock_record_id);
    }
    //print_r($fetch_data);
  } else {
    //OR we are processing this form to save data
    if (!isset($_POST['datepicker']) || empty($_POST['datepicker']) ) {
      $msg = '<div class="alert alert-danger">Why you little...! Fill in stuff properly!</div>';
    } else {
      $stock_record_id = save_incoming_stock($_POST);
      //Redirect to stock record page view once saved
      header('Location: ' . $domain . 'view_incoming_stock_records.php?incoming-stock-record-id=' . $stock_record_id);
    }
  }
}

/* If the page is passed an stock-record-id in the $_POST variable
 * then we want to edit an existing record.
 * So this function gets the data need to pre-populate the form.
 * 
 * name: populate_form_with_saved_data
 * @param $event_id Integer The id of an event
 * @return
 * 
 */

function populate_form_with_saved_data($stock_record_id) {
  global $db;
  $sql = "SELECT * FROM incoming_stock_record
          WHERE id = $stock_record_id";
          
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  //Simple event info - name, type of event, date.
  while($row = $result->fetch_assoc()){
    $data['notes'] = $row["notes"];
    $data['supplier'] = $row["supplier"];
    $data['date'] = date("m/d/Y",strtotime($row["date"]));
  }
  
  //Now get the sales record
  $sql = "SELECT * FROM stock_in
          WHERE incoming_stock_record_id = $stock_record_id";
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  //Simple event info - name, type of event, date.
  while($row = $result->fetch_assoc()){
    //$data['sales'][] = array("stock_id" => $row['stock_id'],"number_sold" => $row['number_sold']);
    $data['stock'][$row['stock_id']] = $row['number_received'];
  }
  return $data;
}




/* If the form has been filled in then the data needs to be 
 * checked and then saved to the database
 * 
 * name: save_event_sales_data
 * @param $_POST Posted data from the form
 * @return
 * 
 */

function save_incoming_stock($_POST) {
  global $db;
  print_r($_POST);
  if (isset($_POST['datepicker'])) {
    $date = filter_var($_POST['datepicker'],FILTER_SANITIZE_STRING);
    //Should be a date in mm/dd/yyyy format
    //Coule maybe check the date is reasonable - i.e. within a coule of months?
    //REgex : ^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d$.
    //See:http://www.regular-expressions.info/dates.html
    //echo $date;
    //echo $_POST['datepicker'];
  }
  if (isset($_POST['supplier'])) {
    $supplier = filter_var($_POST['supplier'],FILTER_SANITIZE_STRING);
  } 
  if (isset($_POST['notes'])) {
    $notes = filter_var($_POST['notes'],FILTER_SANITIZE_STRING);
  }

  //Quantities
  //Sent as quantity1, quantity2, etc where the number is the stock id
  $quantities = array(); //empty array to store quantity info
  
  //Fetch stock id's from the database
  $sql = "SELECT id, cost_price FROM `stock`";
  if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
  }
  $stock = array(); //save the price info for each item as we will use this later
  while($row = $result->fetch_assoc()){
    $quantity = 'quantity' . $row['id']; //ie quantity1, quantity2, etc - these are possible post variables
    $stock[$row['id']] = $row['cost_price']; //store th cost price in array indexed by id
    if (isset($_POST[$quantity])) { //if the post variable e.g. quantity1 exists....
      if($_POST[$quantity] == 0) { //Filter sanitize int disguards zero, so set this special case up to record zero sales
        $quantity_value = 0;
        $quantities[$row['id']] = $quantity_value;
        unset($quantity_value);
      } elseif ( $quantity_value =  filter_var($_POST[$quantity],FILTER_SANITIZE_NUMBER_INT) ) {
        $quantities[$row['id']] = $quantity_value;
        unset($quantity_value);
      }
    }
  }
  
  
  //Database inserts
  /*
   * Create an event record.
   * Store the event type and the date.
   * Return the id of this record to record the sales of staock against
  */
  
  //OR UPDATE A RECORD if $_POST['update_stock_record'] is set
  if (isset($_POST['update_stock_record'])) {
    $update_record = filter_var($_POST['update_stock_record'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($update_record, FILTER_VALIDATE_INT)) {
      unset($update_record);
    }
  }
 
  if(!isset($update_record)) { //Create a new stock record if update_record is not set
    if (isset($date)) {
      $sql = "INSERT INTO incoming_stock_record (date,supplier,notes ) VALUES (STR_TO_DATE('" . $date . "','%m/%d/%Y'),'$supplier','$notes')";
      echo $sql;
      if(!$result = $db->query($sql)){
          die('There was an error running the query [' . $db->error . ']');
      } else {
        $incoming_stock_record_id = mysqli_insert_id($db); //last auto increment ide created by the above query
        //echo $event_record_id;
      }
        
    }
  } else {
    //We may need to update the date and the event type:
    if (isset($date)) {
      $sql = "UPDATE incoming_stock_record 
              SET date=STR_TO_DATE('" . $date . "','%m/%d/%Y'), supplier='$supplier', notes='$notes'
              WHERE id=$update_record";
      echo $sql;
      if(!$result = $db->query($sql)){
          die('There was an error running the query [' . $db->error . ']');
      }         
    }
  }

  /*
   * Store the sales against the event.
   * Use the event_record_id generated above FOR NEW EVENTS to store the stock id, the number sold and the price
   * We store the price so that in future the price can change in the stock record, but we know 
   * what we sold it for at the time
   * 
  */
  if(isset($update_record)) {
    //Delete existing sales record!!!!
    $sql = "DELETE FROM stock_in WHERE incoming_stock_record_id = $update_record";
    if(!$result = $db->query($sql)){
          die('There was an error running the query [' . $db->error . ']');
    }
    $incoming_stock_record_id = $update_record;
  }
  foreach ($quantities as $stock_id=>$num_bought) { //$key actuall == stock_id value)
    $bought_at = $stock[$stock_id]; //$bought_at is looked up against the cost price stock info gathered earlier
    
    $sql = "INSERT INTO `stock_in` VALUES ($incoming_stock_record_id,$stock_id,$num_bought,$bought_at)";
    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
  }
  return $incoming_stock_record_id;
}
  

  
