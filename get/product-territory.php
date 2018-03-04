<?php

    ob_start();
    $productclass="class='active'";
    $editProdTerrclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    $loggdUTerritory = current_user_territory();
    include("header.php");

    if(!isset($_SESSION['USER_ID']) || $loggdUType == "Business Manager")
     {
          header("Location: index.php");
          exit;
     }

     $where = "where (1=1)";
     $pname="";
     $territory="";
     if(isset($_POST['pname']) && $_POST['pname'] != "")
      {
        $where.=" and  product.name LIKE '%".$_POST['pname']."%'";
        $pname = $_POST['pname'];
      }
      if(isset($_POST['territory']) && $_POST['territory'] != "")
      {
        $where.=" and  territory.name LIKE '%".$_POST['territory']."%'";
        $territory = $_POST['territory'];
      }
      $arrUser = GetRecords("SELECT product_by_territory.id, product_by_territory.stat, product.name as pname, territory.name as tename from product_by_territory
                             inner join product on product.id =   product_by_territory.id_product
                             inner join territory on territory.id =   product_by_territory.id_territory
                             $where
                             order by product.name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Lista Productos por territorio</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $pname?>" name="pname" placeholder="Nombre de Productos">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $territory?>" name="territory" placeholder="Nombre del Territorio">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>PRODUCTOS</th>
                              <th>TERRITORIO</th>
                              <th>ESTATUS</th>
                              <th>ACCIÓN</th>
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
                              <td> <button type="button" onclick="window.location='product-territory-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver Detalles</button>
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
