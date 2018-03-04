<?php 

    ob_start();
    $countryclass="class='active'";
    $editTerclass="class='active'";
    
    include("include/config.php"); 
    include("include/defs.php"); 
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    
    include("header.php"); 

    if(!isset($_SESSION['USER_ID']) || $loggdUType == "Business Manager") 
     {
          header("Location: index.php");
          exit;
     }

      $where = "where (1=1)";
      if($loggdUType == "Region Master")
      {
        $where.=" and id_region = ".$loggdURegion;
      }
      $regname = "";
      if(isset($_POST['cname']) && $_POST['cname'] != "")
      {
        $where.=" and  territory.name LIKE '%".$_POST['cname']."%'";
        $regname = $_POST['cname'];
      }
      $arrUser = GetRecords("SELECT territory.*, region.name as RName, country.name as Country from territory 
                              inner join region on region.id = territory.id_region 
                              inner join country on country.id = region.id_country 
                              $where
                             order by territory.name");
     
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">List Territory</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $regname?>" name="cname" placeholder="Territory Name">
                            <span class="input-group-btn"><button class="btn btn-sm btn-default">Search</button></span>
                          </div>  
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light">
                          <thead>
                            <tr>
                              <th>NAME Country</th>
                              <th>NAME Region</th>
                              <th>NAME Territory</th>
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
                              <td class="tbdata"> <?php echo $value['RName']?> </td>
                              <td class="tbdata"> <?php echo $value['name']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='territory-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">See Detail</button> 
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