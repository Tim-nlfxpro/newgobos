<!DOCTYPE HTML>
<html>

<head>
  <title><?php echo $this->title(); ?></title>
  <meta name="description" content="<?php echo $this->meta('description'); ?>" />
  <meta name="keywords" content="<?php echo $this->meta('keywords'); ?>" />
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <script language="javascript" type="text/javascript" src="docroot/js/jquery.js"></script>
  <script language="javascript" type="text/javascript" src="docroot/js/jqueryui.js"></script>
  <link rel="stylesheet" type="text/css" href="docroot/css/jquery/style.css" title="style" />
  <link rel="stylesheet" type="text/css" href="docroot/css/style.css" title="style" />
  <link rel="stylesheet" type="text/css" href="docroot/css/custom.css" title="style" />
  <link href='http://fonts.googleapis.com/css?family=Monoton' rel='stylesheet' type='text/css'>
</head>

<body>
  <div id="main">
    <div id="header">
      <div id="logo">
      <a href="index.php">
      <img src="docroot/images/nlfx.png" style="margin-top: 25px;" border="0" alt="NLFX Pro" />
      </a>
      
      <div id="logo_text" style="margin: -3px 0 0 480px; text-decoariont: none;" >
      <h1 style="font-family: 'Monoton', cursive; font-size: 72px; text-decoration: none;">GOBOS</h1>
      </div>
        <?php /*
	<div id="logo_text" style="margin: 20px 0 0 200px;" >
          <!-- class="logo_colour", allows you to change the colour of the text -->
          <h1><a href="index.php">CUSTOM GOBOS</a></h1>
          <h2>Custom Made, High Quality Gobos by NLFX Pro</h2>
        </div>
	*/ ?>
      </div>
      <div id="menubar">
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
          <li><a href="http://www.nlfxpro.com">NLFX Home</a></li>
          <?php 
          $user = $session->get('user');
	  if($this->module == 'admin') {
	  ?>
	  <li><a href="<?php echo $this->runtime->link('default', 'default', 'default'); ?>">Home</a></li>
	  <li><a href="<?php echo $this->runtime->link('admin','default','default'); ?>">Orders</a></li>
	  <li><a href="<?php echo $this->runtime->link('admin','customer','create'); ?>">Customers</a></li>
	  <li><a href="<?php echo $this->runtime->link('default','default','logout'); ?>">Logout</a></li>
	  <?php
	  } else {
          if($user):
              if($user->is_admin):
          ?>
          <li><a href="<?php echo $this->runtime->link('admin','default','default'); ?>">Admin</a></li>
          <?php 
              endif; 
          ?>
          <?php /*<li class="selected"><a href="<?php echo $this->runtime->link('customer','order','default'); ?>">Order a Gobo</a></li>*/ ?>
          <li><a href="<?php echo $this->runtime->link('customer','default','default'); ?>">My Orders</a></li>
          <?php /*<li><a href="<?php echo $this->runtime->link('customer','profile','default'); ?>">My Profile</a></li>
          <li><a href="#">Help</a></li>*/ ?>
	  <li><a href="<?php echo $this->runtime->link('default','default','logout'); ?>">Logout</a></li>
          <?php endif;
	  } ?>
        </ul>
      </div>
    </div>
    <div id="site_content">
      <div class="sidebar">
        <!-- insert your sidebar items here -->
	
        <h3>Latest News</h3>
        <?php echo $view->nlfxblogrss;
	?>
	<?php /*
	<h3>Gobos for Good</h3>
	<center><img style="margin-top: -10px;" src="docroot/images/toys-for-tots-logo.jpg" width="200"></center>
	
	<p>NLFX Professional is proud to partner with Toys for Tots, a 65 year-old charitable program run by the U.S. Marine Corps Reserve, to provide happiness and hope to disadvantaged children who might otherwise be overlooked during the Christmas holiday season.   During the one season a child would rather be remembered with a gift than anything else in the world, we believe Toys for Tots makes a genuine contribution in over 710 communities nationwide. </p>
	<p>For the month of November, NLFX Professional will make a donation to Toys for Tots for every gobo we ship.</p>
	
	<p><a href="http://nlfxpro.com/gobos-for-good/" target="_blank">Read the full letter from our company President</a></p>
        */ ?>
	<?php /*
        <h3>Useful Links</h3>
        <ul>
          <li><a href="#">link 1</a></li>
          <li><a href="#">link 2</a></li>
          <li><a href="#">link 3</a></li>
          <li><a href="#">link 4</a></li>
        </ul>
        <h3>Search</h3>
        <form method="post" action="#" id="search_form">
          <p>
            <input class="search" type="text" name="search_field" value="Enter keywords....." />
            <input name="search" type="image" style="border: 0; margin: 0 0 -9px 5px;" src="style/search.png" alt="Search" title="Search" />
          </p>
        </form>
        */ ?>
      </div>
      <div id="content">
        <!-- insert the page content here -->
        <?php $this->content(); ?>
      </div>
    </div>
    <div id="content_footer"></div>
    <div id="footer">
      &copy; <?php echo date('Y'); ?>, All Rights Reserved  | <a href="http://www.nlfxpro.com">NLFX Pro</a>
    </div>
  </div>
</body>
</html>
