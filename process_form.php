<?php

if (isset($_POST)) {
  //Date
  if (isset($_POST['datepicker'])) {
    $date = filter_var($_POST['datepicker'],FILTER_SANITIZE_STRING);
    //Should be a date in mm/dd/yyyy format
    //Coule maybe check the date is reasonable - i.e. within a coule of months?
    //echo $date;
    //echo $_POST['datepicker'];
  }

  //Make a database connection
  //We want to check that submitted values relate to records in the database
  $db = new mysqli($host, $database_user, $database_password, $database_name);
  if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
  }

  //Event Type
  if (isset($_POST['event_type'])) {
    $event_type = filter_var($_POST['event_type'],FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($event_type, FILTER_VALIDATE_INT)) {
      unset($event_type);
    }
    if (isset($event_type)) {
      //check it is a valid value in the database.
      //The value should be one of the id's in the event_type table
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
    $quantity = 'quantity' . $row['id'];
    $stock[$row['id']] = $row['retail_price']; //store price in array indexed by id
    if (isset($_POST[$quantity])) {
     $quantities[$row['id']] =  filter_var($_POST[$quantity],FILTER_SANITIZE_NUMBER_INT);
     
    }
  }
  
  
  //Database inserts
  /*
  INSERT INTO `event_record` ($event_type, $date) 
  //Need to get the event_record_id this creates
  foreach ($quantities as $key=>$value) {
    $price = $stock[$key]; //$price is looked up against the stock info gathered earlier
  INSERT INTO `sales_record` ($event_record_id, $stock_id, $quantity, $price)
  }
  
  * */
}
  
