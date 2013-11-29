<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Kirkgate Bar Stuff</title>
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
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
  </head>
  <body>
    <div class="navbar navbar-inverse navbar-static-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <!--<a class="brand" href="<?php echo $host ?>">IATI Public Validator</a>-->
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li<?php if ($toptab=='home') { ?> class="active"<?php } ?>><a href="<?php echo $host ?>"><i class="icon-home"></i> Home</a></li>
              <!--<li<?php if ($toptab=='common_errors') { ?> class="active"<?php } ?>><a href="<?php echo $host ?>common_errors.php"><i class="icon-asterisk"></i> Common errors</a></li>
              <li<?php if ($toptab=='developers') { ?> class="active"<?php } ?>><a href="<?php echo $host ?>developers.php"><i class="icon-asterisk"></i> Developers</a></li>-->
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
            <a href="<?php echo $host; ?>">Kirkgate Centre - Bar App</a>
          </p>
        </div>
      </div><!--end Row-->
    </div><!-- /container -->
  </header>

 
