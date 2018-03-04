<?php

    ob_start();
    $opportunityclass="class='active'";
    $registerOpporclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();

    include("header.php");

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

     $message="";

    if(isset($_POST['submitContac']))
     {
        $name = $_POST['name'];
        $busid = $_POST['busid'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $description = $_POST['business_description'];
        $arrVal = array(
                      "Name" => $name,
                      "id_business" => $busid,
                      "Phone" => $phone,
                      "Email" => $email,
                      "position" => $position,
                      "Description" => $description,
                      "create_date" => date("Y-m-d H:i:s"),
                      "id_user_register" => $_SESSION['USER_ID'],
                      "stat" => 1
                     );

          $nId = InsertRec("contact", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado un contacto dentro del formulario de oportunidad. Contacto: '".$name."'";
            log_actividad(5, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {

              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("contact", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New contact ".$name." registered by ".$_SESSION['USER_NAME'];
                $subj = "Contact ( ".$name." )";
                create_log($mstId, $subj, $notemsg);
              }

              if(isset($_FILES['logo']) && $_FILES['logo']['tmp_name'] != "")
              {
                  $target_dir = "contactimage/";
                  $target_file = $target_dir . basename($_FILES["logo"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["logo"]["tmp_name"], $filename))
                  {
                      makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);

                      UpdateRec("contact", "id = ".$nId, array("Image" => $filenameThumb));
                  }
              }

              $message = '<div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Contacto registrado</strong>
                          </div>';
          }

     }

     if(isset($_POST['submitStep1']))
     {
        $contactid = $_POST['contact'];
        $nextstepdate = $_POST['nextstepdate'];
        /*$preparationdate = $_POST['preparationdate'];
        $proposaldate = $_POST['proposaldate'];
        $closingdate = $_POST['closingdate'];
        $stage = $_POST['stage'];*/
        $estimatedamount = $_POST['estimatedamount'];
        $source = $_POST['source'];
        $nextstep = $_POST['nextstep'];
        $oppname = $_POST['name'];
        $description = $_POST['description'];

        /* Quitado del Array

        "preparation_date" => $preparationdate,
        "proposal_date" => $proposaldate,
        "closing_date" => $closingdate,
        "id_stage" => $stage,

        */

        $usuario = $_SESSION['USER_ID'];

        if(isset($_POST['bizname'])){
          if($_POST['bizname']!=''){
             $usuario = $_POST['bizname'];
          }else{
             $usuario = $_SESSION['USER_ID'];
          }
        }

        if($nextstep ==""){
          $nextstep=1;
        }

        $arrVal = array(
                      "name" => $oppname,
                      "id_contact" => $contactid,
                      "description" => $description,
                      "nextstep_date" => $nextstepdate,
                      "estimated_amount" => $estimatedamount,
                      "source" => $source,
                      "nextstep" => $nextstep,
                      "id_user_register" => $usuario,
                      "created_date" => date("Y-m-d"),
                      "stat" => 1,
                      "id_stage" => 1
                     );

          $nId = InsertRec("opportunity", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha creado una oportunidad. Oportunidad: '".$oppname."'";
            log_actividad(7, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {

              MySQLQuery("Insert into master_notes (id) values (0)");

              $mstId = mysql_insert_id();
              UpdateRec("opportunity", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "New opportunity ".$oppname." registered by ".$_SESSION['USER_NAME'];
                $subj = "opportunity ( ".$name." ) registered";
                create_log($mstId, $subj, $notemsg);
              }

              echo "<script>alert('Opportunity created');window.location='register-opportunity-step1.php';</script>";
          }
          else
          {
            echo "<script>alert('Opportunity not created');window.location='register-opportunity-step1.php';</script>";
          }



     }

     if(isset($_POST['empresa_modal']))
      {
         $name = $_POST['name'];
         $rut = $_POST['rut'];
         $phone = $_POST['phone'];
         $email = $_POST['email'];
         $description = $_POST['description'];
         $backgroundcompany = $_POST['backgroundcompany'];
         $url = $_POST['url'];
         $address = $_POST['address'];

         $arrVal = array(
                       "Name" => $name,
                       "Rut" => $rut,
                       "Phone" => $phone,
                       "Email" => $email,
                       "Url" => $url,
                       "Address" => $address,
                       "ComanyBackground" => $backgroundcompany,
                       "Description" => $description,
                       "create_date" => date("Y-m-d H:i:s"),
                       "id_user_register" => $_SESSION['USER_ID'],
                       "stat" => 1
                      );

           $nId_business = InsertRec("business", $arrVal);

           /* Log de Actividad */
             $mensaje = "Se ha creado una empresa dentro del formulario de oportunidad. Empresa: '".$name."'";
             log_actividad(5, 1, $_SESSION['USER_ID'], $mensaje);
           /* Fin de log de actividad */

           if($nId_business > 0)
           {

               MySQLQuery("Insert into master_notes (id) values (0)");

               $mstId = mysql_insert_id();
               UpdateRec("business", "id = ".$nId_business, array("id_ref_master_note" => $mstId));

               if($mstId > 0)
               {
                 $notemsg = "New business ".$name." registered by ".$_SESSION['USER_NAME'];
                 $subj = "Business ( ".$name." )";
                 create_log($mstId, $subj, $notemsg);
               }

               if(isset($_FILES['logo']) && $_FILES['logo']['tmp_name'] != "")
               {
                   $target_dir = "logo/";
                   $target_file = $target_dir . basename($_FILES["logo"]["name"]);
                   $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                   $filename = $target_dir . $nId_business.".".$imageFileType;
                   $filenameThumb = $target_dir . $nId_business."_thumb.".$imageFileType;
                   if (move_uploaded_file($_FILES["logo"]["tmp_name"], $filename))
                   {
                       makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId_business, 100, 100);

                       UpdateRec("business", "id = ".$nId_business, array("Logo" => $filenameThumb));
                   }
               }

               $message = '<div class="alert alert-success">
                     <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                       <strong>Empresa registrada correctamente</strong>
                     </div>';
           }

      }

if(isset($nId_business)){

    $ultimo_id = MySQLQuery("select MAX(id) as id from business");
    while($l=mysql_fetch_array($ultimo_id)){
      $ulti = $l['id'];
    }

}

if(isset($_POST['submitContac'])){

    $ultimo_id = MySQLQuery("select MAX(id) as id from contact");
    while($l=mysql_fetch_array($ultimo_id)){
      $ultic = $l['id'];
    }

}

if(isset($_POST['busid'])){
  $ulti = $_POST['busid'];
}

?>

	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">

              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" method="post" >
                      <section class="panel panel-default">
                        <?php
                              if($message !="")
                                  echo $message;
                        ?>
                        <header class="panel-heading">
                          <span class="h4">Registro de Oportunidad</span>
                        </header>
                        <div class="panel-body">

                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Seleccionar Empresa</label>
                            <div class="col-lg-3">
                              <select class="chosen-select form-control" name="busid" onChange="getOptionsData(this.value, 'contactbybusiness', 'contact'); updatevariable(this.value)" data-required="true">
                                <option value="">----------------</option>
                                <?PHP

                                $where = "";
                                 if($loggdUType != "Admin" && $loggdUType != "Business Unit Manager")
                                 {
                                   $where.=" and id_user_register=". $_SESSION['USER_ID'];
                                 }
                                $arrKindMeetings = GetRecords("Select * from business where stat = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name'];
                                  $selRoll = (isset($ulti) && $ulti == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>

                            </div>
                            <a href="modal-business.php" data-dismiss="ajaxModal" data-toggle="ajaxModal" class="btn btn-s-md btn-primary" style="float: left; width: 50px; height: 30px;"><b>+</b></a>
                          </div>

                          <div class="form-group required">

                            <label class="col-lg-4 text-right control-label font-bold">
                              Seleccionar Contacto
                              <input id="servicioSelecionado" name="nom_Servicio" type="hidden" >
                            </label>
                            <div class="col-lg-3">

                              <select class="chosen-select form-control" id="contact" name="contact" data-required="true">
                                <option value="">----------------</option>
                                 <?PHP
                                $where = "";
                                 if($loggdUType != "Admin")
                                 {
                                   $where.=" and id_user_register=". $_SESSION['USER_ID'];
                                 }
                                $arrKindMeetings = GetRecords("Select * from contact where stat = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name'];
                                  $selRoll = (isset($ultic) && $ultic == $kinId) ? 'selected' : '';
                                ?>
                                <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                <?php
                                 }
                                ?>
                              </select>
                            </div>
                            <?php if(isset($ulti)){$ulti;}else{$ulti = 0;}?>
                            <a href="modal-contacto.php?empresa=<?php echo $ulti?>" data-dismiss="ajaxModal" data-toggle="ajaxModal" class="btn btn-s-md btn-primary" style="float: left; width: 50px; height: 30px;"><b>+</b></a>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de la Oportunidad</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre de la Oportunidad" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                            <div class="col-lg-4">
                              <textarea rows="3" class="form-control" cols="44" name="description"  placeholder="Descripción"></textarea>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Monto Estimado</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Monto Estimado"  name="estimatedamount" data-required="true">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Seleccionar Fuente</label>
                            <div class="col-lg-4">
                               <select class="chosen-select form-control" name="source">
                                <option value="">----------------</option>
                                <?php
                                $where = "";

                                $arrKindMeetings = GetRecords("Select * from source where status = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name_source'];
                                ?>
                                <option value="<?php echo $kinId?>"><?php echo utf8_encode($kinDesc)?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Próximo Paso</label>
                            <div class="col-lg-4">
                              <!--<input type="text" class="form-control" placeholder="Próximo Paso" name="nextstep" >-->

                              <select class="chosen-select form-control"  name="nextstep">
                                  <option value="">----------------</option>
                                  <?PHP

                                  $arrKindMeetings = GetRecords("Select * from next_step
                                                                 where stat = 1");
                                  foreach ($arrKindMeetings as $key => $value) {
                                    $kinId = $value['id'];
                                    $kinDesc = $value['detail'];
                                  ?>
                                  <option value="<?php echo $kinId?>"><?php echo utf8_encode($kinDesc)?></option>
                                  <?php
                              }
                                  ?>
                                </select>

                            </div>
                          </div>
                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">Fecha del Proximo Paso</label>
                            <div class="col-lg-4">
                              <input class="datepicker-input form-control datepicker" id="datepicker" name="nextstepdate" readonly=""  type="text"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>

                          <?PHP if($loggdUType == "Admin") : ?>

                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">Bussines Manager</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control"  name="bizname">
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
                            </div>

                            <?php endif;?>

                          <?php /* ?>
                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">Fecha de preparacion de propouesta</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker1" name="preparationdate" size="16" readonly=""  type="text"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">Fecha de entrega de propuesta</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker2" name="proposaldate" size="16" readonly=""  type="text"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group ">
                            <label class="col-lg-4 text-right control-label font-bold">Fecha estamima de cierre</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker3" name="closingdate" size="16" readonly="" type="text"  data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>

                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Seleccionar Etapa</label>
                            <div class="col-lg-4">
                               <select class="chosen-select form-control" name="stage" data-required="true">
                                <option value="">----------------</option>
                                <?PHP
                                $where = "";

                                $arrKindMeetings = GetRecords("Select * from stage where status = 1 $where");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['Name_stage'];
                                ?>
                                <option value="<?php echo $kinId?>"><?php echo utf8_encode($kinDesc)?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <?php */ ?>
                        </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" name="submitStep1" class="btn btn-primary btn-s-xs">Registrar</button>
                        </footer>
                      </section>
                    </form>
                  </div>
              </div>
            </section>
        </section>
    </section>

<?php
	include("footer.php");
?>
