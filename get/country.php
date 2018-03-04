<?php

    ob_start();
    $countryclass="class='active'";
    $editCntclass="class='active'";

    include("include/config.php");
    include("include/defs.php");


    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    $loggdUCountry = current_user_country();
    include("header.php");

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

      $where = "where (1=1)";
      if($loggdUType == "Region Master")
      {
        $where.=" and id = ".$loggdUCountry;
      }
      $regname = "";
      if(isset($_POST['cname']) && $_POST['cname'] != "")
      {
        $where.=" and  name LIKE '%".$_POST['cname']."%'";
        $regname = $_POST['cname'];
      }
      $arrUser = GetRecords("SELECT * from country
                              $where
                             order by name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Lista de Paises</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" name="cname" value="<?php echo $regname?>" placeholder="Nombre del país">
                            <span class="input-group-btn"><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>Nombre</th>
                              <th>Estatus</th>
                              <th>ACCION</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {

                              $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['name']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='country-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">Ver Detalle</button>
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
