<?php
    ob_start();
    $activityclass="class='active'";
    $activitylistclass="class='active'";

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
    $where2 = "where (1=1)";

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
     $where2.= " and acces_category.id_category in($in)";
     }
     /* Fin Business Unit Manager */

     if(isset($_POST['id_usuario']) && $_POST['id_usuario'] != "")
     {
        $where.=" and log_activity.id_user = '".$_POST['id_usuario']."'";
        $where2.=" and note_detail.id_user = '".$_POST['id_usuario']."'";
        $id_usuairo = $_POST['id_usuario'];

     }
     if(isset($_POST['datefrom']) && $_POST['datefrom'] != "")
     {
       $where.=" and log_activity.date_create >= '".$_POST['datefrom']."'";
       $where2.=" and note_detail.create_date >= '".$_POST['datefrom']."'";
       $datefrom = $_POST['datefrom'];
     }
     if(isset($_POST['dateto']) && $_POST['dateto'] != "")
     {
       $where.=" and log_activity.date_create <= '".$_POST['dateto'].' 23:59:59'."'";
       $where2.=" and note_detail.create_date <= '".$_POST['dateto'].' 23:59:59'."'";
       $dateto = $_POST['dateto'];
     }

/*

select
log_activity.description,
log_activity.date_create,
module_activity.name_module,
activity_action.action_name,
users.Name,
users.Last_name
from log_activity inner join module_activity on module_activity.id = log_activity.id_module
                  inner join activity_action on activity_action.id = log_activity.id_action
                  inner join users on users.id = log_activity.id_user
$where
order by date_create

 */

      $arrUser = GetRecords("select
                              log_activity.description,
                              log_activity.date_create,
                              module_activity.name_module,
                              activity_action.action_name,
                              users.Name,
                              users.Last_name
                              from log_activity inner join module_activity on module_activity.id = log_activity.id_module
                              				  inner join activity_action on activity_action.id = log_activity.id_action
                              				  inner join users on users.id = log_activity.id_user
                                        $join
                                        $where

                              union

                              select
                              note_detail.note as description,
                              note_detail.create_date as date_create,
                              type_note.Name_type_note as name_module,
                              note_detail.note_subject as action_name,
                              users.Name,
                              users.Last_name
                              from
                              note_detail inner join type_note on type_note.id = note_detail.note_type
                                          inner join users on users.id = note_detail.id_user
                                          $join
                                          $where2
                              order by 2 desc");?>

	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Reporte de Actividad</span>
                </header>
                <div class="panel-body">
                    <?php ?>
                    <form method="post" action="" novalidate>
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group" style="width:100%;">
                            <select class="chosen-select form-control" name="id_usuario" required="required" style="width:100%;" >
            	                        <option value="">Usuario</option>
            	                        <?PHP
                                      if($_SESSION['USER_ROLE']!="Business Unit Manager"){
            	                        $arrKindMeetings = GetRecords("Select * from users WHERE stat = 1");
            	                        foreach ($arrKindMeetings as $key => $value) {
            	                          $kinId = $value['id'];
            	                          $kinDesc = $value['Name']." ".$value['Last_name'];
            	                          $selType = (!empty($id_usuairo) && $id_usuairo == $kinId) ? 'selected' : '';?>
            	                        <option value="<?php echo $kinId?>" <?php echo $selType?>><?php echo utf8_encode($kinDesc)?></option>
            	                        <?php } ?>
                                      <?php }else{
                                      $arrKindMeetings = GetRecords("Select * from users where stat = 1");
            	                        foreach ($arrKindMeetings as $key => $value) {
            	                          $kinId = $value['id'];
            	                          $kinDesc = $value['Name']." ".$value['Last_name'];
            	                          $selType = (!empty($id_usuairo) && $id_usuairo == $kinId) ? 'selected' : '';?>
            	                        <option value="<?php echo $kinId?>" <?php echo $selType?>><?php echo utf8_encode($kinDesc)?></option>
            	                        <?php }
                                      } ?>
            	                </select>
                          </div>
                        </div>
                        <div class="col-sm-6 m-b-xs">
                          <label class="col-lg-1 control-label">Desde</label>
                          <div class="col-lg-2 m-b-xs">
                            <div class="input-group">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker" name="datefrom" size="16" readonly="" data-required="true" type="text" value="<?php if(isset($datefrom)){ echo $datefrom;}?>" data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <label class="col-lg-1 control-label">Hasta</label>
                          <div class="col-lg-2 m-b-xs">
                            <div class="input-group">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker1" name="dateto" size="16" readonly="" data-required="true" type="text" value="<?php if(isset($dateto)){ echo $dateto;}?>" data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                        </div>
                        <div class="col-sm-1 m-b-xs">
                          <div class="input-group">
                          </div>
                        </div>
                      </div>
                    </form>
                    <?php /* Filtros */ ?>
                    <?php $where3 = "where (1=1)";
                          $where_oportunidad = " ";
                          $where_notas = " ";
                          $where_propuesta = " ";
                             if(isset($_POST['id_usuario']) && $_POST['id_usuario'] != "")
                             {
                                $where_oportunidad.=" and id_user_register LIKE '%".$_POST['id_usuario']."%'";
                                $where_notas.=" and note_detail.id_user LIKE '%".$_POST['id_usuario']."%'";
                                $where_propuesta.=" and proposal.id_user LIKE '%".$_POST['id_usuario']."%'";

                                $id_usuairo = $_POST['id_usuario'];

                                $usuario = GetRecords("Select name, Last_name from users where id = '".$id_usuario."'");
                                foreach ($usuario as $key => $value) {
                                        $nombre_usuario=$value["name"]." ".$value["Last_name"];
                                }
                             }
                             if(isset($_POST['datefrom']) && $_POST['datefrom'] != "")
                             {
                               $where_oportunidad.=" and created_date >= '".$_POST['datefrom']."'";
                               $where_notas.=" and note_detail.create_date >= '".$_POST['datefrom']."'";
                               $where_propuesta.=" and proposal.create_date >= '".$_POST['datefrom']."'";

                               $datefrom = $_POST['datefrom'];
                             }
                             if(isset($_POST['dateto']) && $_POST['dateto'] != "")
                             {
                               $where_oportunidad.=" and created_date <= '".$_POST['dateto']." 23:59:59"."'";
                               $where_notas.=" and note_detail.create_date <= '".$_POST['dateto']." 23:59:59"."'";
                               $where_propuesta.=" and proposal.create_date <= '".$_POST['dateto']." 23:59:59"."'";

                               $dateto = $_POST['dateto'];
                             } ?>
                    <?php /* Fin del Filtro */ ?>

                    <?php if($_SESSION['USER_ROLE']!="Business Unit Manager"){ ?>
                    <?php $oportunidad = GetRecords("select
                                                      count(*) as todo,
                                                      (select count(*) from opportunity where stat = 1 $where_oportunidad) as activos,
                                                      (select count(*) from opportunity where stat = 2 $where_oportunidad) as pendientes,
                                                      (select count(*) from opportunity where stat = 3 $where_oportunidad) as ganadas,
                                                      (select count(*) from opportunity where stat = 4 $where_oportunidad) as perdidas
                                                      from opportunity
                                                      $where3$where_oportunidad");
                          }else{

                         $join_log =" inner join log_activity on log_activity.id_user = users.id ";

                         $oportunidad = GetRecords("select
                                                      count(*) as todo,
                                                      (select count(*) from opportunity inner join users on users.id = opportunity.id_user_register $join_log$join $where and opportunity.stat = 1 $where_oportunidad) as activos,
                                                      (select count(*) from opportunity inner join users on users.id = opportunity.id_user_register $join_log$join $where and opportunity.stat = 2 $where_oportunidad) as pendientes,
                                                      (select count(*) from opportunity inner join users on users.id = opportunity.id_user_register $join_log$join $where and opportunity.stat = 3 $where_oportunidad) as ganadas,
                                                      (select count(*) from opportunity inner join users on users.id = opportunity.id_user_register $join_log$join $where and opportunity.stat = 4 $where_oportunidad) as perdidas
                                                      from opportunity inner join users on users.id = opportunity.id_user_register $join_log$join $where
                                                      $where_oportunidad");
                          }

                    foreach ($oportunidad as $key => $value) {
                                $todo = $value['todo'];
                                $activos = $value['activos'];
                                $pendientes = $value['pendientes'];
                                $ganadas = $value['ganadas'];
                                $perdidas = $value['perdidas'];} ?>
                    <div class="row wrapper">
                      <div class="col-sm-2 m-b-xs">
                        <div class="input-group" style="width:100%;">
                          <b>Usuario:
                            <?php if(isset($_POST['id_usuario']) && $_POST['id_usuario'] != ""){ echo $nombre_usuario; }else{ echo "Todos"; } ?>
                          </b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b>Fecha Inicial: <?php if(isset($_POST['datefrom'])){ echo $datefrom; }else{ echo "-"; } ?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b>Fecha Final: <?php if(isset($_POST['dateto'])){ echo $dateto; }else{ echo "-"; } ?></b>
                        </div>
                      </div>
                      <div class="col-sm-2 m-b-xs">
                        <div class="input-group" style="width:100%;">
                          <b>Oportunidades Creadas: <?php echo $todo?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b style="color:green">Oportunidades Ganadas: <?php echo $ganadas?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b style="color:red">Oportunidades Perdidas: <?php echo $perdidas?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b style="color:#FFA500">Oportunidades Pendientes: <?php echo $pendientes?></b>
                        </div>
                      </div>
                      <?php if($_SESSION['USER_ROLE']!="Business Unit Manager"){ ?>
                      <?php $notas_estadisticas = GetRecords("select
                                                              count(*) as todo,
                                                              (select count(*) from note_detail where note_type in(1, 2, 4) $where_notas) as nota,
                                                              (select count(*) from note_detail where note_type = 6 $where_notas) as proximo_paso,
                                                              (select count(*) from note_detail where note_type = 5 $where_notas) as nota_llamada
                                                              from note_detail
                                                              $where3$where_notas;");
                                                            }else{

                            $join_log =" inner join log_activity on log_activity.id_user = users.id ";
                             $notas_estadisticas = GetRecords("select
                                                                count(*) as todo,
                                                                (select count(*) from note_detail inner join users on users.id = note_detail.id_user $join_log$join $where and note_detail.note_type in(1, 2, 4) $where_notas) as nota,
                                                                (select count(*) from note_detail inner join users on users.id = note_detail.id_user $join_log$join $where and note_detail.note_type = 6 $where_notas) as proximo_paso,
                                                                (select count(*) from note_detail inner join users on users.id = note_detail.id_user $join_log$join $where and note_detail.note_type = 5 $where_notas) as nota_llamada
                                                                from note_detail
                                                                $where3$where_notas;");

                                                            }

                      foreach ($notas_estadisticas as $key => $value) {
                                  $notas = $value['nota'];
                                  $proximo_paso = $value['proximo_paso'];
                                  $nota_llamada = $value['nota_llamada']; } ?>
                      <div class="col-sm-2 m-b-xs">
                        <div class="input-group" style="width:100%;">
                          <b>Notas: <?php echo $notas?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b>Próximos Pasos: <?php echo $proximo_paso?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b>Notas de llamadas: <?php echo $nota_llamada?></b>
                        </div>
                      </div>
                      <?php if($_SESSION['USER_ROLE']!="Business Unit Manager"){ ?>

                      <?php $propuesta = GetRecords("select
                                                      count(*) as todo,
                                                      (select count(*) from proposal where status = 1 $where_propuesta) as pendientes,
                                                      (select count(*) from proposal where status = 2 $where_propuesta) as enviadas,
                                                      (select count(*) from proposal where status = 3 $where_propuesta) as rechazadas
                                                      from proposal
                                                      $where3$where_propuesta");

                                                    }else{

                            $join_log =" inner join log_activity on log_activity.id_user = users.id ";
                            $propuesta = GetRecords("select
                                                        count(*) as todo,
                                                        (select count(*) from proposal inner join users on users.id = proposal.id_user $join_log$join $where and proposal.status = 1 $where_propuesta) as pendientes,
                                                        (select count(*) from proposal inner join users on users.id = proposal.id_user $join_log$join $where and proposal.status = 2 $where_propuesta) as enviadas,
                                                        (select count(*) from proposal inner join users on users.id = proposal.id_user $join_log$join $where and proposal.status = 3 $where_propuesta) as rechazadas
                                                        from proposal
                                                        $where3$where_propuesta");

                                                      }


                      foreach ($propuesta as $key => $value) {
                                  $pendientes = $value['pendientes'];
                                  $enviadas = $value['enviadas'];
                                  $rechazadas = $value['rechazadas'];} ?>
                      <div class="col-sm-2 m-b-xs">
                        <div class="input-group" style="width:100%;">
                          <b style="color:#6495ED">Propuestas Enviadas: <?php echo $enviadas ?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b>Propuestas Rechazadas: <?php echo $rechazadas ?></b>
                        </div>
                        <div class="input-group" style="width:100%;">
                          <b>Propuestas pendientes: <?php echo $pendientes ?></b>
                        </div>
                      </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables_activity" id="example">
                          <thead>
                            <tr>
                              <th>DESCRIPCIÓN</th>
                              <th style="width:150px;">MODULO</th>
                              <th>ACCIÓN</th>
                              <th style="width:150px;">FECHA Y HORA</th>
                              <th style="width:150px;">USUARIO</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) { ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['description']?> </td>
                              <td class="" style="width:150px;"> <?php echo utf8_encode($value['name_module'])?> </td>
                              <td class="tbdata"> <?php echo utf8_encode($value['action_name'])?> </td>
                              <td class="" style="width:150px;"> <?php echo $value['date_create']?> </td>
                              <td class="" style="width:150px;"> <?php echo utf8_encode($value['Name'].' '.$value['Last_name'])?> </td>
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
