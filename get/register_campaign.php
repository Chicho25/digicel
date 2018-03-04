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

    if(isset($_POST['submitCampaign']))
     {
        $name_campaign = $_POST['name'];
        $date_start = $_POST['date_start'];
        $date_end = $_POST['date_end'];
        $user_assigned = $_POST['Assigned_to'];
        $category = $_POST['category'];
        $ifUserExist = RecCount("campaign", "name = '".$name_campaign."'");
        if($ifUserExist > 0)
        {
          $message = '<div class="alert alert-danger">
                      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Ya existe una campaña con el mismo nombre!</strong>
                      </div>';
        }
        else
        {
            $arrVal = array(
                          "name" => $name_campaign,
                          "date_start" => $date_start,
                          "date_end" => $date_end,
                          "user_assigned" => $user_assigned,
                          "category" => $category,
                          "stat" => 1,
                          "date_register" => date("Y-m-d H:i:s"),
                          "id_user_register" => $_SESSION['USER_ID']
                         );

          $nId = InsertRec("campaign", $arrVal);

          /* Log de Actividad */
          $mensaje = "Se ha registrado una Campaña. Campaña: '".$name_campaign."' ";
          log_actividad(9, 1, $_SESSION['USER_ID'], $mensaje);
          /* Fin de log de actividad */

          if($nId > 0)
          {

              MySQLQuery("Insert into master_notes (id) values (0)");
              $mstId = mysql_insert_id();

              if($mstId > 0)
              {
                $notemsg = "Nueva Campaña ".$name_campaign." Registrada por ".$_SESSION['USER_NAME'];
                $subj = "Campaña ( ".$name_campaign." )";
                create_log($mstId, $subj, $notemsg);
              }

              if(isset($_FILES['list_susp']) && $_FILES['list_susp']['tmp_name'] != "")
              {
                  $target_dir = "campaign/list/";
                  $target_file = $target_dir . basename($_FILES["list_susp"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["list_susp"]["tmp_name"], $filename))
                  {
                      /*makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);*/

                      UpdateRec("campaign", "id = ".$nId, array("list_suspects" => $filenameThumb));
                  }
              }

              if(isset($_FILES['content']) && $_FILES['content']['tmp_name'] != "")
              {
                  $target_dir = "campaign/content/";
                  $target_file = $target_dir . basename($_FILES["content"]["name"]);
                  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
                  $filename = $target_dir . $nId.".".$imageFileType;
                  $filenameThumb = $target_dir . $nId."_thumb.".$imageFileType;
                  if (move_uploaded_file($_FILES["content"]["tmp_name"], $filename))
                  {
                      /*makeThumbnailsWithGivenWidthHeight($target_dir, $imageFileType, $nId, 100, 100);*/

                      UpdateRec("campaign", "id = ".$nId, array("content_campaign" => $filenameThumb));
                  }
              }

              $message = '<div class="alert alert-success">
                          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Campaña Creada</strong>
                          </div>';
              }
          }
     }
?>

	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <div class="row">
                <div class="col-sm-12">
                	<form class="form-horizontal" action="" data-validate="parsley" method="post" enctype="multipart/form-data" novalidate>
                      <section class="panel panel-default">
                        <header class="panel-heading">
                          <span class="h4">Registro de Campaña</span>
                        </header>
                        <div class="panel-body">
                          <?php
                                if($message !="")
                                    echo $message;
                          ?>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Nombre de Campaña</label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" placeholder="Nombre de Campaña" name="name" data-required="true">
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Fecha de Inicio</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker3" name="date_start" size="16" readonly="readonly" data-required="true" type="text" data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Fecha Final</label>
                            <div class="col-lg-4">
                              <input class="input-sm input-s datepicker-input form-control datepicker" id="datepicker3" name="date_end" size="16" readonly="readonly" data-required="true" type="text" data-date-format="yyyy-mm-dd" >
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Asignado a</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="Assigned_to" required="required" onChange="mostrar(this.value);">
                                <option value="">--------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from users where id_roll_user = 5");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinName = $value['Name'];
                                  $kinLastName = $value['Last_name'];
                                ?>
                                <option value="<?php echo $kinId?>"><?php echo $kinName.' '.$kinLastName;?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="col-lg-4 text-right control-label font-bold">Categoria</label>
                            <div class="col-lg-4">
                              <select class="chosen-select form-control" name="category">
                                <option value="">--------------</option>
                                <?PHP
                                $arrKindMeetings = GetRecords("Select * from category where stat = 1");
                                foreach ($arrKindMeetings as $key => $value) {
                                  $kinId = $value['id'];
                                  $kinDesc = $value['name'];
                                ?>
                                <option value="<?php echo $kinId?>"><?php echo $kinDesc?></option>
                                <?php
                            }
                                ?>
                              </select>
                            </div>
                          </div>
                          <div class="form-group required">
                            <label class="col-lg-4 text-right control-label font-bold">Lista de Sospechosos</label>
                            <div class="col-lg-4">
                                <label class="btn yellow btn-default">
                                <input type="file" name="list_susp" required="required" accept=".xlsx">
                                </label>
                            </div>
                          </div>
                          <div class="form-group required">
                              <label class="col-lg-4 text-right control-label font-bold">Contenido de la campaña</label>

                                <div class="col-lg-4">
                                    <label class="btn yellow btn-default">
                                    <input type="file" name="content" required="required">
                                    </label>
                                </div>
                          </div>
                        <footer class="panel-footer text-right bg-light lter">
                          <button type="submit" onclick="document.getElementById('llamado').style.display='block'" name="submitCampaign" class="btn btn-primary btn-s-xs">Crear</button>

                        </footer>
                      </section>
                    </form>
                  </div>
              </div>
              <?php if(isset($_POST['submitCampaign'])){  ?>
              <table border="1" width="100%">
              	<thead>
                	  <tr>
                    	<th><center><strong>A</strong></center></th>
                        <th><center><strong>B</strong></center></th>
                        <th><center><strong>C</strong></center></th>
                        <th><center><strong>D</strong></center></th>
                  			<th><center><strong>E</strong></center></th>
                  			<th><center><strong>F</strong></center></th>
                    </tr>
                  	<tr>
                    	  <th>Empresa</th>
                        <th>Contacto Cliente</th>
                        <th>Cargo de la persona</th>
                        <th>Email del cliente</th>
                  			<th>Telefono Oficina</th>
                  			<th>Celular</th>
                    </tr>
              	</thead>
                  <tbody>
              <?php require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';
                    $find_file = GetRecords("Select list_suspects from campaign where id = $nId");
                    foreach ($find_file as $key => $value){
                    $rute = $value['list_suspects'];}
                    $rute_modified = str_replace("_thumb", "", $rute);
                    $objPHPExcel = PHPExcel_IOFactory::load($rute_modified);
              			$objHoja=$objPHPExcel->getActiveSheet()->toArray(false,true,true,true,true,true,true);
                    $numero_contar = 0;
                    foreach ($objHoja as $iIndice=>$objCelda) {
                      $numero_contar++;
                      if($numero_contar == 1 || $numero_contar == 2){
                         continue;
                      }
                      echo '
                        <tr>
                          <td>'.$objCelda['B'].'</td>
                          <td>'.$objCelda['C'].'</td>
                          <td>'.$objCelda['D'].'</td>
                          <td>'.$objCelda['E'].'</td>
                          <td>'.$objCelda['F'].'</td>
                          <td>'.$objCelda['G'].'</td>
                        </tr>';

                      $empresa=$objCelda['B'];
                      $contacto_cliente=$objCelda['C'];
                      $cargo=$objCelda['D'];
                      $email=$objCelda['E'];
                      $telefono_oficina=$objCelda['F'];
                      $telefono_celular=$objCelda['G'];

                      if(isset($nId)){
                      MySQLQuery("Insert into master_notes (id) values (0)");
                      $mnId = mysql_insert_id();
                      if($mnId > 0){
                        $notemsg = "Sospechoso Registrado: Registrada por ".$_SESSION['USER_NAME'];
                        $subj = "Sospechoso";
                        create_log($mnId, $subj, $notemsg);
                      }
                    }

                      $arrVal = array(
                                    "id_campaign" => $nId,
                                    "business" => $empresa,
                                    "customer_client" => $contacto_cliente,
                                    "position" => $cargo,
                                    "email" => $email,
                                    "phone_office" => $telefono_oficina,
                                    "cell_phone" => $telefono_celular,
                                    "date_create" => date("Y-m-d H:i:s"),
                                    "stat" => 1,
                                    "id_user_register" => $_SESSION['USER_ID'],
                                    "id_ref_master_note" => $mnId,
                                    "next_step" => 1,
                                    "date_next_step" => date("Y-m-d H:i:s")
                                   );
                            InsertRec("suspects", $arrVal);
                      }
                  } ?>
                </tbody>
              </table>
              <?php if(isset($numero_contar)){
                       echo "Se han registrado ".$numero_contar." Sospechosos";
                       UpdateRec("campaign", "id = ".$nId, array("total_listado" => $numero_contar)); } ?>
            </section>
        </section>
    </section>
<?php include("footer.php"); ?>
