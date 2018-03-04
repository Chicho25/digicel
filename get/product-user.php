<?php 

    //ob_start();
    $productclass="class='active'";
    $editProdUserclass="class='active'";
    
    include("include/config.php"); 
    include("include/defs.php"); 
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    $loggdUTerritory = current_user_territory();
    include("header.php"); 

    if(!isset($_SESSION['USER_ID'])  || $loggdUType == "Business Manager") 
     {
          header("Location: index.php");
          exit;
     }
    
      $arrUser = GetRecords("SELECT product_by_user.id, product_by_user.stat, product.name as pname, users.Name as tename from product_by_user
                             inner join product on product.id =   product_by_user.id_product
                             inner join users on users.id =   product_by_user.id_user
                             order by product.name");
     
?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">List Product User</span>
                </header>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light">
                          <thead>
                            <tr>
                              <th>PRODUCT</th>
                              <th>User</th>
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
                              <td class="tbdata"> <?php echo $value['pname']?> </td>
                              <td class="tbdata"> <?php echo $value['tename']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='product-user-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">See Detail</button> 
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
    <script type="text/javascript">
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img').show().attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
  </script>
<?php    
	include("footer.php"); 
?> 