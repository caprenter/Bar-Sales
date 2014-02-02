<?php
  //print_r($_POST); die;
if (isset($_POST) && !empty($_POST)) {
  //Either we are editing previous data and have been sent here from 
  //and event record view
  //print_r($_POST); 
  foreach ($_POST as $key=>$values) {
    if (is_int($key)) { //UPDATE A RECORD
    //echo "UPDATIG";
      $update_msg = update_stock_item($key,$values);
    } elseif (strstr($key,"new_")) { //CREATE A NEW RECORD
   // echo "creating";
    //print_r($values);
      $creation_msg = create_stock_item($values);
    }
  }
}
/*
 * Updates an existing stock record
 * 
 * name: update-stock-item
 * @param $id INT Stock id array
 * @param $Values ARRAY of stock item values
 * @return
 * 
 */

function update_stock_item ($id,$values) {
  global $db;
  //Sanitize variables
  //There should be 4 values in $values
  if (count($values) !=5) {
    echo count($values);
    echo "uhoh";
    $msg = '<div class="alert alert-danger">Update failed</div>';
    return $msg;
  }
  //$id = filter_var($id, FILTER_SANITIZE_INT);
  $name = filter_var($values[1], FILTER_SANITIZE_STRING); //item name
  $cost = filter_var($values[2], FILTER_VALIDATE_FLOAT); //cost price
  $sell = filter_var($values[3], FILTER_VALIDATE_FLOAT);  //sell price
  $weight = filter_var($values[4], FILTER_VALIDATE_INT); //weight in list
  //echo "UPDATIG";
  if ($id && $name && $cost !=NULL && $sell !=NULL && $weight !=NULL) {
    $name = $db->real_escape_string($name);
    //update the database:
    $sql = "UPDATE stock
            SET name = '$name', cost_price = $cost, retail_price = $sell, weight = $weight
            WHERE id = $id;";
    if(!$result = $db->query($sql)){
      die('There was an error running the query [' . $db->error . ']');
    } else {
      $msg = '<div class="alert alert-success">Stock Items Updated</div>';
      return $msg;
    }
  }
}

/*
 * Creates a new stock record
 * 
 * name: create_stock_item
 * @param $Values ARRAY of stock item values
 * @return
 * 
 */

function create_stock_item ($values) {
  global $db;
  //Sanitize variables
  //There should be 4 values in $values
  if (count($values) !=4) {
    //print_r($values);
    //echo count($values);
    //echo "uhoh";
    $msg = '<div class="alert alert-danger">Creation failed</div>';
    return $msg;
  }
  //$id = filter_var($id, FILTER_SANITIZE_INT);
  $name = filter_var($values[0], FILTER_SANITIZE_STRING); //item name
  $cost = filter_var($values[1], FILTER_VALIDATE_FLOAT); //cost price
  $sell = filter_var($values[2], FILTER_VALIDATE_FLOAT);  //sell price
  $weight = filter_var($values[3], FILTER_VALIDATE_INT); //weight in list
  //echo $name . PHP_EOL;
  if ($name && $cost !=NULL && $sell !=NULL && $weight !=NULL) {
    $name = $db->real_escape_string($name);
    //echo "Creating";
    //add to the database:
    $sql = "INSERT INTO stock (name, cost_price, retail_price, weight)
            VALUES ('$name', $cost, $sell, $weight)";
            
    if(!$result = $db->query($sql)){
      echo $db->error;
      die('There was an error running the query [' . $db->error . ']');
    } else {
      $msg = '<div class="alert alert-success">New Stock Items Created</div>';
      return $msg;
    }
  } else {
    $msg = '<div class="alert alert-danger">New Stock Items NOT Created</div>';
      return $msg;
  }
    
}
?>
  

  
