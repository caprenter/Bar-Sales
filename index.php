<?php
include('settings.php');
include('theme/header.php');
?>


<div class="container">
  <div class="row">
    <div class="span10" style="float:right">
      <h1>Kirkgate Stock Application</h1>
    
    <div class="event-record-header">
      <h2>Instructions</h2>
      <h3>Update your stock list</h3>
        <p>Make sure the prices are up to date in <a href="<?php echo $domain; ?>form_stock_items.php">Stock Items</a>.</p>
        <p>
        Prices change over time and we do not keep a historical record, except against an item as it is sold. This means whenever you create an incoming stock record, or a sales record, the current cost and retail prices are stored against those records. These prices are then use to calculate <a href="<?php echo $domain; ?>total_sales.php">Total Sales</a>.</p>
        <p>This also means you need to make sure the prices are right before you create those records, otherwise you'll just have to do them again!</p>
      <h3>Before stock taking...</h3>
        <p> Make sure all the sales records and incoming stock records are completed.<br/>
          When we take stock we make a shrinkage record which can only be altered by deleting a stock taking record and re-entering it.
        </p>
        <h3>Receiving stock (Incoming stock)</h3>
        <p> Before you <a href="<?php echo $domain; ?>form_incoming_stock.php">Enter Stock</a>, make sure the cost values are updated in <a href="<?php echo $domain; ?>form_stock_items.php">Stock Items</a>.
        </p>
        <h3>Sales Records</h3>
        <p> Each sales record will make a record of:</p>
          <ul>
            <li>the number of items sold</li>
            <li>the retail price at the time</li>
            <li>the cost price at the time</li>
          </ul>
        </p>
        <p>You can easily edit the number of items, but NOT the prices. The prices are set in the <a href="<?php echo $domain; ?>form_stock_items.php">Stock Items</a> view.</p>
        <h3>Wine and Juices</h3>
        <p>Wine sales are by the glass.<br/>Wine is bought in bottles.<br/> So, when you <a href="<?php echo $domain; ?>form_incoming_stock.php">Enter Stock</a> put bottles in as the number of bottles.</p>
        <p>A bottle of wine contains:</p>
        <ul><li>3x250ml (large) glasses</li><li>4x175ml (medium) glasses</li><li>6x125ml (small) glasses</li></ul>
        <p>For every 4 medium glases of wine we calculate there will be 1/15th of a bottle wasted</p>
        <p>Juices come in small individual cartons or large cartons and are then sold by the glass.<br/>A large carton of juice contains 6 glasses.</p>
    </div>
    <div class="events-list">
      
    
    </div>
    <!--Sales Record table -->
  </div><!--end span10-->
			
    <!--Sidebar-->
    <div class="span2" style="float:left">
      
      <?php include("theme/sidebar.php");?>
      
    </div><!--end Sidebar-->
        
  </div><!--end Row-->
</div>
<?php
include('theme/footer.php');
?>

