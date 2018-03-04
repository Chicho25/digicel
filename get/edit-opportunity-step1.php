<?php 

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
    
    $arrUser = GetRecord("opportunity", "id = ".$_REQUEST['id']);
    $status = ($arrUser['stat'] == 1) ? 'checked' : ''; 
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post" action="edit-opportunity-step2.php">
                      <input type="hidden" value="<?php echo $arrUser['id']?>" name="id">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Edit Opportunity Step 1</span>
                        </header>
                        <div class="panel-body">
                          
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Contact</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="contact" required="required">
                                <option value="">----------------</option>
                                <?PHP
                                $where = "";
                                 if($loggdUType != "Admin")
                                 {
                                   $where.=" and id_user_register=". $_SESSION['USER_ID'];
                                 }
                                $arrKindMeetings = GetRecords("Select * from contact where stat = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name'];
                                  $selRoll = (isset($arrUser['id_contact']) && $arrUser['id_contact'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>                     
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Name Opportunity</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Name Opportunity" value="<?php echo $arrUser['name']?>" name="name" data-required="true">                        
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Description</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="description"  data-required="true" placeholder="Description"><?php echo $arrUser['description']?></textarea>     
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Estimated Amount</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Estimated Amount" value="<?php echo $arrUser['estimated_amount']?>" name="estimatedamount" data-required="true">
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Source</label>
                            <div class="col-lg-4">
                               <select class="chosen-select form-control" name="source" data-required="true">
                                <option value="">----------------</option>
                                <?PHP
                                $where = "";
                                 
                                $arrKindMeetings = GetRecords("Select * from source where status = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name_source'];
                                  $selRoll = (isset($arrUser['source']) && $arrUser['source'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>                          
                            </div>  
                          </div>
                          
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Next Step</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Next Step" value="<?php echo $arrUser['nextstep']?>" name="nextstep" data-required="true">                        
                            </div>  
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Date of Next Step</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker" name="nextstepdate" size="16" readonly="" data-required="true" type="text" value="<?php echo $arrUser['nextstep_date']?>"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Date of preparation of proposal</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker1" name="preparationdate" size="16" readonly="" data-required="true" type="text" value="<?php echo $arrUser['preparation_date']?>"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Proposal delivery date</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker2" name="proposaldate" size="16" readonly="" data-required="true" type="text" value="<?php echo $arrUser['proposal_date']?>"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Estimated closing date</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker3" name="closingdate" size="16" readonly="" data-required="true" type="text" value="<?php echo $arrUser['closing_date']?>"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Select Stage</label>
                            <div class="col-lg-4">
                               <select class="chosen-select form-control" name="stage" data-required="true">
                                <option value="">----------------</option>
                                <?PHP
                                $where = "";
                                 
                                $arrKindMeetings = GetRecords("Select * from stage where status = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name_stage'];
                                  $selRoll = (isset($arrUser['id_stage']) && $arrUser['id_stage'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>                          
                            </div>  
                          </div>
                          

                          <div class="form-group required">
                            <label class="col-lg-4 font-bold control-label">Active/Deactive</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="status" data-required="true">
                                <?php
                                
                                foreach ($arrStatus as $key => $value) {
                                  $selRoll = (isset($arrUser['stat']) && $arrUser['stat'] == $key) ? 'selected' : ''
                                ?>
                                  <option value="<?php echo $key?>" <?php echo $selRoll?>><?php echo $value?></option>
                                <?php
                                }
                                ?>
                              </select>  
                            </div>
                          </div> 
                        </div>  
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitUser" class="btn btn-primary btn-s-xs">Continue With Step 2</button>
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