<?php

if (isset($_POST)) {
  //Date
  if (isset($_POST['datepicker'])) {
    $date = filter_var($_POST['datepicker'],FILTER_SANITIZE_STRING);
    //Should be a date in mm/dd/yyyy format
    //Coule maybe check the date is reasonable - i.e. within a coule of months?
    //REgex : ^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)\d\d$.
    //See:http://www.regular-expressions.info/dates.html
    echo $date;
    //echo $_POST['datepicker'];
  }

  //Make a database connection
  //We want to check that submitted values relate to records in the database
  $db = new mysqli($host, $database_user, $database_password, $database_name);
  if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
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
      if( $quantity_value =  filter_var($_POST[$quantity],FILTER_SANITIZE_NUMBER_INT) ) {
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
  if (isset($date) && isset($event_type)) {
    $sql = "INSERT INTO event_record (event_type_id,date ) VALUES (" . $event_type . ", STR_TO_DATE('" . $date . "','%m/%d/%Y'))";
    echo $sql;
    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    } else {
      $event_record_id = mysqli_insert_id($db); //last auto increment ide created by the above query
      echo $event_record_id;
    }
      
  }
  /*
   * Store the sales against the event.
   * Use the event_record_id generated above to store the stock id, the number sold and the price
   * We store the price so that in future the price can change in the stock record, but we know 
   * what we sold it for at the time
   * 
  */
  foreach ($quantities as $stock_id=>$num_sold) { //$key actuall == stock_id value)
    $price = $stock[$stock_id]; //$price is looked up against the stock info gathered earlier
    $sql = "INSERT INTO `sales_record` VALUES ($event_record_id,$stock_id,$num_sold,$price)";
    if(!$result = $db->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }
  }
}
  

  
