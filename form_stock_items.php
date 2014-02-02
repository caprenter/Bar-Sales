<?php
include('settings.php');

//Make a database connection
$db = new mysqli($host, $database_user, $database_password, $database_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

include('process_stock_items.php');
include('theme/header.php');
//$fetch_data is calculated in _update_stock_items.php and is the data required
//to edit a record. It's a bit messy in the HTML below, checking if it needs
//to be inserted or not.
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Edit stock Items</h1>
      <?php
        if (isset($update_msg)) {
          echo $update_msg;
        }
      ?>
      <?php
        if (isset($creation_msg)) {
          echo $creation_msg;
        }
      ?>
      <form action="form_stock_items.php" method="post">          
        <table class="table table-striped">
          <thead>
            <th>ID</th>
            <th>Item</th>
            <th>Cost</th>
            <th>Sells at</th>
            <th style="width:40px">Sort</th>
          </thead>
          <tbody>
          <?php
            $sql = "SELECT * FROM `stock` ORDER BY weight";

            if(!$result = $db->query($sql)){
                die('There was an error running the query [' . $db->error . ']');
            }

            while($row = $result->fetch_assoc()){
              echo '<tr>';
              echo '<td>' . $row['id'] . '<input name="' . $row['id'] . '[]" value="' . $row['id'] . '" type="hidden" /></td>';
                echo '<td><input name="' . $row['id'] . '[]" value="' . htmlentities($row['name']) . '"/></td>';
                echo '<td><input name="' . $row['id'] . '[]" value="' . $row['cost_price'] . '"/></td>';
                echo '<td><input name="' . $row['id'] . '[]" value="' . $row['retail_price'] . '"/></td>';
                echo '<td><input name="' . $row['id'] . '[]" value="' . $row['weight'] . '" style="width:40px"/></td>';
              echo '</tr>';
            }
                
            ?>
            <tr><td id="addVar" style="background:none">Add Item</td></tr>

          </tbody>
        </table>

        <input type="submit">
        </form>
				
			</div><!--end span10-->
			
      <!--Sidebar-->
			<div class="span2" style="float:left">
        
        <?php include("theme/sidebar.php");?>
        
			</div><!--end Sidebar-->
			
  </div><!--end Row-->

<script>
  var $node = "";
  var varCount=0;

  //remove a textfield    
  $('form').on('click', '.removeVar', function(){
     $(this).parent().remove();
    
  });
  //add a new node
  $('#addVar').on('click', function(){
  varCount++;
  $node =  '<tr><td></td><td><input name="new_'+varCount+'[]"></td><td><input name="new_'+varCount+'[]"></td><td><input name="new_'+varCount+'[]"></td><td><input name="new_'+varCount+'[]"></td></tr>';
  $(this).parent().before($node);
  });
</script>


<?php
include('theme/footer.php');

?>
