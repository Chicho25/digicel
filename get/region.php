<?php 

    ob_start();
    $countryclass="class='active'";
    $editRegclass="class='active'";
    
    include("include/config.php"); 
    include("include/defs.php"); 
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    
    include("header.php"); 

    if(!isset($_SESSION['USER_ID'])|| $loggdUType == "Business Manager") 
     {
          header("Location: index.php");
          exit;
     }

      $where = "where (1=1)";
      if($loggdUType == "Region Master")
      {
        $where.=" and region.id = ".$loggdURegion;
      }
      $regname = "";
      if(isset($_POST['cname']) && $_POST['cname'] != "")
      {  
        $where.=" and  region.name LIKE '%".$_POST['cname']."%'";
        $regname = $_POST['cname'];
      }
      $arrUser = GetRecords("SELECT region.*, country.name as Country from region
                             inner join country on country.id = region.id_country 
                             $where 
                             order by region.name");
     
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">List Region</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $regname?>" name="cname" placeholder="Region Name">
                            <span class="input-group-btn"><button class="btn btn-sm btn-default">Search</button></span>
                          </div>  
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light">
                          <thead>
                            <tr>
                              <th>NAME COUNTRY</th>
                              <th>NAME REGION</th>
                              <th>STATUS</th>
                              <th>ACTION</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP  
                            $i=1;
                            foreach ($arrUser as $key => $value) {
                              
                              $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                            ?> 
                          <tr> 
                              <td class="tbdata"> <?php echo $value['Country']?> </td>
                              <td class="tbdata"> <?php echo $value['name']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='region-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">See Detail</button> 
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
            </section>
        </section>
    </section>
    
<?php    
	include("footer.php"); 
?> 