<?php
    ob_start();
    session_start();
    $userclass="class='active'";
    $userlistclass="class='active'";

    if(!isset($_SESSION['USER_ID']))
     {
          header("Location: index.php");
          exit;
     }

    include("include/config.php");
    include("include/defs.php");

    if(isset($_POST['submitUsuario'])){

      if(isset($_POST['stat'])){ $stat = 1; }else{ $stat = 2; }

      $arrUser = array("user_name"=>$_POST['user'],
                       "Name"=>$_POST['name'],
                       "last_name"=>$_POST['last_name'],
                       "type_user"=>$_POST['roll'],
                       "stat"=>$stat,
                       "Email"=>$_POST['email']);

      UpdateRec("users", "id = ".$_POST['id_user'], $arrUser);

      $message = '<div class="alert alert-success">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Usuario Modificado</strong>
                  </div>';
    }

    if(isset($_POST['submitPass'])){

      if(isset($_POST['stat'])){ $stat = 1; }else{ $stat = 2; }

      $arrUser = array("pass"=>encryptIt($_POST['pass']));

      UpdateRec("users", "id = ".$_POST['id_user'], $arrUser);

      $message = '<div class="alert alert-success">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>Password Modificada!</strong>
                  </div>';
    }

    include("header.php");
    $where = "where (1=1)";

     if(isset($_POST['name_fitro']) && $_POST['name_fitro'] != "")
     {
        $where.=" and  users.Name LIKE '%".$_POST['name_fitro']."%'";
        $name = $_POST['name_fitro'];
     }
     if(isset($_POST['lname_fitro']) && $_POST['lname_fitro'] != "")
     {
        $where.=" and  users.Last_name LIKE '%".$_POST['lname_fitro']."%'";
        $lname = $_POST['lname_fitro'];
     }
     if(isset($_POST['user_fitro']) && $_POST['user_fitro'] != "")
     {
        $where.=" and  users.user_name LIKE '%".$_POST['user_fitro']."%'";
        $user = $_POST['user_fitro'];
     }

      $arrUser = GetRecords("select
                              users.id,
                              users.Name,
                              users.last_name,
                              users.user_name,
                              users.photo,
                              users.Email,
                              users.date_create,
                              users.type_user,
                              type_user.descriptions as tipo_usuario,
                              master_stat.descriptions as stat,
                              users.stat as stat_id
                              from users inner join type_user on users.type_user = type_user.id
                              		       inner join master_stat on users.stat = master_stat.id
                             $where
                             order by Name");

?>
	<section id="content">
          <section class="vbox">
            <section class="scrollable padder">
              <section class="panel panel-default">
                <?php
                      if($message !="")
                          echo $message;
                ?>
                <header class="panel-heading">
                          <span class="h4">Lista de Usuarios</span>
                </header>
                <div class="panel-body">
                    <form method="post">
                      <div class="row wrapper">
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($name)){ echo $name;}?>" name="name_fitro" placeholder="Nombre">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($lname)){ echo $lname;}?>" name="lname_fitro" placeholder="Apellido">
                          </div>
                        </div>
                        <div class="col-sm-2 m-b-xs">
                          <div class="input-group">
                            <input type="text" class="input-s input-sm form-control" value="<?php if(isset($user)){ echo $user;}?>" name="user_fitro" placeholder="Usuario">
                          </div>
                        </div>
                        <div class="col-sm-3 m-b-xs">
                          <div class="input-group">
                            <span class="input-group-btn padder "><button class="btn btn-sm btn-default">Buscar</button></span>
                          </div>
                        </div>
                      </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped b-t b-light" data-ride="datatables">
                          <thead>
                            <tr>
                              <th>ID</th>
                              <th>NOMBRE</th>
                              <th>APELLIDO</th>
                              <th>EMAIL</th>
                              <th>TIPO DE USUARIO</th>
                              <th>ESTATUS</th>
                              <th>ACCIÓN</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?PHP
                            foreach ($arrUser as $key => $value) {
                            ?>
                          <tr>
                              <td class="tbdata"> <?php echo $value['id']?> </td>
                              <td class="tbdata"> <?php echo $value['Name']?> </td>
                              <td class="tbdata"> <?php echo $value['last_name']?> </td>
                              <td class="tbdata"> <?php echo $value['Email']?> </td>
                              <td class="tbdata"> <?php echo $value['tipo_usuario']?> </td>
                              <td class="tbdata"> <?php echo $value['stat']?> </td>
                              <td>
                                <button title="Editar" type="button" class="btn green btn-info" data-toggle="modal" data-target="#myModal<?php echo $value['id']?>"><li class="glyphicon glyphicon-pencil"></li></button>
                                <button title="Cambiar contrasena" type="button" class="btn danger btn-danger" data-toggle="modal" data-target="#myModalPass<?php echo $value['id']?>"><li class="glyphicon glyphicon-retweet"></li></button>
                              </td>
                          </tr>
                          <!-- Modal Cambiar pass -->
                          <div id="myModalPass<?php echo $value['id']?>" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                            <div class="modal-content">
                            	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">
                          	    <div class="modal-header">
                          	      <button type="button" class="close" data-dismiss="modal">&times;</button>
                          	      <h4 class="modal-title">Cambiar Password</h4>
                          	    </div>
                          	    <div class="modal-body">
                          	      <div class="row">
                          		      <div class="form form-horizontal">
                          			      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Cambiar Password</label>
                                        <div class="col-lg-7">
                                          <input type="text" class="form-control" name="pass" value="">
                                        </div>
                                      </div>
                          			  </div>
                              </div>
                          	    <div class="modal-footer">
                          	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
                          	      <button type="submit" name="submitPass" class="btn btn-primary">Ok</button>
                                  <input type="hidden" name="id_user" value="<?php echo $value['id']?>">
                          	    </div>
                              </form>
                              </div>
                            </div>
                            </div><!-- /.modal-content -->
                          </div><!-- /.modal-dialog -->
                          <!-- Modal Editar-->
                          <div id="myModal<?php echo $value['id']?>" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                            <div class="modal-content">
                            	<form role="form" class="form-horizontal" id="role-form"  method="post" action="" enctype="multipart/form-data">
                          	    <div class="modal-header">
                          	      <button type="button" class="close" data-dismiss="modal">&times;</button>
                          	      <h4 class="modal-title">Editar usuario </h4>
                          	    </div>
                          	    <div class="modal-body">
                          	      <div class="row">
                          		      <div class="form form-horizontal">
                          			      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Usuario</label>
                                        <div class="col-lg-7">
                                          <input type="text" class="form-control" name="user" value="<?php echo $value['user_name']?>">
                                        </div>
                                      </div>
                                      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Nombre</label>
                                        <div class="col-lg-7">
                                          <input type="text" class="form-control" name="name" value="<?php echo $value['Name']?>">
                                        </div>
                                      </div>
                                      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Apellido</label>
                                        <div class="col-lg-7">
                                          <input type="text" class="form-control" name="last_name" value="<?php echo $value['last_name']?>">
                                        </div>
                                      </div>
                                      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Imagen</label>
                                        <div class="col-lg-7">
                                          <img src="<?php echo $value['photo']?>" alt="">
                                        </div>
                                      </div>
                                      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Rol</label>
                                        <div class="col-lg-7">
                                          <select class="chosen-select form-control" name="roll" required="required">
                                            <option value="">Seleccionar</option>
                                            <?PHP
                                                $arrKindMeetings = GetRecords("select * from type_user where stat = 1");
                                                foreach ($arrKindMeetings as $key => $value1) {
                                                  $kinId = $value1['id'];
                                                  $kinDesc = $value1['descriptions'];
                                                ?>
                                                <option value="<?php echo $kinId?>" <?php if(isset($value['type_user']) && $value['type_user']==$kinId){ echo "selected";} ?>><?php echo $kinDesc?></option>
                                                <?php
                                                }
                                                ?>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Email</label>
                                        <div class="col-lg-7">
                                          <input type="text" class="form-control" name="email" value="<?php echo $value['Email']?>">
                                        </div>
                                      </div>
                                      <div class="form-group required">
                                        <label class="col-lg-3 text-right control-label">Fecha de creacion</label>
                                        <div class="col-lg-7">
                                          <input type="text" class="form-control" readonly name="creation_date" value="<?php echo $value['date_create']?>">
                                        </div>
                                      </div>
                                        <div class="form-group">
                                          <label class="col-sm-3 text-right control-label">Estado</label>
                                          <div class="col-sm-7">
                                            <label class="switch">
                                              <input type="checkbox" <?php if($value['stat_id']==1){ echo 'checked';} ?> value="1" name="stat">
                                              <span></span>
                                            </label>
                                          </div>
                                        </div>
                          			      </div>
                          			  </div>
                              </div>
                          	    <div class="modal-footer">
                          	      <a href="#" class="btn btn-default" data-dismiss="modal">Cerrar</a>
                          	      <button type="submit" name="submitUsuario" class="btn btn-primary">Ok</button>
                                  <input type="hidden" name="id_user" value="<?php echo $value['id']?>">
                          	    </div>
                              </form>
                            </div><!-- /.modal-content -->
                          </div><!-- /.modal-dialog -->
                        </div>
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
