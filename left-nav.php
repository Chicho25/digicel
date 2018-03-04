<!-- .aside -->
        <aside class="bg-black aside-md hidden-print hidden-xs" id="nav">
          <section class="vbox">
            <section class="w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="10px" data-railOpacity="0.2">
                <div class="clearfix wrapper dk nav-user hidden-xs">
                  <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <span class="thumb avatar pull-left m-r">
                        <?php
                          $getuserDetail = GetRecord("users", "id =".$_SESSION['USER_ID']);

                          if(isset($getuserDetail['Image']) && $getuserDetail['Image'] != '')
                            $uimage = $getuserDetail['Image'];
                          else
                            $uimage = "images/p0.jpg";
                        ?>
                        <img src="<?php echo $uimage;?>" class="dker" alt="...">
                        <i class="on md b-black"></i>
                      </span>
                      <span class="hidden-nav-xs clear">
                        <span class="block m-t-xs">
                          <strong class="font-bold text-lt"><?php echo $_SESSION['USER_NAME']?></strong>
                          <b class="caret"></b>
                        </span>
                        <span class="text-muted text-xs block"></span>
                      </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                      <li>
                        <a href="logout.php">Salir</a>
                      </li>
                    </ul>
                  </div>
                </div>
                <!-- nav -->
                <nav class="nav-primary hidden-xs">
                  <div class="text-muted text-sm hidden-nav-xs padder m-t-sm m-b-sm">Inicio </div>
                  <ul class="nav nav-main" data-ride="collapse">
                    <?php if($_SESSION['TYPE_USER'] == 1) : ?>
                    <li <?php if(isset($userclass)) echo $userclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Usuarios</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($registerclass)) echo $registerclass;?>>
                          <a href="register_user.php"><i class="i i-dot"></i>
                            <span>Registrar Usuarios</span>
                          </a>
                        </li>
                        <li <?php if(isset($userlistclass)) echo $userlistclass;?>>
                          <a href="users_view.php"><i class="i i-dot"></i>
                            <span>Ver Usuarios</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <?php endif;?>
                    <?php if($_SESSION['TYPE_USER'] == 1 || $_SESSION['TYPE_USER'] == 2) : ?>
                    <li <?php if(isset($fpiclass)) echo $fpiclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Imprimir</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($fpiregtclass)) echo $fpiregtclass;?>>
                          <a href="registrar_factura.php"><i class="i i-dot"></i>
                            <span>Crear Facturas</span>
                          </a>
                        </li>
                        <li <?php if(isset($fpilistclass)) echo $fpilistclass;?>>
                          <a href="facturas_por_imprimir.php"><i class="i i-dot"></i>
                            <span>Facturas por Imprimir</span>
                          </a>
                        </li>
                        <li <?php if(isset($rfacimpreclass)) echo $rfacimpreclass;?>>
                          <a href="facturas_impresas.php"><i class="i i-dot"></i>
                            <span>Historial Facturas</span>
                          </a>
                        </li>
                        <li <?php if(isset($cxtclass)) echo $cxtclass;?>>
                          <a href="cinta_xy.php"><i class="i i-dot"></i>
                            <span>Cinta X & Z</span>
                          </a>
                        </li>
                        <li <?php if(isset($ncclass)) echo $ncclass;?>>
                          <a href="nota_credito.php"><i class="i i-dot"></i>
                            <span>Nota de Credito</span>
                          </a>
                        </li>
                        <li <?php if(isset($hncclass)) echo $hncclass;?>>
                          <a href="notas_credito_empresas.php"><i class="i i-dot"></i>
                            <span>Historial Notas de Credito</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <?php endif;?>

                    <li <?php if(isset($catalogoclass)) echo $catalogoclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Catalogo</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($catalogolistclass)) echo $catalogolistclass;?>>
                          <a href="catalogo.php"><i class="i i-dot"></i>
                            <span>Ver Catalogo</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li <?php if(isset($reportclass)) echo $reportclass;?>>
                      <a href="#table" class="auto">
                        <span class="pull-right text-muted">
                          <i class="i i-circle-sm-o text"></i>
                          <i class="i i-circle-sm text-active"></i>
                        </span>
                        <i class="i i-dot"></i>
                        <span>Reporte</span>
                      </a>
                      <ul class="nav dker">
                        <li <?php if(isset($reportlistclass)) echo $reportlistclass;?>>
                          <a href="report.php"><i class="i i-dot"></i>
                            <span>Ver Reporte</span>
                          </a>
                        </li>
                      </ul>
                    </li>
              </ul>
            </nav>
          </div>
        </section>
        <footer class="footer hidden-xs no-padder text-center-nav-xs">
          <a href="modal.lockme.html" data-toggle="ajaxModal" class="btn btn-icon icon-muted btn-inactive pull-right m-l-xs m-r-xs hidden-nav-xs">
            <i class="i i-logout"></i>
          </a>
          <a href="#nav" data-toggle="class:nav-xs" class="btn btn-icon icon-muted btn-inactive m-l-xs m-r-xs">
            <i class="i i-circleleft text"></i>
            <i class="i i-circleright text-active"></i>
          </a>
        </footer>
      </section>
    </aside>
