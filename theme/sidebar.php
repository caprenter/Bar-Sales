<?php
//Sidebar Navigation elements
//$domain is set in settings.php
?>
<div class="well sidebar-nav">
  <ul class="nav nav-list">
    <li class="nav-header">Reports</li>
    <li><a href="<?php echo $domain; ?>view_stock_levels.php">Stock Levels</a></li>
    <li><a href="<?php echo $domain; ?>total_sales.php">Total Sales</a></li>
    <li><a href="<?php echo $domain; ?>view_events.php">Event Reports</a></li>
    <li><a href="<?php echo $domain; ?>view_incoming_stock_records.php">Incoming Stock</a></li>
    <li><a href="<?php echo $domain; ?>view_stock_take_records.php">Stock Take</a></li>
  </ul>
</div><!--/.well -->
<div class="well sidebar-nav">
  <ul class="nav nav-list">
    <li class="nav-header">Data entry</li>
    <li><a href="<?php echo $domain; ?>form_sales_entry.php">Enter Sales</a></li>
    <li><a href="<?php echo $domain; ?>form_incoming_stock.php">Enter Stock</a></li>
    <!--<li><a href="<?php echo $domain; ?>form_stock_take.php">Stock Take</a></li>-->
  </ul>
</div><!--/.well -->
<div class="well sidebar-nav">
  <ul class="nav nav-list">
    <li class="nav-header">Edit</li>
    <li><a href="<?php echo $domain; ?>form_stock_items.php">Stock Items</a></li>

  </ul>
</div><!--/.well -->
