<?php 

    ob_start();
    $countryclass="class='active'";
    $editRegclass="class='active'";
    
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
        $arrUser = GetRecords("select * from region where id = ".$_GET['id']);
        
     }
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" action="edit-region.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">View Region</span>
                        </header>
                        <div class="panel-body">
                          
                          
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Region Name</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['name'] ?>
                            </div>  
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right font-bold">Population</label>
                            <div class="col-lg-4">
                              <?php echo $arrUser[0]['population'] ?>
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
                          <button type="submit" name="editUser" class="btn btn-primary btn-s-xs">Edit Region</button>
                        </footer>
                        <?php } ?>
                      </section>
                    </form>
                    <?php
                      $userrefid = $arrUser[0]['id_ref_master_note'];
                      //$userimage = $arrUser[0]['Image'];
                      include("notes-attached.php");
                    $arrTerritory = GetRecords("SELECT * from territory where id_region = ".$_GET['id']."
                             order by name");
                    ?>

                      <section class="panel panel-default">
                          <header class="panel-heading">
                            <span class="h4">Territory that belong to this region</span>
                            <?php if($loggdUType == "Admin") { ?>
                            <span><a href="register-territory.php?cid=<?php echo $arrUser[0]['id_country']?>&rid=<?php echo $_GET['id']?>"  class="btn btn-sm btn-primary">Add</a></span>
                            <?php } ?>
                          </header>
                          <div class="panel-body">
                            <div class="table-responsive">
                              <table class="table table-striped b-t b-light" data-ride="datatables">
                                <thead>
                                  <tr>
                                    <th>NAME</th>
                                    <th>STATUS</th>
                                    <th>ACTION</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?PHP  
                                $i=1;
                                foreach ($arrTerritory as $key => $value) {
                                  
                                  $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                                ?> 
                              <tr> 
                                  <td class="tbdata"> <?php echo $value['name']?> </td>
                                  <td class="tbdata"> <?php echo $status?> </td>
                                  <td> <button type="button" onclick="window.location='territory-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">See Detail</button>
                                  <?php if($loggdUType == "Admin") { ?>
                                    <button type="button" onclick="window.location='edit-territory.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Edit</button> 
                                    <?php } ?>
                                  </td>
                              </tr>
                              <?php
                                $i++;
                              }
                              ?>
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </section>    
                  </div>  
              </div>
            </section>
        </section>
    </section>
<?php    
	include("footer.php"); 
?> 