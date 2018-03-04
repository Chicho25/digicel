<?php

    ob_start();
    $opportunityclass="class='active'";
    $editOpporclass="class='active'";

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
     if(isset($_POST['submitOption']))
     {
        $optname = $_POST['optname'];
        $description = $_POST['description'];
        $optid = $_POST['optid'];


        if($optid == "")
        {
          $arrVal = array(
                          "optionName" => $optname,
                          "description" => $description,
                          "id_opportunity" => $_REQUEST['id'],
                          "createdOn" => date("Y-m-d"),
                          "createdBy" => $_SESSION['USER_ID']
                         );
          $iopt = InsertRec("opportunity_option", $arrVal);

          /* Log de Actividad */
            $mensaje = "Se ha actualizado la oportunidad '".$optname."'";
            log_actividad(7, 2, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($iopt > 0)
          {
            $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>New Option created</strong>
                    </div>';
          }
          $getrefid = GetRecord("opportunity", "id = ".$_REQUEST['id']);
          $mstId = $getrefid['id_ref_master_note'];

          if($mstId > 0)
          {
            $notemsg = "New Option (".$optname.") for Opportunity ".$getrefid['name']." created by ".$_SESSION['USER_NAME'];
            $subj = "New Option ( ".$optname." ) created";
            create_log($mstId, $subj, $notemsg);
          }
        }
        else
        {
          $arrVal = array(
                          "optionName" => $optname,
                          "description" => $description
                         );

          UpdateRec("opportunity_option", "id = ".$optid, $arrVal);

          $getrefid = GetRecord("opportunity", "id = ".$_REQUEST['id']);
          $mstId = $getrefid['id_ref_master_note'];

          if($mstId > 0)
          {
            $notemsg = "New Option (".$optname.") for Opportunity ".$getrefid['name']." updated by ".$_SESSION['USER_NAME'];
            $subj = "New Option ( ".$optname." ) updated";
            create_log($mstId, $subj, $notemsg);
          }
          $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Option updated</strong>
                    </div>';
        }

     }

     if(isset($_POST['submitProduct']))
     {
        $arrVal = array(
                      "id_option" => $optionid,
                      "id_category" => $category,
                      "id_product" => $product,
                      "sale_price" => $saleprice,
                      "quantity" => $qty,
                      "description" => $description
                     );

        $nPid = InsertRec("opportunity_detail", $arrVal);
        if($nPid > 0)
        {
          $message = '<div class="alert alert-success">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                      <strong>Product added</strong>
                    </div>';
        }
     }
    if(isset($_POST['updateOpportunity']))
     {

        $contactid = $_POST['contact'];
        $nextstepdate = $_POST['nextstepdate'];
        $preparationdate = $_POST['preparationdate'];
        $proposaldate = date("Y-m-d",strtotime($_POST['proposaldate']))." ".$_POST['hour'];
        $closingdate = $_POST['closingdate'];
        $stage = $_POST['stage'];
        $estimatedamount = $_POST['estimatedamount'];
        $source = $_POST['source'];
        $nextstep = $_POST['nextstep'];
        $oppname = $_POST['name'];
        $description = $_POST['description'];
        $stval = $_POST['status'];
        $category = $_POST['category'];
        $arrVal = array(
                      "name" => $oppname,
                      "id_contact" => $contactid,
                      "description" => $description,
                      "nextstep_date" => $nextstepdate,
                      "preparation_date" => $preparationdate,
                      "proposal_date" => $proposaldate,
                      "closing_date" => $closingdate,
                      "id_stage" => $stage,
                      "estimated_amount" => $estimatedamount,
                      "source" => $source,
                      "nextstep" => $nextstep,
                      "stat" => $stval,
                      "id_category" => $category
                     );

          UpdateRec("opportunity", "id=".$_REQUEST['id'], $arrVal);
          $nId=$_REQUEST['id'];


          if($nId > 0)
          {

              $getrefid = GetRecord("opportunity", "id = ".$_REQUEST['id']);
              $mstId = $getrefid['id_ref_master_note'];
              UpdateRec("opportunity", "id = ".$nId, array("id_ref_master_note" => $mstId));

              if($mstId > 0)
              {
                $notemsg = "Opportunity ".$oppname." updated by ".$_SESSION['USER_NAME'];
                $subj = "Opportunity ( ".$name." ) updated";
                create_log($mstId, $subj, $notemsg);
              }

          }

     }


     if(isset($_POST['submitNextstep'])){

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

          $sql_oportunidad = GetRecords("SELECT name FROM opportunity WHERE id =".$_POST['id']);
                      foreach ($sql_oportunidad as $key => $l) {
                            $nombre_oportunidad=$l['name'];
                      }
          /* Log de Actividad */
            $mensaje = "Se ha modificado el proximo paso en la oportunidad '".$nombre_oportunidad."'";
            log_actividad(7, 2, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

     }
     if(isset($_POST['etapaNote'])){

       $update_next_step = MySQLQuery("update opportunity set id_stage = '".$_POST['etapaNote']."'
                                                              where
                                                              id = '".$_POST['id_opportunity']."'");

     }

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
         $comment = " Hola ".$nombre.",\n\n se le ha asignado la Oportunidad: ".$nombre_oportunidad.". Ingrese al sistema para mas detalles . <a href='" . $actual_link . "'>Click</a>";

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

     /* Propuesta */

     if(isset($_POST['email'],
                $_FILES['attached'],
                        $_POST['mensaje'],
                          $_POST['nota'])){


          /* Adjunto */

          function generateRandomString($length = 5) {
              $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
              $charactersLength = strlen($characters);
              $randomString = '';
              for ($i = 0; $i < $length; $i++) {
                  $randomString .= $characters[rand(0, $charactersLength - 1)];
              }
              return $randomString;
          }

          if(isset($_FILES['attached']) && $_FILES['attached']['tmp_name'] != "")
	          {
                $ramdon = generateRandomString();

	              $target_dir = "attached/";
	              $target_file = $target_dir . basename($_FILES["attached"]["name"]);
	              $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	              $filename = $target_dir . $ramdon.$_SESSION['USER_ID'].".".$imageFileType;
	              $filenameThumb = $target_dir . $ramdon.$_SESSION['USER_ID']."_thumb.".$imageFileType;
	              if (move_uploaded_file($_FILES["attached"]["tmp_name"], $filename))
	              {

	              }
	          }

            if(isset($filenameThumb)){ $filenameThumb = $filenameThumb;}else{ $filenameThumb = "";}

          $insert_propuesta = MySQLQuery("INSERT INTO proposal(status,
                                                               email,
                                                               message,
                                                               note,
                                                               id_oppottunity,
                                                               id_user,
                                                               id_contact,
                                                               attached,
                                                               create_date,
                                                               amount
                                                               )VALUES(
                                                                1,
                                                                '".$_POST['email']."',
                                                                '".$_POST['mensaje']."',
                                                                '".$_POST['nota']."',
                                                                '".$_POST['optionid']."',
                                                                '".$_SESSION['USER_ID']."',
                                                                '".$_POST['contactid']."',
                                                                '".$filenameThumb."',
                                                                '".date("Y-m-d H:i:s")."',
                                                                '".$_POST['monto_propuesta']."')");
                          }
     /* --------- */
     //$message="";
    if(isset($_GET['id']))
     {
        $arrUser = GetRecords("SELECT opportunity.*,
                                business.Name as bizname,
                                business.Logo as logo,
                                contact.Email as Email,
                                contact.Phone as Phone,
                                contact.Name as contactname,
                                contact.Image as ContactImage,
                                stage.Name_stage as stage,
                                contact.id as conid
                                from opportunity
                                 inner join contact on contact.id  = opportunity.id_contact
                                 inner join business on business.id  = contact.id_business
                                 inner join stage on stage.id  = opportunity.id_stage
                                 WHERE opportunity.id = ".$_GET['id']."
                                 ");
     }
     $userrefid = $arrUser[0]['id_ref_master_note'];

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <?php
                                if($message !="")
                                    echo $message;
                          ?>
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" data-validate="parsley" novalidate action="edit-opportunity-step1.php?id=<?php echo $_GET['id']?>" method="post">
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Detalle de la Oportunidad</span>
                          <?php if(isset($update_next_step) || isset($nId) || isset($insert_propuesta)){ ?>

                          <div class="alert alert-success">
                              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                              <strong>Realizado con éxito</strong>
                          </div>

                          <?php } ?>
                        </header>
                        <div class="panel-body">
                          <div class="row m-b-lg">
                            <div class="col-lg-12 m-b-sm font-bold ">
                              <div class="col-lg-3">Nombre de oportunidad</div>
                              <div class="col-lg-3">Monto</div>
                              <div class="col-lg-3">Estado</div>
                              <div class="col-lg-3">Fecha estimada de cierre</div>
                            </div>
                            <div class="col-lg-12">
                              <div class="col-lg-3 b-b"><?php echo $arrUser[0]['name'] ?></div>
                              <div class="col-lg-3 b-b"><?php echo number_format($arrUser[0]['estimated_amount'], 2, ',', '');?></div>
                              <div class="col-lg-3 b-b"><?php echo utf8_encode($arrUser[0]['stage']) ?></div>
                              <div class="col-lg-3 b-b"><?php echo $arrUser[0]['closing_date'] ?></div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-3" style="padding-right: 0px;">
                              <div style="width:200px;
                                          height:200px;
                                          background-color: #cccccc;
                                          border: solid 2px gray;
                                          margin: 5px;">
                                      <img src="<?php echo $arrUser[0]['logo']?>" width="200" />
                              </div>
                            </div>
                            <div class="col-lg-4" style="padding-left: 0px;">
                              <p><h1><?php echo $arrUser[0]['contactname'] ?></h1></p>
                              <p><h4><?php echo $arrUser[0]['bizname'] ?></h4></p>
                              <p><?php echo $arrUser[0]['Phone'] ?></p>
                              <p><?php echo $arrUser[0]['Email'] ?></p>

                              <?php if($arrUser[0]['id_user_register']!=$_SESSION['USER_ID'] || $loggdUType != "Admin" ){}else{ ?>
                              <p><button type="button" onclick="window.location='edit-contact.php?id=<?php echo $arrUser[0]['conid']?>';" class="btn green btn-info">Editar Contacto</button></p>
                              <?php } ?>
                              <?php /* Agregado variables */ ?>
                              <?php $email_contacto = $arrUser[0]['Email']; ?>
                              <?php $id_contacto = $arrUser[0]['id']; ?>
                            </div>

                            <div class="col-lg-3 text-right">
                              <p><h3>Próximo Paso</h3></p>
                               <p class="label label-success">

                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from next_step where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['detail'];
                                  if($arrUser[0]['nextstep']==$kinId){ echo utf8_encode($kinDesc);} } ?>
                                </p>
                              <p><?php echo $arrUser[0]['nextstep_date']; ?></p>
                            </div>
                            <div class="col-lg-12 text-right" id="more">
                              <button type="button" onclick="showOppForm(1)" class="btn green btn-link">Más Detalles</button>
                            </div>
                            <div class="col-lg-12 text-right" style="display: none;" id="less">
                              <button type="button" onclick="showOppForm(2)" class="btn green btn-link">Ocultar</button>
                            </div>
                          </div>
                      </section>
                    </form>
                    <?php /* Variables agregadas */ ?>
                    <?php $fecha_proximo_paso = $arrUser[0]['nextstep_date']; ?>
                    <?php $id_proximo_paso = $arrUser[0]['nextstep']; ?>
                    <?php $id_oportunidad = $arrUser[0]['id']; ?>
                    <?php $id_stage = $arrUser[0]['id_stage']; ?>
                    <?php $id_usuario = $arrUser[0]['id_user_register']; ?>
                    <?php
                      $arrUser = GetRecord("opportunity", "id = ".$_REQUEST['id']);
                    ?>
                    <form class="form-horizontal opportunityname" style="display: none;" data-validate="parsley" method="post" action="opportunity-view.php?id=<?php echo $arrUser['id']?>" >
                            <section class="panel panel-default">
                              <div class="panel-body">

                                <div class="form-group required">
                                  <label class="col-lg-4 text-right control-label font-bold">Seleccionar Contacto</label>
                                  <div class="col-lg-4">
                                    <?php

                                    $where = "";
                                     if($loggdUType != "Admin")
                                     {
                                       $where.=" and id_user_register=". $_SESSION['USER_ID'];
                                     }

                                     $comprobar_usuario = GetRecords("Select * from contact where id = '".$arrUser['id_contact']."'");
                                     foreach ($comprobar_usuario as $key => $comprobar) {
                                          if($comprobar['id_user_register']==$_SESSION['USER_ID']){
                                             $arrKindMeetings = GetRecords("Select * from contact where stat = 1 $where");
                                          }else{
                                            $arrKindMeetings = GetRecords("Select * from contact where stat = 1");
                                            $solo_lectura=1;
                                          }
                                     }

                                     ?>
                                    <select style="width: 400px;" class="chosen-select form-control" name="contact" required="required">
                                      <option value="">----------------</option>
                                      <?PHP
                                      foreach ($arrKindMeetings as $key => $value) {
                                        if(isset($solo_lectura)){
                                          if($arrUser['id_contact']!=$value['id']){
                                            continue;
                                          }
                                        }
                                        $kinId = $value['id'];
                                        $kinDesc = $value['Name'];
                                        $selRoll = (isset($arrUser['id_contact']) && $arrUser['id_contact'] == $kinId) ? 'selected' : ''; ?>
                                      <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                                      <?php
                                  }
                                      ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group required">
                                  <label class="col-lg-4 text-right control-label font-bold">Nombre de Oportunidad</label>
                                  <div class="col-lg-4">
                                    <input type="text" class="form-control" placeholder="Name Opportunity" value="<?php echo $arrUser['name']?>" name="name" data-required="true">
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                                  <div class="col-lg-4">
                                    <textarea rows="3" class="form-control" cols="44" name="description" placeholder="Description"><?php echo $arrUser['description']?></textarea>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Categoría</label>
                                  <div class="col-lg-4">
                                    <select class="form-control" name="category" id="category">
                                              <option value="">------------------</option>
                                              <?PHP
                                              $catQuery = "Select * from category WHERE stat = 1 ";

                                              $arrKindMeetings = GetRecords($catQuery);
                                              foreach ($arrKindMeetings as $key => $value) {
                                                $kinId = $value['id'];
                                                $kinDesc = $value['name'];
                                              ?>
                                              <option value="<?php echo $kinId?>" <?php if($arrUser['id_category']==$kinId){ echo "selected";}  ?>><?php echo $kinDesc?></option>
                                              <?php
                                            }
                                              ?>
                                        </select>
                                  </div>
                                </div>
                                <div class="form-group required">
                                  <label class="col-lg-4 text-right control-label font-bold">Monto Estimado</label>
                                  <div class="col-lg-4">
                                    <input type="text" class="form-control" placeholder="Estimated Amount" value="<?php echo $arrUser['estimated_amount']?>" name="estimatedamount" data-required="true">
                                  </div>
                                </div>
                                <div class="form-group required">
                                  <label class="col-lg-4 text-right control-label font-bold">Seleccionar Fuente</label>
                                  <div class="col-lg-4">
                                     <select class="chosen-select form-control" name="source" data-required="true">
                                      <option value="">----------------</option>
                                      <?PHP
                                      $where = "";

                                      $arrKindMeetings = GetRecords("Select * from source where status = 1 $where");
                                      foreach ($arrKindMeetings as $key => $value) {
                                        $kinId = $value['id'];
                                        $kinDesc = $value['Name_source'];
                                        $selRoll = (isset($arrUser['source']) && $arrUser['source'] == $kinId) ? 'selected' : '';
                                      ?>
                                      <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo utf8_encode($kinDesc)?></option>
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
                                        <option value="<?php echo $kinId?>" <?php if($arrUser['nextstep']==$kinId){ echo "selected";} ?> ><?php echo utf8_encode($kinDesc)?></option>
                                        <?php
                                    }
                                        ?>
                                      </select>

                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Fecha del Proximo Paso</label>
                                  <div class="col-lg-4">
                                    <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker" name="nextstepdate" size="16" readonly="" type="text" value="<?php if($arrUser['nextstep_date']!="0000-00-00"){ echo $arrUser['nextstep_date'];} ?>" data-date-format="yyyy-mm-dd" >
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Fecha de Preparacion de Propuesta</label>
                                  <div class="col-lg-4">
                                    <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker1" name="preparationdate" size="16" readonly="" type="text" value="<?php if($arrUser['preparation_date']!="0000-00-00"){ echo $arrUser['preparation_date'];}?>" data-date-format="yyyy-mm-dd" >
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Fecha de entrega de propuesta</label>
                                  <div class="col-lg-4">
                                    <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker2" name="proposaldate" size="16" readonly="" type="text" value="<?php if($arrUser['proposal_date']!="0000-00-00"){ echo $arrUser['proposal_date'];}?>" data-date-format="yyyy-mm-dd" >
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Hora de Entrega de Propuesta</label>
                                  <div class="col-lg-4">
                                    <?php $fecha=date("Y-m-d",strtotime($arrUser['proposal_date'])); ?>
                                    <?php $hora=date("H:i",strtotime($arrUser['proposal_date'])); ?>
                                    <select class="form-control" name="hour" required="required" >
                    	                        <option value="">Seleccionar</option>
                                              <?php $cero = 0; ?>
                                              <?php $minu_0 = "00"; ?>
                                              <?php $minu_3 = 30; ?>
                                              <?PHP for($i=0;$i<=23;$i++){
                                                    if($i==0 || $i==1 || $i==2 || $i==3 || $i==4){
                                                      continue;
                                                    }
                                                    if(strlen($i) == 1){
                                                    $hora_select = $cero.$i.":".$minu_0;
                                                    $hora_select_3 = $cero.$i.":".$minu_3;
                                                  }else{
                                                    $hora_select = $i.":".$minu_0;
                                                    $hora_select_3 = $i.":".$minu_3;
                                                  } ?>
                    	                        <option value="<?php if(strlen($i) == 1){ echo "0".$i.":00"; }else{ echo $i.":00"; } ?>" <?php if($hora==$hora_select){ echo "selected";} ?> ><?php if(strlen($i) == 1){ echo "0".$i.":00"; }else{ echo $i.":00"; } ?></option>
                                              <option value="<?php if(strlen($i) == 1){ echo "0".$i.":30"; }else{ echo $i.":30"; } ?>" <?php if($hora==$hora_select_3){ echo "selected";} ?> ><?php if(strlen($i) == 1){ echo "0".$i.":30"; }else{ echo $i.":30"; } ?></option>
                    	                        <?php } ?>
                    	                </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Tiempo Estimado de Cierre</label>
                                  <div class="col-lg-4">
                                    <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker3" name="closingdate" size="16" readonly="" type="text" value="<?php if($arrUser['closing_date']!="0000-00-00"){ echo $arrUser['closing_date'];}?>" data-date-format="yyyy-mm-dd" >
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-lg-4 text-right control-label font-bold">Seleccionar Etapa</label>
                                  <div class="col-lg-4">
                                     <select class="chosen-select form-control" name="stage" data-required="true">
                                      <option value="">----------------</option>
                                      <?PHP
                                      $arrKindMeetings = GetRecords("Select * from stage where status = 1");
                                      foreach ($arrKindMeetings as $key => $value) {
                                        $kinId = $value['id'];
                                        $kinDesc = $value['Name_stage'];
                                        $selRoll = (isset($id_stage) && $id_stage == $kinId) ? 'selected' : '';
                                      ?>
                                      <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo utf8_encode($kinDesc)?></option>
                                      <?php
                                  }
                                      ?>
                                    </select>
                                  </div>
                                </div>


                                <div class="form-group">
                                  <label class="col-lg-4 font-bold control-label">Activa/Desactiva</label>
                                  <div class="col-lg-4">
                                    <select class="chosen-select form-control" name="status" data-required="true">
                                      <?php
                                      $arrStatus = array("1" => "Active", "2" => "Pending", "3" => "Won", "4" => "Lost", "5" => "Cancelled", "6" => "Finished");
                                      foreach ($arrStatus as $key => $value) {
                                        $selRoll = (isset($arrUser['stat']) && $arrUser['stat'] == $key) ? 'selected' : ''
                                      ?>
                                        <option value="<?php echo $key?>" <?php echo $selRoll?>><?php echo $value?></option>
                                      <?php
                                      }
                                      ?>
                                     </select>
                                  </div>
                                </div>

                              </div>
                              <footer class="panel-footer text-right bg-light lter">
                                <button type="submit" name="updateOpportunity" class="btn btn-primary btn-s-xs">Update</button>
                              </footer>
                            </section>
                          </form>
                    <?php

                      //include("notes-attached.php");
                    ?>
                    <section class="panel panel-default">
                      <header class="panel-heading">
                        <span><a href="modal-notes.php" data-toggle="ajaxModal" class="btn btn-sm btn-primary">Nota</a></span>
                        <span><a href="modal-notes.php?call=1" data-toggle="ajaxModal" class="btn btn-sm btn-warning">Nota de Llamada</a></span>
                        <?php /* ?><span><a href="modal-option.php" data-toggle="ajaxModal" class="btn btn-sm btn-primary">Opciones</a></span> */ ?>
                        <?php $fecha=date("Y-m-d",strtotime($fecha_proximo_paso)); ?>
                        <?php $hora=date("H:i",strtotime($fecha_proximo_paso)); ?>
                        <a href="modal-next-step.php?next_step=<?php echo $id_oportunidad?>&next_step_id=<?php echo $id_proximo_paso?>&fecha=<?php echo $fecha; ?>&hora=<?php echo $hora; ?>&id_master_note=<?php echo $userrefid?>" title="Proximo Paso" data-toggle="ajaxModal" class="btn btn-sm btn-success">Próximo Paso</a>
                        <span><a href="modal-etapa.php?etapa=<?php echo $id_oportunidad?>&source=<?php echo $id_stage; ?>" title="Etapa" data-toggle="ajaxModal" class="btn btn-sm btn-default">Etapa</a></span>
                        <?PHP if($loggdUType == "Admin") :  ?>
                        <span><a href="modal-usuario.php?usuario=<?php echo $id_usuario?>&oportunidad_id=<?php echo $id_oportunidad?>" title="Asignar un usuario" data-toggle="ajaxModal" class="btn btn-sm btn-danger">Usuario</a></span>
                        <?php endif; ?>
                      </header>
                      <div class="panel-body">
                          <?php
                          /*  $arrOpt = GetRecords("SELECT opportunity_option.*, users.Name from opportunity_option
                                                  inner join users on users.id = opportunity_option.createdBy
                                                  where id_opportunity = ".$_GET['id']."
                             ");

                            $optindx=1;
                            foreach ($arrOpt as $key => $value) {

                                $optindx++;
                              } */
                            ?>
                              <div class="col-lg-12   no-padder b-b">
                                <div class="col-lg-12 bg-light  " style="padding-top: 10px; padding-bottom: 10px; padding-left: 0px; padding-right: 0px;">

                                  <div class="col-lg-12 no-padder ">
                                    <span class="padder font-bold"></span>
                                    <span class="m-l-sm">
                                      <a href="modal-opportunitydetail.php?oppid=<?php echo $id_oportunidad?>&id_contact=<?php echo $id_contacto;?>&correo_contacto=<?php echo $email_contacto; ?>" data-toggle="ajaxModal" class="btn btn-sm btn-primary">Nueva Propuesta</a>

                                    </span>
                                    <span class="m-l-xs pull-right m-r-sm">
                                    </span>
                                  </div>
                                </div>

                                <div class="col-lg-12 m-b-sm">
                                  <div class="table-responsive">
                                    <?php include("propuestas.php"); ?>
                                 </div>
                                </div>
                              </div>

                        </div>
                      </div>

                      <?php
                        //$userrefid = $arrUser[0]['id_ref_master_note'];
                        include("notes-opportunity.php");
                      ?>
                    </section>

                  </div>
              </div>

            </section>

        </section>


    </section>
<?php
	include("footer.php");
?>
