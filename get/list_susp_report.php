<?php
    ob_start();
    $activityclass="class='active'";
    $activitytelemarketign="class='active'";
    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    include("header.php");

    $message="";

    if(isset($_POST['submitNote']))
    {
          $notetype = $_POST['notetype'];
          $note = $_POST['note'];
          $noteid = $_POST['noteid'];
          $userrefid = $_POST['master_note'];
          $subject = $_POST['subject'];

          if($userrefid != "")
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
              $message = '<div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Nota Creada con éxito</strong>
                          </div>';
          }

    }


    if(isset($_POST['submitNextstep'])){

      $update_next_step = MySQLQuery("update suspects set date_next_step = '".$_POST['nextstepdate']."',
                                                          next_step = '".$_POST['nextstep']."'
                                                          where
                                                          id = '".$_POST['id']."'");

      $arrUser = GetRecords("SELECT * FROM next_step WHERE id = '".$_POST['nextstep']."'");

         foreach($arrUser as $ns => $nextstep){
           $nombre_ns = $nextstep['detail'];
         }

         $log_next_step = "Proximo Paso: '".$nombre_ns."'";

         create_log_next_step($_POST['nextid'], $log_next_step, $log_next_step, 6);

         $message = '<div class="alert alert-success">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>Próximo Paso actualizado con éxito</strong>
                     </div>';

    }


    if(isset($_POST['submitResult'])){

      $arrVal = array(
                      "result" => $_POST['result']
                     );

      UpdateRec("suspects", "id = ".$_POST['id'], $arrVal);

        $getrefid = GetRecord("campaign", "id = ".$_GET['id_campaign']);
        $name_campaign = $getrefid['name'];

        /* Log de Actividad */
        $mensaje = "Se ha Modificado el resultado de la Campaña '".$name_campaign."'";
        log_actividad(9, 11, $_SESSION['USER_ID'], $mensaje);
        /* Fin de log de actividad */

        $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>El Resultado ha sido Modificado</strong>
                    </div>';
    }


    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

      $where = "where (1=1)";
      if($loggdUType != "Admin")
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

      $arrUser = GetRecords("select
                             suspects.business,
                             suspects.customer_client,
                             suspects.phone_office,
                             suspects.next_step,
                             suspects.date_next_step,
                             suspects.verified,
                             suspects.id as id_suspect,
                             next_step_suspect.name as name_next_step_suspect,
                             suspects.id_ref_master_note,
                             suspects.date_create,
                             campaign.id as id_campaign,
                             campaign.name as name_campaign,
                             campaign.date_start,
                             campaign.date_end,
                             campaign.sospechoso,
                             campaign.verificado,
                             campaign.lead,
                              case result
                                when 0 then 'Sin resultado'
                                when 1 then 'Lead por Asignar'
                                when 2 then 'No interezado'
                                when 3 then 'No existe'
                                when 4 then 'No contesto'
                                when 5 then 'Ya tiene el Servicio / Producto'
                                when 6 then 'Lead Aprobado'
                                when 7 then 'Sin Presupuesto'
                              end as result_concept,
                              suspects.result,
                              suspects.item,
                              suspects.position,
                              suspects.email,
                              suspects.email_contact,
                              suspects.cell_phone,
                              suspects.domain,
                              (select count(*) from note_detail where note_ref_id = suspects.id_ref_master_note and note_type not in(5)) as note,
                              (select count(*) from note_detail where note_ref_id = suspects.id_ref_master_note and note_type in(5)) as note_call,
                              (select count(*) from suspects where next_step not in(1) and id_campaign = campaign.id) as next_step,
                              (select count(*) from suspects where result = 2 and id_campaign = campaign.id) as no_interezado,
                              (select count(*) from suspects where result = 4 and id_campaign = campaign.id) as no_contesto,
                              (select count(*) from suspects where result = 7 and id_campaign = campaign.id) as no_presupuesto,
                              (select count(*) from suspects where result in(3, 5) and id_campaign = campaign.id) as otros
                             from campaign inner join suspects on suspects.id_campaign = campaign.id
                                           inner join next_step_suspect on next_step_suspect.id = suspects.next_step
                             where
                             campaign.id = '".$_GET['id_campaign']."'
                             and
                             campaign.stat = 1");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <header class="panel-heading">
                    <span class="h4">Reporte Telemarketing</span>
                </header>
                <?php if ($message!="") {
                  echo $message;
                } ?>
                <div class="panel-body">
                  <?php /* --------------------------------------------------- */ ?>
                  <?php

                  $name_campaign = $arrUser[0]['name_campaign'];
                  $date_start_campaign = $arrUser[0]['date_start'];
                  $date_end_campaign = $arrUser[0]['date_end'];
                  $sospechoso = $arrUser[0]['sospechoso'];
                  $verificado = $arrUser[0]['verificado'];
                  $lead = $arrUser[0]['lead'];
                  $no_interezado = $arrUser[0]['no_interezado'];
                  $no_contesto = $arrUser[0]['no_contesto'];
                  $no_presupuesto = $arrUser[0]['no_presupuesto'];
                  $otros = $arrUser[0]['otros'];
                  $next_step = $arrUser[0]['next_step'];
                  $note = $arrUser[0]['note'];
                  $call = $arrUser[0]['note_call'];

                   ?>
                  <div class="row wrapper">
                    <div class="col-sm-2 m-b-xs">
                      <div class="input-group" style="width:100%;">
                        <b>Campaña:
                          <?php if(isset($name_campaign)){ echo $name_campaign; }else{ echo ""; } ?>
                        </b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b>Fecha Inicial: <?php if(isset($date_start_campaign)){ echo $date_start_campaign; }else{ echo "-"; } ?></b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b>Fecha Final: <?php if(isset($date_end_campaign)){ echo $date_end_campaign; }else{ echo "-"; } ?></b>
                      </div>
                    </div>
                    <div class="col-sm-2 m-b-xs">
                      <div class="input-group" style="width:100%;">
                        <b>sospechosos: <?php echo $sospechoso?></b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b style="color:blue">Contactos Verificados: <?php echo $verificado?></b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b style="color:#3ed16a">Leads: <?php echo $lead?></b>
                      </div>
                    </div>
                    <?php foreach ($arrUser as $key => $value) {
                            $call+=$value['note_call'];
                            $note+=$value['note'];
                    } ?>
                    <div class="col-sm-2 m-b-xs">
                      <div class="input-group" style="width:100%;">
                        <b>Notas: <?php echo $note?></b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b>Próximos Pasos: <?php echo $next_step?></b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b>Notas de llamadas: <?php echo $call?></b>
                      </div>
                    </div>
                    <div class="col-sm-2 m-b-xs">
                      <div class="input-group" style="width:100%;">
                        <b>No contestaron: <span  style="color:red"> <?php echo $no_contesto?></span> </b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b>No interesados: <?php echo $no_interezado?></b>
                      </div>
                      <div class="input-group" style="width:100%;">
                        <b>Sin presupuesto: <?php echo $no_presupuesto?></b>
                      </div>
                    </div>
                  </div>
                  <?php /* --------------------------------------------*/ ?>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>N°</th>
                              <th>Empresa</th>
                              <th>Contacto</th>
                              <th>Teléfono</th>
                              <th>Próximo Paso</th>
                              <th>Fecha de Próximo Paso</th>
                              <th>Resultado</th>
                              <th>Verificado</th>
                              <th>Acción</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            $i=0;
                            foreach ($arrUser as $key => $value) {
                              $i++; ?>
                          <tr>
                              <td class="tbdata"><b><?php echo $i;?></b></td>
                              <td class="tbdata"> <?php if(is_numeric($value['business'])){
                                  $arrbusiness = GetRecords("Select * from business WHERE id = '".$value['business']."'");
                                  $name_business = $arrbusiness[0]["Name"];
                                  echo $name_business;
                                }else{
                                  echo $value['business'];
                                }?></td>
                              <td class="tbdata"> <?php echo $value['customer_client']?> </td>
                              <td class="tbdata"> <?php echo $value['phone_office']?> </td>
                              <td class="tbdata"> <?php echo utf8_encode($value['name_next_step_suspect'])?> </td>
                              <td class="tbdata"> <?php echo $value['date_next_step']?> </td>
                              <td class="tbdata"> <?php echo $value['result_concept']?> </td>
                              <td class="tbdata" style="text-align:center;"> <?php if($value['verified']==0){ ?> <i class="fa fa-check-circle" style="font-size:22px;"></i> <?php }else{ ?> <i class="fa fa-check-circle" style="color:#5DADE2; font-size:22px;"></i> <?php }?> </td>
                              <td class="tbdata" style="width:205px;">
                                  <a href="suspect.php?id_suspect=<?php echo $value['id_suspect']?>&id_campaign=<?php echo $value['id_campaign']?>" title="Ver detalle de: " class="btn btn-sm btn-icon btn-info"><i class="fa fa-edit"></i></a>
                                  <a href="modal-notes.php?id_suspect=<?php echo $value['id_suspect']?>&master_note=<?php echo $value['id_ref_master_note']?>" title="Agregar una nota" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-primary"><i class="i i-chat"></i></a>
                                  <?php $fecha=date("Y-m-d",strtotime($value['date_next_step'])); ?>
                                  <?php $hora=date("H:i",strtotime($value['date_next_step'])); ?>
                                  <a href="modal-next-step-suspect.php?next_step=<?php echo $value['id_suspect']?>&next_step_id=<?php echo $value['next_step']?>&fecha=<?php echo $fecha;?>&id_master_note=<?php echo $value['id_ref_master_note']?>&hora=<?php echo $hora; ?>" title="Proximo Paso" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-success"><i class="glyphicon glyphicon-calendar"></i></a>
                                  <a href="modal-notes.php?call=1&id_suspect=<?php echo $value['id_suspect']?>&master_note=<?php echo $value['id_ref_master_note']?>" title="Agregar una nota de llamada" data-toggle="ajaxModal" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-warning"><i class="fa fa-phone"></i></a>
                                  <?php $result_check = 0;
                                        if($value['business']!="" &&
                                           $value['customer_client']!="" &&
                                           $value['item']!="" &&
                                           $value['position']!="" &&
                                           $value['verified']!=0 &&
                                           $value['email']!="" &&
                                           $value['email_contact']!="" &&
                                           $value['phone_office']!="" &&
                                           $value['cell_phone']!="" &&
                                           $value['domain']!=""){ $result_check = 1;} ?>
                                  <a href="result.php?result=<?php echo $value['id_suspect']?>&id_result=<?php echo $value['result']; ?>&result_check=<?php echo $result_check; ?>" title="Etapa" data-toggle="ajaxModal" class="btn btn-sm btn-icon btn-default"><i class="glyphicon glyphicon-tasks"></i></a>
                              </td>
                          </tr>
                          <?php
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
