<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=11;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'home.php';
		include('mensaje_sistema.php');
		return;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Stages'; include('top.php'); ?>
    <form action="lista_stage.php" method="post" name="brinca" id="brinca">
      <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>">
      <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>">
    </form>
	<div class="main">
      <p>
      
		<?
                include('../conexion.php');

				$stage=$_POST['stage'];
				if (empty($stage)) $stage=$_GET['stage'];
				$autorizar=$_POST['autorizar'];
				if (empty($autorizar)) $autorizar=$_GET['autorizar'];
				
				$error=FALSE;

				// comenzar transaccción	
				$resultado = mysql_query("SET AUTOCOMMIT = 0");
				$resultado = mysql_query("START TRANSACTION");
				

                if (!empty($stage) AND empty($autorizar)) {  // si no está en proceso de autorización

                    $resultado= mysql_query("DELETE FROM stage WHERE clave = '$stage'" ,$conexion);
                      $totalRegistros = mysql_affected_rows() ;
                      if ($totalRegistros > 0) {
                        $mensaje='Registro eliminado.';
						
						@unlink('images/cms/stages/'.$stage.'.jpg');

				        echo '<script languaje="JavaScript">';
					    echo '  document.brinca.submit(); ';
					    echo '</script>';
						 						
					  }
                      else {
                        $error=TRUE; 
                        $mensaje='No se eliminó el Registro.';
					  }
				
				} 
				
				elseif (!empty($stage) AND !empty($autorizar))  {  // si está en proceso de autorización


				    $resultado= mysql_query("SELECT * FROM stage WHERE clave='$stage'",$conexion);
					$row = mysql_fetch_array($resultado);
					
					$original=$row['original'];

           		    $resultado= mysql_query("UPDATE stage SET estatus=1 WHERE clave='$original'",$conexion);


                    $resultado= mysql_query("DELETE FROM stage WHERE clave = '$stage'" ,$conexion);
                      $totalRegistros = mysql_affected_rows() ;
                      if ($totalRegistros > 0) {
                        $mensaje='Registro eliminado.';
						
				        echo '<script languaje="JavaScript">';
						echo '  document.brinca.action="lista_autorizar.php"; ';
					    echo '  document.brinca.submit(); ';
					    echo '</script>';
						 						
					  }
                      else {
                        $error=TRUE; 
                        $mensaje='No se eliminó el Registro.';
					  }
				
				}

			    // revisar si hubo error o no
			    if ($error) mysql_query("ROLLBACK"); 
			    else mysql_query("COMMIT");
				if (!$mensaje) $mensaje = "Registro eliminado";

                mysql_close();
				
				$link='javascript:history.go(-1);';
            ?>
      
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
