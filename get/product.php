<?php

    ob_start();
    $productclass="class='active'";
    $editProdclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    $loggdUTerritory = current_user_territory();

    include("header.php");

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

     $where = "where (1=1)";
     if($loggdUType != "Admin")
     {
       $where.=" and product.id IN (SELECT id_product FROM product_by_territory WHERE id_territory = ".$loggdUTerritory.")";
     }
     $cname="";
     $category="";
     $code="";
     if(isset($_POST['cname']) && $_POST['cname'] != "")
      {
        $where.=" and  product.name LIKE '%".$_POST['cname']."%'";
        $cname = $_POST['cname'];
      }
      if(isset($_POST['code']) && $_POST['code'] != "")
      {
        $where.=" and  product.code LIKE '%".$_POST['code']."%'";
        $code = $_POST['code'];
      }
      if(isset($_POST['category']) && $_POST['category'] != "")
      {
        $where.=" and  category.name LIKE '%".$_POST['category']."%'";
        $category = $_POST['category'];
      }


      $arrUser = GetRecords("SELECT product.*, category.name as catname from product
                                inner join category on category.id = product.id_category
                              $where
                             order by name");
      $nTotalRecords = count($arrUser);

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Lista de Productos</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $cname?>" name="cname" placeholder="Nombre del Producto">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $code?>" name="code" placeholder="Código">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $category?>" name="category" placeholder="Categoria">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light"  data-ride="datatables">
                          <thead>
                            <tr>
                              <th>CATEGORIA</th>
                              <th>NOMBRE DEL PRODUCTO</th>
                              <th>CODIGO</th>
                              <th>REQUIERE ENTRENAMIENTO?</th>
                              <th>ESTATUS</th>
                              <th>ACCIÓN</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {

                              $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                              $training = ($value['training'] == 1) ? 'Yes' : 'No';
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['catname']?> </td>
                              <td class="tbdata"> <?php echo $value['name']?> </td>
                              <td class="tbdata"> <?php echo $value['code']?> </td>
                              <td class="tbdata"> <?php echo $training?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='product-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver Detalles</button>
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
