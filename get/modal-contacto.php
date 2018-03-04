<?php

    //ob_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    include("include/config.php");
    include("include/defs.php");
    $loggdUType = current_user_type();
    $loggdURegion = current_user_region();
    //$loggdUType = current_user_type();


    /*if(isset($_GET['optid']) && $_GET['optid'] > 0)
    {
    	$getNote = GetRecord("opportunity_option", "id=".$_GET['optid']);
    	$optname = $getNote['optionName'];
    	$optdesc = $getNote['description'];
    	$optid = $_GET['optid'];
    	$htext = "Edit";
    }
    else
    {
    	$optname = "";
    	$optdesc = "";
    	$optid = "";
    	$htext = "Add";
    }*/
?>

<div class="modal-dialog">
  <div class="modal-content">
  	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">

	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Agregar un Contacto</h4>
	    </div>
	    <div class="modal-body">
	      <div class="row">
		      <div class="form form-horizontal">
            <section id="content">
                    <section class="vbox">
                    <header class="panel-heading">
                    </header>
                    <div class="panel-body">

                      <?php

                      if($_GET['empresa']!=0){
                         $id_empre = $_GET['empresa'];
                      }else{

                      }

                       ?>

                      <div class="form-group required">
                        <label class="col-lg-4 text-right control-label font-bold">Seleccionar Empresa</label>
                        <div class="col-lg-7">
                          <select class="chosen-select form-control" name="busid" required="required" <?php if(isset($id_empre)){ echo 'readonly';} ?>>
                            <option value="">----------------</option>
                            <?PHP

                            $where = "";
                             if($loggdUType != "Admin")
                             {
                               $where.=" and id_user_register=". $_SESSION['USER_ID'];
                             }
                            $arrKindMeetings = GetRecords("Select * from business where stat = 1 $where");
                            foreach ($arrKindMeetings as $key => $value) {
                              $kinId = $value['id'];
                              $kinDesc = $value['Name'];
                              $selRoll = (isset($id_empre) && $id_empre == $kinId) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $kinId?>" <?php echo $selRoll?>><?php echo $kinDesc?></option>
                            <?php
                        }
                            ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-lg-4 text-right control-label font-bold">Nombre</label>
                        <div class="col-lg-7">
                          <input type="text" class="form-control" placeholder="Name" name="name" data-required="true">
                        </div>
                      </div>
                      <div class="form-group ">
                          <label class="col-lg-4 text-right control-label font-bold">Imagen</label>
                          <div class="col-lg-7">
                              <input type="file" name="logo" class="form-control">
                            </div>
                      </div>
                      <div class="form-group required">
                        <label class="col-lg-4 text-right control-label font-bold">Teléfono</label>
                        <div class="col-lg-7">
                          <input type="text" class="form-control" placeholder="Phone" name="phone" data-required="true">
                        </div>
                      </div>
                      <div class="form-group ">
                        <label class="col-lg-4 text-right control-label font-bold">Email</label>
                        <div class="col-lg-7">
                          <input type="email" class="form-control" placeholder="Email" name="email">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-4 text-right control-label font-bold">Descripción</label>
                        <div class="col-lg-7">
                          <textarea rows="3" class="form-control" cols="44" name="business_description" placeholder="Description"></textarea>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-lg-4 text-right control-label font-bold">Posición en la Empresa</label>
                        <div class="col-lg-7">
                          <input type="text" class="form-control" placeholder="Position in the Business" name="position">
                        </div>
                      </div>
                    </div>
              </section>
			  </div>

		  </div>
	    </div>
	    <div class="modal-footer">
	      <a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
	      <button type="submit"   name="submitContac" class="btn btn-primary">Ok</button>
	    </div>
    </form>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
