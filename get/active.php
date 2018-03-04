<?php 

    ob_start();
    $userclass="";
    $registerclass="";
    
    include("include/config.php"); 
    include("include/defs.php"); 
    
    include("header.php"); 

    
	if(!empty($_GET["key"])) 
	{

		if(RecCount("users", "activationkey = '".$_GET['key']."'") > 0) 
		{
			UpdateRec("users", "activationkey = '".$_GET['key']."'", array("stat" => 1));
			$message = "<div class='alert alert-success'>Your account is activated. Please login <a href='index.php'>here</a></div>";
		} else 
		{
			$message = "<div class='alert alert-danger'>Problem in account activation.</div>";
		}
?>
		<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              
              <div class="row">
                <div class="col-sm-12">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Account Activation</span>
                        </header>
                        <div class="panel-body">
                        <?php 
                                if($message !="")
                                    echo $message;
                          ?>
                        </div>
                      </section>
                 </div>
              </div>
            </section>
         </section>
       </section>                 
<?php		
		
	}
?>