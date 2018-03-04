<?php 

    ob_start();
    $productclass="class='active'";
    $editProdUserclass="class='active'";
    
    include("include/config.php"); 
    include("include/defs.php"); 
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    
    include("header.php"); 

    if(!isset($_SESSION['USER_ID'])) 
     {
          header("Location: index.php");
          exit;
     }
       
     $message="";
    if(isset($_GET['id']))
     {
        $arrUser = GetRecords("SELECT product_by_user.*, product.name as pname, users.Name as tename from product_by_user
                              inner join product on product.id =   product_by_user.id_product
                              inner join users on users.id =   product_by_user.id_user
                               where product_by_user.id = ".$_GET['id']);
        
     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-product-by-user.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">View Product By User</span>
                        </header>
                        <div class="panel-body">
                          
                          
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Product</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['pname'] ?>
                            </div>  
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">User</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['tename'] ?>
                            </div>  
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Price</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['price'] ?>
                            </div>  
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right  font-bold">Status</label>
                            <div class="col-lg-4">
                              <label><?php echo ($arrUser[0]['stat'] == 1) ? 'Active' : 'In Active' ?></label>                                              
                            </div>  
                          </div>    
                        </div>  
                        <?php if($loggdUType == "Admin") { ?>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Edit Product By User</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      //$userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");
                    
                    ?>
 
                  </div>  
              </div>
            </section>
        </section>
    </section>
<?php    
	include("footer.php"); 
?> 