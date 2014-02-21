<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Kirkgate Bar Stuff</title>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script>
    $(function() {
      $( "#datepicker" ).datepicker();
    });
    </script>
    
    <!--Twitter Bootstrap-->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="assets/css/validate-me.css" rel="stylesheet">
    <link href ="theme/style.css" rel="stylesheet">
    <link href ="theme/print.css" media="print" rel="stylesheet">
  </head>
  <body>
    <div class="navbar navbar-inverse navbar-static-top">
      <div class="navbar-inner">
        <div class="container">

         
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="<?php echo $domain ?>"><?php echo htmlentities($site_name); ?></a></li>
              <li><a href="<?php echo $domain ?>"><i class="icon-home"></i> Home</a></li>
              <!--<li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>-->
            </ul>
          </div><!--/.nav-collapse -->
      </div>
    </div>
  </div>
  <header class="jumbotron subhead" id="overview">
    <div class="container">
      <div class="row">
        <div class="span10">
          <p class="lead">
            <!--<a href="<?php echo $host; ?>"><img src="assets/img/logo.png" width="" height="" alt="IATI Logo" /></a>-->
            <!--<a href="<?php echo $host; ?>">Kirkgate Centre - Bar App</a>-->
          </p>
        </div>
      </div><!--end Row-->
    </div><!-- /container -->
  </header>

 
