<?php
    ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

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
    /* Para los usuarios Business Unit Manager */
    $join = "";
     if($_SESSION['USER_ROLE']=="Business Unit Manager"){
     $join.=" inner join acces_category on acces_category.id_user = users.id
              inner join category_business on category_business.id = acces_category.id_category";
     $access = optener_category_roll_by_id($_SESSION['USER_ID']);
     $in = "";
     $nr = 1;
     foreach($access as $key => $value){
              if($nr>1){
              $in .= $value['id_category'].", ";
              }else{
                $in .= $value['id_category'];
              }
              $nr++;
            }
     $where.= " and acces_category.id_category in($in)";
     }

     $pname="";
     $territory="";
     if(isset($_POST['name']) && $_POST['name'] != "")
     {
        $where.=" and  users.Name LIKE '%".$_POST['name']."%'";
        $name = $_POST['name'];
     }
     if(isset($_POST['lname']) && $_POST['lname'] != "")
     {
        $where.=" and  users.Last_name LIKE '%".$_POST['lname']."%'";
        $lname = $_POST['lname'];
     }
     if(isset($_POST['user']) && $_POST['user'] != "")
     {
        $where.=" and  users.user LIKE '%".$_POST['user']."%'";
        $user = $_POST['user'];
     }
     if(isset($_POST['location']) && $_POST['location'] != "")
     {
        $where.=" and  CONCAT(country.name , region.name , territory.name ) LIKE '%".$_POST['location']."%'";
        $location = $_POST['location'];
     }
     if($loggdUType == "Region Master")
     {
      $where.=" and location_user.id_region = ".$loggdURegion." and type_user.name_type_user NOT IN ('Admin')";
     }


      $arrUser = GetRecords("SELECT users.*, type_user.name_type_user, CONCAT(country.name , ' / ',
                                    region.name, ' / ' , territory.name ) AS LOCATION
                             from users
                             inner join type_user on type_user.id_user = users.id_roll_user
                             inner join location_user on location_user.id_user = users.id
                             inner join territory on territory.id = location_user.id_territory
                             inner join region on region.id = territory.id_region
                             inner join country on country.id = region.id_country
                             $join

                             $where
                             order by Name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Lista de Usuarios</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($name)){ echo $name;}?>" name="name" placeholder="Nombre">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($lname)){ echo $lname;}?>" name="lname" placeholder="Apellido">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($user)){ echo $user;}?>" name="user" placeholder="Usuario">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($location)){ echo $location;}?>" name="location" placeholder="Localidad">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>NOMBRE</th>
                              <th>APELLIDO</th>
                              <th>EMAIL</th>
                              <th>TIPO DE USUARIO</th>
                              <th>LOCALIDAD</th>
                              <th>ESTATUS</th>
                              <th>ACCIÓN</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {

                              $status = ($value['stat'] == 1) ? 'Activo' : 'Inactivo';
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['Name']?> </td>
                              <td class="tbdata"> <?php echo $value['Last_name']?> </td>
                              <td class="tbdata"> <?php echo $value['Email']?> </td>
                              <td class="tbdata"> <?php echo $value['name_type_user']?> </td>
                              <td class="tbdata"> <?php echo $value['LOCATION']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <td> <button type="button" onclick="window.location='user-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">VER DETALLES</button>
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
