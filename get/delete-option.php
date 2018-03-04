<?php 

    ob_start();
    
    include("include/config.php"); 
    include("include/defs.php"); 
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
  	
  	if(!isset($_SESSION['USER_ID'])) 
     {
          header("Location: index.php");
          exit;
     }

     if(isset($_GET['optid']) && $_GET['optid'] > 0 && isset($_GET['opprid']) && $_GET['opprid'] > 0 )
     {
		DeleteRec("opportunity_detail", "id_option = ".$_GET['optid']);
     	DeleteRec("opportunity_option", "id = ".$_GET['optid']);

     	header("Location: opportunity-view.php?id=".$_GET['opprid']);
     }
?>