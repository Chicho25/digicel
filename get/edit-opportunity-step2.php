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
        $stval = (isset($_POST['status'])) ? 1 : 0;
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
                      "stat" => $stval
                     );
          
          UpdateRec("opportunity", "id=".$_REQUEST['id'], $arrVal);    
          $nId=$_REQUEST['id'];
            

          if($nId > 0 && count($prodDetail) > 0)
          {
              
              $getrefid = GetRecord("opportunity", "id = ".$_REQUEST['id']);
              $mstId = $getrefid['id_ref_master_note'];  
              UpdateRec("opportunity", "id = ".$nId, array("id_ref_master_note" => $mstId));
              
              if($mstId > 0)
              {
                $notemsg = "Opportunity ".$oppname." updated by ".$_SESSION['USER_NAME'];
                $subj = "Opportunity ( ".$name." ) updated";
                create_log($mstId, $subj, $notemsg);    
              }

              DeleteRec("opportunity_detail", "id_opportunity=".$nId);
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
              echo "<script>alert('Opportunity updated');window.location='opportunity-view.php?id=".$_REQUEST['id']."';</script>";
          }
          else
          {
            echo "<script>alert('Opportunity not updated');window.location='opportunity-view.php?id=".$_REQUEST['id']."';</script>";
          }
        
          
        
     }
     $arrUser = GetRecord("opportunity", "id = ".$_REQUEST['id']);
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post"   enctype="multipart/form-data">
                      <input type="hidden" value="<?php echo $arrUser['id']?>" name="id">
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
                      <?php if(isset($_POST['status'])) { ?>
                      <input type="hidden" name="status" value="<?php echo $st = (isset($_POST['status'])) ? $_POST['status'] : '';?>" >
                      <?php } ?>
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Edit Opportunity Step 2</span>
                        </header>
                        <div class="panel-body">
                          
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Name Option</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name Option" value="<?php echo $arrUser['option_name']?>" name="optname" data-required="true">                        
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Description</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="description" data-required="true" placeholder="Description"><?php echo $arrUser['option_description']?></textarea>     
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
                          <?php 
                          $arrOppDetail = GetRecords("select opportunity_detail.*, product.name as pname  from  opportunity_detail inner join product on product.id = opportunity_detail.id_product where id_opportunity = ".$arrUser['id']);
                          foreach ($arrOppDetail as $key => $value) {
                            $hdata = $value['id_category']."::::".$value['id_product']."::::".$value['original_price']."::::".$value['sale_price']."::::".$value['quantity']."::::".$value['description'];
                          ?>
                              
                              <tr>
                                <input type='hidden' name='h1[]' value='<?php echo $hdata?>'>
                                <td><?php echo $value['pname']?></td>
                                <td><?php echo $value['original_price']?></td>
                                <td><?php echo $value['sale_price']?></td>
                                <td><?php echo $value['quantity']?></td>
                                <td><a onclick="editRM('<?php echo $hdata?>')" href='modal-opportunitydetail.php' data-toggle='ajaxModal'><i class='glyphicon glyphicon-pencil'></i></a>&nbsp;&nbsp;<i onclick='rm()' class='glyphicon glyphicon-remove'></i></td>
                              </tr>
                          <?php
                          }
                          ?>
                          </tbody>
                       </table>
                     </div>
                     <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitStep2" class="btn btn-primary btn-s-xs">Edit Option</button>
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