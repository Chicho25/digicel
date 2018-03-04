<?php
    ob_start();
    $campanatyclass="class='active'";
    $registerCampanaclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']) || $loggdUType != "Admin" && $loggdUType != "Admin Telemarketing" && $loggdUType != "Business Unit Manager")
     {
          header("Location: index.php");
          exit;
     }
     $message="";

     /*

     if(isset($_POST['submitResult'])){

       $arrVal = array(
                       "result" => $_POST['result']
                      );

       UpdateRec("suspects", "id = ".$_POST['id'], $arrVal);

         $getrefid = GetRecord("campaign", "id = ".$_GET['id_campaign']);
         $name_campaign = $getrefid['name'];

         /* Log de Actividad
         $mensaje = "Se ha Modificado el resultado de la Campaña '".$name_campaign."'";
         log_actividad(9, 11, $_SESSION['USER_ID'], $mensaje);
         /* Fin de log de actividad

         $message = '<div class="alert alert-success">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>El Resultado ha sido Modificado</strong>
                     </div>';
     }

     if(isset($_POST['submitcheck'])){

       $arrVal = array(
                       "verified" => 1
                      );

       UpdateRec("suspects", "id = ".$_POST['id_suspect'], $arrVal);

         $getrefid = GetRecord("campaign", "id = ".$_POST['id_campaign']);
         $name_campaign = $getrefid['name'];

         $notemsg = "Sospechoso Verificado";
         $subj = "Sospechoso Verificado";
         create_log($_POST['note_master'], $subj, $notemsg);

         /* Log de Actividad
         $mensaje = "Se ha Verificado un sospechoso Campaña'".$name_campaign."'";
         log_actividad(9, 10, $_SESSION['USER_ID'], $mensaje);
         /* Fin de log de actividad

         $message = '<div class="alert alert-success">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>El Sospechoso ha sido verificado</strong>
                     </div>';
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

     }

    if(isset($_POST['submitSuspect']))
     {

            $arrVal = array(
                          "business" => $_POST['business'],
                          "customer_client" => $_POST['contact'],
                          "position" => $_POST['position'],
                          "email" => $_POST['email'],
                          "phone_office" => $_POST['phone_ofice'],
                          "cell_phone" => $_POST['phone'],
                          "domain" => $_POST['domain'],
                          "email_contact" => $_POST['email_contact'],
                          "item" => $_POST['item'],
                          "id_user_updated" => $_SESSION['USER_ID']
                         );

          UpdateRec("suspects", "id = ".$_POST['id_suspec'], $arrVal);

          /* Log de Actividad
          $mensaje = "Se ha Modificado un sospechoso. Sospechoso: '".$_POST['name_campaign']."' ";
          log_actividad(9, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad

          $message = '<div class="alert alert-success">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>El Sospechoso ha sido Modificado</strong>
                      </div>';

          }

          */

          $arrUser = GetRecords("select
                                 suspects.business,
                                 suspects.customer_client,
                                 suspects.phone_office,
                                 suspects.next_step,
                                 suspects.date_next_step,
                                 suspects.result,
                                 suspects.verified,
                                 suspects.id as id_suspect,
                                 next_step.detail,
                                 suspects.id_ref_master_note,
                                 suspects.date_create,
                                 campaign.name,
                                 suspects.email,
                                 suspects.domain,
                                 suspects.cell_phone,
                                 suspects.position,
                                 suspects.email_contact,
                                 suspects.item
                                 from campaign inner join suspects on suspects.id_campaign = campaign.id
                                               inner join next_step on next_step.id = suspects.next_step
                                 where
                                 campaign.id = '".$_GET['id_campaign']."'
                                 and
                                 suspects.id = '".$_GET['id_suspect']."'
                                 and
                                 campaign.stat = 1");

          $userrefid = $arrUser[0]['id_ref_master_note'];
          $suspectid = $arrUser[0]['id_suspect'];
          $next_step = $arrUser[0]['next_step'];
          $date_next_step = $arrUser[0]['date_next_step'];
          $verified = $arrUser[0]['verified'];
          $result = $arrUser[0]['result'];
          $email = $arrUser[0]['email'];
          $phone_office = $arrUser[0]['phone_office'];
          $domain = $arrUser[0]['domain'];
          $cell_phone = $arrUser[0]['cell_phone'];
          $position = $arrUser[0]['position'];
          $name_business = $arrUser[0]['business'];
          $contacto = $arrUser[0]['customer_client'];
          $email_contact = $arrUser[0]['email_contact'];
          $item = $arrUser[0]['item'];

          ?>

	   <section id="content">
        <section class="vbox">
          <section class="scrollable padder">
            <?php if(isset($message) && $message != ""){

                  echo $message;

            } ?>
            <div class="row">
              <div class="col-sm-12">
              	<form class="form-horizontal" action="#" data-validate="parsley" method="post" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="id_suspec" value="<?php echo $_GET['id_suspect'];?>">
                    <input type="hidden" name="name_campaign" value="<?php echo $arrUser[0]['name']; ?>">
                    <section class="panel panel-default">
                      <header class="panel-heading">
                        <span class="h4">Campaña: <?php echo $arrUser[0]['name']; ?></span>
                      </header>
                      <div class="panel-body">
                        <span class="h4">Detalles del Sospechoso </span>
                        <div class="form-group">
                          <label class="col-lg-2 text-right control-label font-bold"><h3>Detalle de Empresa</h3></label>
                          <label class="col-lg-2 text-right control-label font-bold"><h3></h3></label>
                          <label class="col-lg-2 text-right control-label font-bold"><h3>Detalle Contacto</h3></label>
                          <label class="col-lg-2 text-right control-label font-bold"><h3></h3></label>
                          <label class="col-lg-2 text-right control-label font-bold"><h3>Próximo Paso</h3></label>
                        </div>
                        <div class="form-group">
                          <label class="col-lg-2 text-right control-label font-bold">Empresa</label>
                          <div class="col-lg-2">
                            <?php /* Verificar si existe la empresa */ ?>
                            <?php $verificarEmpresa = GetRecords("Select * from business where name like '%$name_business%'"); ?>
                            <?php $contar = 0; ?>
                            <?php foreach ($verificarEmpresa as $key => $value) {
                                    $nombre_empresa = $value['Name'];
                                    $contar++;
                            } ?>
                            <?php if($contar >= 1){ ?>
                            <select class="chosen-select form-control" disabled name="business" required="required" onChange="ocultar();">
                              <option value="">--------------</option>
                              <?PHP
                              foreach ($verificarEmpresa as $key => $value) {
                                $kinId = $value['id'];
                                $kinDesc = $value['Name'];
                              ?>
                              <option value="<?php echo $kinId?>"><?php echo $kinDesc?></option>
                              <?php
                                  }
                              ?>
                            </select>
                          <?php }else{ ?>
                            <input type="text" readonly class="form-control" placeholder="Empresa" name="business" data-required="true"
                            value="<?php if($name_business != ""){

                               if(is_numeric($name_business)){
                                $arrbusiness = GetRecords("Select * from business WHERE id = '".$name_business."'");
                                $name_business = $arrbusiness[0]["Name"];
                                echo $name_business;
                              }else{
                                echo $name_business;
                              }

                             } ?>">
                          <?php } ?>
                            <?php if($contar == 0){}else{ ?>
                            <?php echo '<small id="ocultar_smal" style="color:red;">'.$contar.' Similares</small>'; ?>
                             <?php } ?>
                            <?php /* Fin de la verificacion */ ?>

                            <script type="text/javascript">
                              function ocultar(){
                                document.getElementById("ocultar_smal").innerHTML="";
                              }
                            </script>

                          </div>
                          <label class="col-lg-2 text-right control-label font-bold">Contacto</label>
                          <div class="col-lg-2">
                            <input type="text" readonly class="form-control" placeholder="Contacto" name="contact" data-required="true" value="<?php if($contacto != ""){ echo $contacto; } ?>">
                          </div>
                          <div class="col-lg-1">
                          </div>
                          <label class="col-lg-1 label label-success control-label font-bold" style="text-align: center; margin-left:-20px">
                            <?PHP
                             $arrKindMeetings = GetRecords("Select * from next_step where stat = 1");
                             foreach ($arrKindMeetings as $key => $value) {
                              $kinId = $value['id'];
                              $kinDesc = $value['detail'];
                              if($arrUser[0]['next_step']==$kinId){ echo utf8_encode($kinDesc);} } ?>
                          </label>
                             <br><?php echo $arrUser[0]['date_next_step']; ?>
                           <div class="col-lg-1">
                           </div>

                        </div>
                        <div class="form-group">
                          <label class="col-lg-2 text-right control-label font-bold">Rubro</label>
                          <div class="col-lg-2">
                            <select disabled class="chosen-select form-control" name="item" required="required" onChange="mostrar(this.value);">
                              <option value="">--------------</option>
                              <?PHP
                              $arrKindMeetings = GetRecords("Select * from rubro where stat = 1");
                              foreach ($arrKindMeetings as $key => $value) {
                                $kinId = $value['id'];
                                $kinDesc = $value['name'];?>
                              <option value="<?php echo $kinId?>" <?php if($item == $kinId){ echo "selected";} ?>><?php echo $kinDesc?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <label class="col-lg-2 text-right control-label font-bold">Posición</label>
                          <div class="col-lg-2">
                            <input type="text" readonly class="form-control" placeholder="Posición" name="position" data-required="true" value="<?php if($position != ""){ echo $position; } ?>">
                          </div>
                          <label class="col-lg-1 text-right control-label font-bold"></label>
                          <div class="col-lg-2">
                            <a href="#" style="border-radius:100px; position:absolute;" title="Verificar Sospechoso" <?php /* data-toggle="ajaxModal" */?> class="btn btn-sm btn-icon"><i style="font-size:50px; <?php if($verified != 0){ echo 'color:#5DADE2'; } ?>" class="fa fa-check-circle"></i></a>
                          </div>
                        </div>
                        <div class="form-group required">
                          <label class="col-lg-2 text-right control-label font-bold">Email</label>
                          <div class="col-lg-2">
                            <input type="text" readonly class="form-control" placeholder="Email" name="email" data-required="true" value="<?php if($email != ""){ echo $email; } ?>">
                          </div>
                          <label class="col-lg-2 text-right control-label font-bold">Email</label>
                          <div class="col-lg-2">
                            <input type="text" readonly class="form-control" placeholder="Email" name="email_contact" data-required="true" value="<?php if($email_contact != ""){ echo $email_contact; } ?>">
                          </div>
                        </div>
                        <div class="form-group required">
                          <label class="col-lg-2 text-right control-label font-bold">Telefono</label>
                          <div class="col-lg-2">
                            <input type="text" readonly class="form-control" placeholder="Telefono" name="phone_ofice" data-required="true" value="<?php if($phone_office != ""){ echo $phone_office; } ?>">
                          </div>
                          <label class="col-lg-2 text-right control-label font-bold">Telefono</label>
                          <div class="col-lg-2">
                            <input type="text" readonly class="form-control" placeholder="Telefono" name="phone" data-required="true" value="<?php if($cell_phone != ""){ echo $cell_phone; } ?>">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-lg-2 text-right control-label font-bold">Dominio</label>
                          <div class="col-lg-2">
                            <input type="text" readonly class="form-control" placeholder="Dominio" name="domain" data-required="true" value="<?php if($domain != ""){ echo $domain; } ?>">
                          </div>
                        </div>
                      <footer class="panel-footer text-right bg-light lter">
                        <?php  ?><a href="list_oppprtunities_to_assign.php" class="btn btn-primary btn-s-xs">Regresar</a>
                      </footer>
                    </div>
                    </section>
                  </form>
                </div>
            </div>
            <?php /* ?>
            <section class="panel panel-default">
              <header class="panel-heading">
                <span><a href="modal-notes.php" data-toggle="ajaxModal" class="btn btn-sm btn-primary">Nota</a></span>
                <span><a href="modal-notes.php?call=1" data-toggle="ajaxModal" class="btn btn-sm btn-warning">Nota de Llamada</a></span>
                <?php $fecha=date("Y-m-d",strtotime($date_next_step)); ?>
                <span><a href="modal-next-step.php?next_step=<?php echo $suspectid?>&next_step_id=<?php echo $next_step?>&fecha=<?php echo $fecha?>&id_master_note=<?php echo $userrefid; ?>" title="Proximo Paso" data-toggle="ajaxModal" class="btn btn-sm btn-success">Próximo Paso</a></span>
                <?php $result_check = 0;
                      if($name_business !="" &&
                         $contacto !="" &&
                         $item !="" &&
                         $position !="" &&
                         $verified!=0 &&
                         $email !="" &&
                         $email_contact !="" &&
                         $phone_office !="" &&
                         $cell_phone !="" &&
                         $domain !=""){ $result_check = 1;} ?>
                <span><a href="result.php?result=<?php echo $suspectid?>&id_result=<?php echo $result; ?>&result_check=<?php echo $result_check; ?>" title="Etapa" data-toggle="ajaxModal" class="btn btn-sm btn-default">Resultado</a></span>
              </header>*/

                include("notes-opportunity_suspect.php");
              ?>
            </section>
          </section>
      </section>
  </section>
<?php include("footer.php"); ?>
