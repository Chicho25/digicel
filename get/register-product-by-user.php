<?php 

    ob_start();
    $productclass="class='active'";
    $registerProdUserclass="class='active'";
    
    include("include/config.php"); 
    include("include/defs.php"); 
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    
    include("header.php"); 

    if(!isset($_SESSION['USER_ID']) || $loggdUType != "Admin") 
     {
          header("Location: index.php");
          exit;
     }
     $message="";

    if(isset($_POST['submitUser']))
     {
          $user = $_POST['user'];
          $product = $_POST['product'];
          $price = $_POST['price'];
          $arrVal = array(
                        "id_user" => $user,
                        "id_product" => $product,
                        "price" => $price,
                        "stat" => 1
                       );
            
          $nCountPT = RecCount("product_by_user", "id_user = ".$user." and id_product = ".$product);
          if($nCountPT > 0)
          {
              $message = '<div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Already registered product with this User</strong>
                    </div>';
          }
          else
          {
            $nId = InsertRec("product_by_user", $arrVal);    

            if($nId > 0)
            {
                MySQLQuery("Insert into master_notes (id) values (0)");

                $mstId = mysql_insert_id();   
                UpdateRec("product_by_user", "id = ".$nId, array("id_ref_master_note" => $mstId));
                $getProdName = GetRecord("product", "id=".$product);
                $getUserName = GetRecord("users", "id=".$user);
                if($mstId > 0)
                {
                  $notemsg = "Product (".$getProdName['name'].") Registered by User ( ".$getUserName['Name']." ) registered by ".$_SESSION['USER_NAME'];
                  $subj = "Product (".$getProdName['name'].") Registered by User ( ".$getUserName['Name']." )";
                  create_log($mstId, $subj, $notemsg);    
                }

                $message = '<div class="alert alert-success">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Product Registered By User created successfully</strong>
                      </div>';
            }
            else
            {
              

              $message = '<div class="alert alert-danger">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Product Registered By User  not created</strong>
                    </div>';
            }
        
          
        }
     }
?>
  <section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              
              <div class="row">
                <div class="col-sm-12">
                  <form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Register Product By User</span>
                        </header>
                        <div class="panel-body">
                          <?php 
                                if($message !="")
                                    echo $message;
                          ?> 
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select User</label>
                            <div class="col-lg-4">
                              <select class="form-control" name="user" required="required" >
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from users where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name'];
                                  
                                  $selRoll = (isset($_GET['cid']) && $_GET['cid'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>                     
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Product</label>
                            <div class="col-lg-4">
                              <select class="form-control" name="product" required="required" >
                                <option value="">----------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from product where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];
                                  
                                  $selRoll = (isset($_GET['cid']) && $_GET['cid'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?PHP echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>                     
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Price</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Price" name="price" data-required="true">                        
                            </div>  
                          </div>
                        </div>  
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Register Product By User</button>
                        </footer>
                      </section>
                    </form>
                  </div>  
              </div>
            </section>
        </section>
    </section>
    
<?php    
  include("footer.php"); 
?> 