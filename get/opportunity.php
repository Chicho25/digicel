<?php

    ob_start();
    $opportunityclass="class='active'";
    $editOpporclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

/* Actualizar proximo paso */

if(isset($_POST['submitUsuario'])){

  $sql_oportunidad = GetRecords("SELECT name, id_contact FROM opportunity WHERE id =".$_POST['id_opportunity']);
              foreach ($sql_oportunidad as $key => $l) {
                    $nombre_oportunidad=$l['name'];
                    $id_contacto = $l['id_contact'];
              }

  $update_next_step = MySQLQuery("update opportunity set id_user_register = '".$_POST['id_usuario']."'
                                                        where
                                                        id = '".$_POST['id_opportunity']."'");

  $sql_usuario = GetRecords("SELECT
                              Name,
                              Last_name,
                              Email
                             FROM
                              users
                             WHERE
                              id =".$_POST['id_usuario']);

              foreach ($sql_usuario as $key => $l2) {
                    $nombre=$l2['Name'].' '.$l2['Last_name'];
                    $email = $l2['Email'];
              }

     /* Email */

     $actual_link = "http://".$_SERVER['HTTP_HOST']."/opportunity.php";
     $admin_email = "dev@dchain.com";
     $subject = "dChain GET: Oportunidad Asignada";
     $headers = "MIME-Version: 1.0\\r\ ";
     $headers .= "Content-type: text/html; charset=iso-8859-1\\r\ ";
     $headers .= "From: ".$admin_email;
     $comment = " Hola ".$nombre.",\n\n se le ha asignado la Oportunidad: ".$nombre_oportunidad.". Ingrese al sistema para mas detalles . " . $actual_link . " ";

     include("mailjet/src/Mailjet/php-mailjet-v3-simple.class.php");

     $apiKey = '16ecb7873995588027a5cef50f59b719';
     $secretKey = '06e6276f1fe3249498c103b601869f58';

     $mj = new Mailjet($apiKey, $secretKey);
     if (isset($_POST['submitUsuario'])) {

         function sendEmail($email, $subject, $comment, $admin_email) {
             // Create a new Object
             $mj = new Mailjet();
             $params = array(
                 "method" => "POST",
                 "from" => "{$admin_email}",
                 "to" => "{$email}",
                 "subject" => "{$subject}",
                 "text" => "{$comment}"
             );
             $result = $mj->sendEmail($params);
             if ($mj->_response_code == 200) {
                 //echo "success - email sent";
                 print '<script type="text/javascript">';
                 print 'alert("email successfully sent!")';
                 print '</script>';
             } elseif ($mj->_response_code == 400) {
                 //echo "error - " . $mj->_response_code;
                 print '<script type="text/javascript">';
                 print 'alert("Bad Request! One or more arguments are missing or maybe mispelling.")';
                 print '</script>';
             } elseif ($mj->_response_code == 401) {
                 //echo "error - " . $mj->_response_code;
                 print '<script type="text/javascript">';
                 print 'alert("Unauthorized! You have specified an incorrect ApiKey or username/password couple.")';
                 print '</script>';
             } elseif ($mj->_response_code == 404) {
                 //echo "error - " . $mj->_response_code;
                 print '<script type="text/javascript">';
                 print 'alert("Not Found! The method your are trying to reach don\'t exists.")';
                 print '</script>';
             } elseif ($mj->_response_code == 405) {
                 //echo "error - " . $mj->_response_code;
                 print '<script type="text/javascript">';
                 print 'alert("Method Not Allowed! You made a POST request instead of GET, or the reverse.")';
                 print '</script>';
             } else {
                 print '<script type="text/javascript">';
                 print 'alert(" Internal Server Error! Status returned when an unknow error occurs")';
                 print '</script>';
             }

             return $result;
         }

         sendEmail($email, $subject, $comment, $admin_email);
     }

     /* Fin Email */

     /* Log de Actividad */
       $mensaje = "Se ha cambiado al Business Manager en la oportunidad '".$nombre_oportunidad."'";
       log_actividad(5, 2, $_SESSION['USER_ID'], $mensaje);
     /* Fin de log de actividad */

}

if(isset($_POST['submitNextstep'])){

  $sql_oportunidad = GetRecords("SELECT name FROM opportunity WHERE id =".$_POST['id']);
              foreach ($sql_oportunidad as $key => $l) {
                    $nombre_oportunidad=$l['name'];
              }

  $update_next_step = MySQLQuery("update opportunity set nextstep_date = '".$_POST['nextstepdate']." ".$_POST['hour']."',
                                                         nextstep = '".$_POST['nextstep']."'
                                                         where
                                                         id = '".$_POST['id']."'");

     $arrUser = GetRecords("SELECT * FROM next_step WHERE id = '".$_POST['nextstep']."'");

        foreach($arrUser as $ns => $nextstep){
          $nombre_ns = $nextstep['detail'];
        }

        $log_next_step = "Proximo Paso: '".$nombre_ns."'";

        create_log_next_step($_POST['nextid'], $log_next_step, $log_next_step, 6);

 /* Log de Actividad */
   $mensaje = "Se ha modificado el proximo paso en la oportunidad '".$nombre_oportunidad."'";
   log_actividad(7, 2, $_SESSION['USER_ID'], $mensaje);
 /* Fin de log de actividad */

}

if(isset($_POST['etapaNote'])){

  $sql_oportunidad = GetRecords("SELECT name FROM opportunity WHERE id =".$_POST['id_opportunity']);
              foreach ($sql_oportunidad as $key => $l) {
                    $nombre_oportunidad=$l['name'];
              }

  $update_source = MySQLQuery("update opportunity set id_stage = '".$_POST['etapaNote']."'
                                                         where
                                                         id = '".$_POST['id_opportunity']."'");

/* Log de Actividad */
 $mensaje = "Se ha modificado la etapa en la oportunidad: '".$nombre_oportunidad."'";
 log_actividad(5, 2, $_SESSION['USER_ID'], $mensaje);
/* Fin de log de actividad */

}

/* Inser de la nota Oportunidad */

    	$message="";

    	if(isset($_POST['submitNote']))
    	{

    		    $notetype = $_POST['notetype'];
            $note = $_POST['note'];
            $noteid = $_POST['noteid'];
            $userrefid = $_POST['master_note'];
            $subject = $_POST['subject'];

            if($noteid != "")
            {
            	$arrVal = array(
                              "note_type" => $notetype,
                              "note" => $note,
                              "note_subject" => $subject,
                              "note_ref_id" => $userrefid,
                              "create_date" => date("Y-m-d H:i:s"),
                              "id_user" => $_SESSION['USER_ID']
                             );
            	$nId = InsertRec("note_detail", $arrVal);
            }
            else
            {
            	$nId = $noteid;
            	$arrVal = array(
                              "note_type" => $notetype,
                              "note" => $note,
                              "note_subject" => $subject,
                              "id_user" => $_SESSION['USER_ID']
                             );

            	UpdateRec("note_detail", "id = ".$nId, $arrVal);
            }

            if($nId == 0)
            {
            	$message = 'La nota no fue creada!';
            }
            else
            {
            	if(isset($_FILES['attached']) && $_FILES['attached']['tmp_name'] != "")
    	          {
    	              $target_dir = "notes/";
    	              $target_file = $target_dir . basename($_FILES["attached"]["name"]);
    	              $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    	              $filename = $target_dir . $nId.".".$imageFileType;
    	              $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
    	              if (move_uploaded_file($_FILES["attached"]["tmp_name"], $filename))
    	              {
    	                  makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

    	                  UpdateRec("note_detail", "id = ".$nId, array("attached" => $filenameThumb, "filename" => basename($_FILES["attached"]["name"])));
    	              }
    	          }
            	$message = 'Nota creada con éxito!';
            }

            //echo '<script>window.location="'.$_SERVER['REQUEST_URI'].'"</script>';
    	}

/* Fin de la nota oportunidad */

    if(!isset($_SESSION['USER_ID']))
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
       /* Fin Business Unit Manager */

      if($loggdUType != "Admin" && $loggdUType != "Business Unit Manager")
      {
        $where.=" and opportunity.id_user_register = ".$_SESSION['USER_ID'];
      }
      else
      {
       $bizname="";
       $stage="";
       $contactname="";
       if(isset($_POST['bizname']) && $_POST['bizname'] != "")
        {
          $where.=" and  opportunity.id_user_register =  ".$_POST['bizname'];
          $bizname = $_POST['bizname'];
        }
        if(isset($_POST['contactname']) && $_POST['contactname'] != "")
        {
          $where.=" and  contact.Name LIKE '%".$_POST['contactname']."%'";
          $contactname = $_POST['contactname'];
        }
        if(isset($_POST['stage']) && $_POST['stage'] != "")
        {
          $where.=" and  stage.Name_stage LIKE '%".$_POST['stage']."%'";
          $stage = $_POST['stage'];
        }
        if(isset($_POST['datefrom']) && $_POST['datefrom'] != "")
        {
          $where.=" and  opportunity.closing_date >= '".$_POST['datefrom']."'";
          $datefrom = $_POST['datefrom'];
        }
        if(isset($_POST['dateto']) && $_POST['dateto'] != "")
        {
          $where.=" and  opportunity.closing_date <= '".$_POST['dateto']."'";
          $dateto = $_POST['dateto'];
        }

      }
      $arrUser = GetRecords("SELECT
                              opportunity.*,
                              contact.Name as contactname,
                              business.Name as bizname,
                              stage.Name_stage  as stagename,
                              stage.id as idstage,
                              (select Name_stage from stage where opportunity.id_stage = id) as nombre_etapa,
                              next_step.detail as name_next_step,
                              concat(users.Name,' ',users.Last_name) as nombre_vendedor
                              from opportunity
                              inner join contact on contact.id  = opportunity.id_contact
                              inner join business on business.id  = contact.id_business
                              inner join stage on stage.id  = opportunity.id_stage
                              inner join next_step on next_step.id = opportunity.nextstep
                              inner join users on users.id = opportunity.id_user_register
                              $join
                              $where and opportunity.stat in (1,2)
                              order by opportunity.name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                  <?php if(isset($update_next_step) || isset($nId)){ ?>

                  <div class="alert alert-success">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Realizado con éxito</strong>
                  </div>

                  <?php } ?>
                          <span class="h4">Oportunidades Pendientes</span>

                </header>
                <div class="panel-body">
                    <?PHP /* if($loggdUType == "Admin") :  ?>
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2">
                            <select class="chosen-select form-control"  name="bizname" data-required="true">
                                <option value="">----------------</option>
                                <?PHP

                                $arrKindMeetings = GetRecords("Select users.* from users
                                                               inner join type_user on type_user.id_user = users.id_roll_user
                                                               where users.stat = 1 and type_user.name_type_user = 'Business Manager'");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name'] ." ". $value['Last_name'];
                                  $selRoll = (isset($_POST['bizname']) && $_POST['bizname'] == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $stage?>" name="stage" placeholder="Etapa">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php echo $contactname?>" name="contactname" placeholder="Nombre de contacto">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                      <div class="row wrapper">
                        <label class="col-lg-2 control-label font-bold">Rango de fecha Desde</label>
                        <div class="col-lg-2 m-b-xs">
                          <div class="input-group">
                            <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker" name="datefrom" size="16" readonly="" data-required="true" type="text" value="<?php echo $datefrom?>"  data-date-format="yyyy-mm-dd" >
                          </div>
                        </div>
                        <label class="col-lg-1 control-label font-bold">Hasta</label>
                        <div class="col-lg-2 m-b-xs">
                          <div class="input-group">
                            <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker1" name="dateto" size="16" readonly="" data-required="true" type="text" value="<?php echo $dateto?>"  data-date-format="yyyy-mm-dd" >
                          </div>
                        </div>
                      </div>
                    </form>
                  <?php endif; */ ?>
                    <div class="table-responsive">
                        <table class="table table-striped m-b-none" data-ride="datatables">
                          <thead>
                            <tr>
                              <!--<th>Nombre del Contacto</th>-->
                              <th>Nombre de la Oportunidad</th>
                              <?PHP if($loggdUType == "Admin" || $loggdUType == "Business Unit Manager") :  ?>
                              <th>Business Manager</th>
                              <?php endif; ?>
                              <th>Empresa</th>
                              <th>Monto </th>
                              <th>Proximo Paso</th>
                              <th>Fecha de Próximo Paso</th>
                              <!--<th>Status</th>-->
                              <th>Etapa</th>
                              <th>Acciones</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {

                              $status = ($value['stat'] == 1) ? 'Active' : 'In Active';
                            ?>
                          <tr>
                              <?php /* ?><td class="tbdata"> <a href='opportunity-view.php?id=<?php echo $value['id']?>'><?php echo $value['contactname']?></a> </td><?php */ ?>
                              <td class="tbdata"><b><?php echo $value['name']?></b></td>
                              <?PHP if($loggdUType == "Admin" || $loggdUType == "Business Unit Manager") :  ?>
                              <td class="tbdata"><?php echo $value['nombre_vendedor']?></td>
                              <?php endif; ?>
                              <td class="tbdata"> <?php echo $value['bizname']?> </td>
                              <td class="tbdata"> <?php echo $value['estimated_amount']?> </td>
                              <td class="tbdata"> <?php /*echo date("M d, Y", strtotime($value['created_date'])) */?> <?php echo utf8_encode($value['name_next_step'])?> </td>
                              <td class="tbdata"> <?php echo $value['nextstep_date']?> </td>
                              <?php /* ?><td class="tbdata"> <?php echo $status?> </td> */?>
                              <td class="tbdata"> <?php echo utf8_encode($value['nombre_etapa'])?> </td>
                              <td class="tbdata" style="width:205px;">
                                  <a href="opportunity-view.php?id=<?php echo $value['id']?>" title="Ver detalle de: <?php echo $value['name']?>" class="btn btn-sm btn-icon btn-info"><i class="fa fa-edit"></i></a>
                                  <a href="modal-notes.php?id=<?php echo $value['id']?>&master_note=<?php echo $value['id_ref_master_note']?>" title="Agregar una nota" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-primary"><i class="i i-chat"></i></a>
                                  <?php date_default_timezone_set("America/Panama"); ?>
                                  <?php $fecha=date("Y-m-d",strtotime($value['nextstep_date'])); ?>
                                  <?php $hora=date("H:i",strtotime($value['nextstep_date'])); ?>
                                  <a href="modal-next-step.php?next_step=<?php echo $value['id']?>&next_step_id=<?php echo $value['nextstep']?>&fecha=<?php echo $fecha; ?>&hora=<?php echo $hora; ?>&id_master_note=<?php echo $value['id_ref_master_note']?>" title="Proximo Paso" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-success"><i class="glyphicon glyphicon-calendar"></i></a>
                                  <a href="modal-notes.php?call=1&id=<?php echo $value['id']?>&master_note=<?php echo $value['id_ref_master_note']?>" title="Agregar una nota de llamada" data-toggle="ajaxModal" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-warning"><i class="fa fa-phone"></i></a>
                                  <a href="modal-etapa.php?etapa=<?php echo $value['id']?>&source=<?php echo $value['id_stage']; ?>" title="Etapa" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-default"><i class="glyphicon glyphicon-tasks"></i></a>
                                  <?PHP if($loggdUType == "Admin") :  ?>
                                  <a href="modal-usuario.php?usuario=<?php echo $value['id_user_register']?>&oportunidad_id=<?php echo $value['id']?>" title="Asignar un usuario" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-danger"><i class="i i-user2"></i></a>
                                  <?php endif; ?>
                              </td>
                              <!-- <td> <button type="button" onclick="window.location='opportunity-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">See Detail</button>
                              </td> -->
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
              <?php
              /*$arrUser = GetRecords("SELECT opportunity.*, contact.Name as contactname, business.Name as bizname, stage.Name_stage  as stagename from opportunity
                                     inner join contact on contact.id  = opportunity.id_contact
                                     inner join business on business.id  = contact.id_business
                                     inner join stage on stage.id  = opportunity.id_stage

                                     $where and opportunity.stat in (3,4, 5, 6)
                                     order by opportunity.name");
                                     ?>
              <?php /* ?>
              <section class="panel panel-default">
                <header class="panel-heading">
                          <span class="h4">Oportunidades Cerradas (Ganadas o Perdidas)</span>
                </header>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" id="example"  data-ride="datatables">
                          <thead>
                            <tr>
                              <th>Nombre del Contacto</th>
                              <th>Nombre de la Oportunidad</th>
                              <th>Empresa</th>
                              <th>Monto</th>
                              <th>Fecha de Creacion</th>
                              <th>Fecha de posible Cierre</th>
                              <th>Etapa</th>
                              <th>Estatus</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=1;
                            foreach ($arrUser as $key => $value) {

                              $status = $arrStatus[$value['stat']];
                            ?>
                          <tr>
                              <td class="tbdata"> <a href='opportunity-view.php?id=<?php echo $value['id']?>'><?php echo $value['contactname']?></a> </td>
                              <td class="tbdata"> <?php echo $value['name']?> </td>
                              <td class="tbdata"> <?php echo $value['bizname']?> </td>
                              <td class="tbdata"> <?php echo $value['estimated_amount']?> </td>
                              <td class="tbdata"> <?php echo date("M d, Y", strtotime($value['created_date']))?> </td>
                              <td class="tbdata"> <?php echo date("M d, Y", strtotime($value['closing_date']))?> </td>
                              <td class="tbdata"> <?php echo $value['stagename']?> </td>
                              <td class="tbdata"> <?php echo $status?> </td>
                              <!-- <td> <button type="button" onclick="window.location='opportunity-view.php?id=<?php echo $value['id']?>';" class="btn green btn-info">See Detail</button>
                              </td> -->
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
              <?php */ ?>
            </section>
        </section>
    </section>

<?php
	include("footer.php");
?>
