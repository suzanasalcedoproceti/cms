<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=2;
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
	<? $tit='Administrar Subategorías'; include('top.php'); ?>
    <form action="lista_subcategoria.php" method="post" name="brinca" id="brinca">
      <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>">
      <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>">
    </form>
	<div class="main">
      <p>
      
		<?
                include('../conexion.php');

				$subcategoria=$_POST['subcategoria'];
				if (empty($subcategoria)) $subcategoria=$_GET['subcategoria'];
				$autorizar=$_POST['autorizar'];
				if (empty($autorizar)) $autorizar=$_GET['autorizar'];
				
				$error=FALSE;

				// comenzar transaccción	
				$resultado = mysql_query("SET AUTOCOMMIT = 0");
				$resultado = mysql_query("START TRANSACTION");
				

                if (!empty($subcategoria) AND empty($autorizar)) {  // si no está en proceso de autorización

                    $resultado= mysql_query("DELETE FROM subcategoria WHERE clave = '$subcategoria'" ,$conexion);
                      $totalRegistros = mysql_affected_rows() ;
                      if ($totalRegistros > 0) {
                        $mensaje='Registro eliminado.';
						
				        echo '<script languaje="JavaScript">';
					    echo '  document.brinca.submit(); ';
					    echo '</script>';
						 						
					  }
                      else {
                        $error=TRUE; 
                        $mensaje='No se eliminó el Registro.';
					  }
				
				} 
				
				elseif (!empty($subcategoria) AND !empty($autorizar))  {  // si está en proceso de autorización


				    $resultado= mysql_query("SELECT * FROM subcategoria WHERE clave='$subcategoria'",$conexion);
					$row = mysql_fetch_array($resultado);
					
					$original=$row['original'];

           		    $resultado= mysql_query("UPDATE subcategoria SET estatus=1 WHERE clave='$original'",$conexion);


                    $resultado= mysql_query("DELETE FROM subcategoria WHERE clave = '$subcategoria'" ,$conexion);
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
