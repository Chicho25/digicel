<?php 
    session_start();
    ob_start();
    $opportunityclass="class='active'";
    $registerOpporclass="class='active'";
    
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

     
    if(isset($_POST['submitStep2']))
     {
        $contactid = $_POST['contactid'];
        $oppname = $_POST['oppname'];
        $oppdesc = $_POST['oppdesc'];
        $nextstepdate = $_POST['nextstepdate'];
        $preparationdate = $_POST['preparationdate'];
        $proposaldate = $_POST['proposaldate'];
        $closingdate = $_POST['closingdate'];
        $stage = $_POST['stage'];
        $estimatedamount = $_POST['estimatedamount'];
        $source = $_POST['source'];
        $nextstep = $_POST['nextstep'];
        $name = $_POST['optname'];
        $description = $_POST['description'];
        $prodDetail = $_POST['h1'];
        $arrVal = array(
                      "name" => $oppname,
                      "id_contact" => $contactid,
                      "description" => $oppdesc,
                      "nextstep_date" => $nextstepdate,
                      "preparation_date" => $preparationdate,
                      "proposal_date" => $proposaldate,
                      "closing_date" => $closingdate,
                      "id_stage" => $stage,
                      "estimated_amount" => $estimatedamount,
                      "source" => $source,
                      "nextstep" => $nextstep,
                      "option_name" => $name,
                      "option_description" => $description,
                      "id_user_register" => $_SESSION['USER_ID'],
                      "created_date" => date("Y-m-d"),
                      "stat" => 1
                     );
               
          $nId = InsertRec("opportunity", $arrVal);    

          if($nId > 0 && count($prodDetail) > 0)
          {
              
              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();   
              UpdateRec("opportunity", "id = ".$nId, array("id_ref_master_note" => $mstId));
              
              if($mstId > 0)
              {
                $notemsg = "New opportunity ".$oppname." registered by ".$_SESSION['USER_NAME'];
                $subj = "opportunity ( ".$name." ) registered";
                create_log($mstId, $subj, $notemsg);    
              }

              foreach ($prodDetail as $key => $value) {
                $expVal = explode("::::", $value);

                $arrVal = array(
                      "id_opportunity" => $nId,
                      "id_category" => $expVal[0],
                      "id_product" => $expVal[1],
                      "original_price" => $expVal[2],
                      "sale_price" => $expVal[3],
                      "quantity" => $expVal[4],
                      "description" => $expVal[5]
                     );
                InsertRec("opportunity_detail", $arrVal);  
              }
              echo "<script>alert('Opportunity created');window.location='register-opportunity-step1.php';</script>";
          }
          else
          {
            echo "<script>alert('Opportunity not created');window.location='register-opportunity-step1.php';</script>";
          }
        
          
        
     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data">
                      <input type="hidden" name="contactid" value="<?php echo $_POST['contact']?>" >
                      <input type="hidden" name="oppname" value="<?php echo $_POST['name']?>" >
                      <input type="hidden" name="oppdesc" value="<?php echo $_POST['description']?>" >
                      <input type="hidden" name="nextstepdate" value="<?php echo $_POST['nextstepdate']?>" >
                      <input type="hidden" name="preparationdate" value="<?php echo $_POST['preparationdate']?>" >
                      <input type="hidden" name="proposaldate" value="<?php echo $_POST['proposaldate']?>" >
                      <input type="hidden" name="closingdate" value="<?php echo $_POST['closingdate']?>" >
                      <input type="hidden" name="stage" value="<?php echo $_POST['stage']?>" >
                      <input type="hidden" name="estimatedamount" value="<?php echo $_POST['estimatedamount']?>" >
                      <input type="hidden" name="source" value="<?php echo $_POST['source']?>" >
                      <input type="hidden" name="nextstep" value="<?php echo $_POST['nextstep']?>" >
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Register Opportunity Step 2</span>
                        </header>
                        <div class="panel-body">
                          
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Name Option</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name Option" name="optname" data-required="true">                        
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Description</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="description" data-required="true" placeholder="Description"></textarea>     
                            </div>  
                          </div>
                          <div class="form-group required">
                            <div class="col-lg-4 text-right">
                              <a href="modal-opportunitydetail.php" data-toggle="ajaxModal" class="btn btn-sm btn-primary">Add Product</a>
                            </div>
                          </div>
                          
                        </div>  
                        
                      </section>

                      <div class="table-responsive">
                        <table class="table table-striped b-t b-light tableproduct">
                          <thead>
                            <tr>
                              <th>PRODUCT</th>
                              <th>ORIGINAL PRICE</th>
                              <th>SALE PRICE</th>
                              <th>QUANTITY</th>
                              <th>ACTION</th>
                            </tr>
                          </thead>
                          <tbody>
                          </tbody>
                          <tfoot>
                            <tr>
                              <th>Total</th>
                              <th id="orgtotal"></th>
                              <th id="saletotal"></th>
                              <th id="qtytotal"></th>
                              <th></th>
                              <th></th>
                            </tr>
                          </tfoot>
                       </table>
                     </div>
                     <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitStep2" class="btn btn-primary btn-s-xs">Register Option</button>
                        </footer>     
                    </form>
                  </div>  
              </div>
            </section>
        </section>
    </section>

 
<?php    
	include("footer.php"); 
?> 