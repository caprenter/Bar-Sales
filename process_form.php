<?php

if (isset($_POST) && !empty($_POST)) {
  //Either we are editing previous data and have been sent here from 
  //and event record view
  if (isset($_POST["event-id"])) {
    $event_id = filter_var($_POST["event-id"], FILTER_SANITIZE_NUMBER_INT);
    if (filter_var($event_id, FILTER_VALIDATE_INT)) {
      //Get the info about this event to pre-populate the form.
      $fetch_data = populate_form_with_saved_data($event_id);
    }
    //print_r($fetch_data);
  } else {
    //OR we are processing this form to save data
    if (!isset($_POST['datepicker']) || empty($_POST['datepicker']) || !isset($_POST['event_type']) || empty($_POST['event_type'])) {
      $msg = '<div class="alert alert-danger">Why you little...! Fill in stuff properly!</div>';
    } else {
      $event_id = save_event_sales_data($_POST);
      //Redirect to event page view once saved
      header('Location: ' . $domain . '/view_event.php?eventID=' . $event_id);
    }
  }
}

/* If the page is passed an event-id in the $_POST variable
 * then we want to edit an existing record.
 * So this function gets the data need to pre-populate the form.
 * 
 * name: populate_form_with_saved_data
 * @param $event_id Integer The id of an event
 * @return
 * 
 */

function populate_form_with_saved_data($event_id) {
  global $db;
  $sql = "SELECT * FROM event_record
          JOIN event_type ON event_record.event_type_id = event_type.id
          WHERE event_record.id = $event_id";
          
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  //Simple event info - name, type of event, date.
  while($row = $result->fetch_assoc()){
    $data['event_type_id'] = $row["id"];
    $data['event_name'] = $row["name"];
    $data['event_date'] = date("m/d/Y",strtotime($row["date"]));
  }
  
  //Now get the sales record
  $sql = "SELECT * FROM sales_record
          WHERE event_record_id = $event_id";
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }
  //Simple event info - name, type of event, date.
  while($row = $result->fetch_assoc()){
    //$data['sales'][] = array("stock_id" => $row['stock_id'],"number_sold" => $row['number_sold']);
    $data['sales'][$row['stock_id']] = $row['number_sold'];
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

function save_event_sales_data($_POST) {
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

  $sql = "SELECT id FROM `event_type`";
  
  if(!$result = $db->query($sql)){
    die('There was an error running the query [' . $db->error . ']');
  }

  while($row = $result->fetch_assoc()){
    $event_type_ids[] = $row["id"];
  }
  
  //Event Type
  if (isset($_POST['event_type'])) {
    $event_type = filter_var($_POST['event_type'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($event_type, FILTER_VALIDATE_INT)) {
      unset($event_type);
    }
    if (isset($event_type) && in_array($event_type,$event_type_ids)) {
      //check it is a valid value in the database.
      //The value should be one of the id's in the event_type table
    } else {
      unset($event_type);
    }
  }
  
  //Quantities
  //Sent as quantity1, quantity2, etc where the number is the stock id
  $quantities = array(); //empty array to store quantity info
  
  

  //Fetch stock id's from the database
  $sql = "SELECT id, retail_price FROM `stock`";
  if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
  }
  $stock = array(); //save the price info for each item as we will use this later
  while($row = $result->fetch_assoc()){
    $quantity = 'quantity' . $row['id']; //ie quantity1, quantity2, etc - these are possible post variables
    $stock[$row['id']] = $row['retail_price']; //store price in array indexed by id
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
  
  //OR UPDATE A RECORD if $_POST['update_event'] is set
  if (isset($_POST['update_event'])) {
    $update_event = filter_var($_POST['update_event'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($update_event, FILTER_VALIDATE_INT)) {
      unset($update_event);
    }
  }
  
  if(!isset($update_event)) { //Create a new event record if update_event is not set
    if (isset($date) && isset($event_type)) {
      $sql = "INSERT INTO event_record (event_type_id,date ) VALUES (" . $event_type . ", STR_TO_DATE('" . $date . "','%m/%d/%Y'))";
      echo $sql;
      if(!$result = $db->query($sql)){
          die('There was an error running the query [' . $db->error . ']');
      } else {
        $event_record_id = mysqli_insert_id($db); //last auto increment ide created by the above query
        //echo $event_record_id;
      }
        
    }
  } else {
    //We may need to update the date and the event type:
    if (isset($date) && isset($event_type)) {
      $sql = "UPDATE event_record 
              SET event_type_id=$event_type, date=STR_TO_DATE('" . $date . "','%m/%d/%Y') 
              WHERE id=$update_event";
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
  if(isset($update_event)) {
    //Delete existing sales record!!!!
    $sql = "DELETE FROM sales_record WHERE event_record_id = $update_event";
    if(!$result = $db->query($sql)){
          die('There was an error running the query [' . $db->error . ']');
    }
    $event_record_id = $update_event;
  }
  foreach ($quantities as $stock_id=>$num_sold) { //$key actuall == stock_id value)
    $price = $stock[$stock_id]; //$price is looked up against the stock info gathered earlier
    $sql = "INSERT INTO `sales_record` VALUES ($event_record_id,$stock_id,$num_sold,$price)";
    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
  }
  return $event_record_id;
}
  

  
