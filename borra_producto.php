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
//	include("_checa_vars.php"); return;

function borra_match($path,$match){
	// funcion que borra recursivamente archivos que coincidan con el match
   static $deld = 0, $dsize = 0;
   $dirs = glob($path."*");
   $files = glob($path.$match);
   foreach($files as $file){
      if(is_file($file)){
         $dsize += filesize($file);
         unlink($file);
         $deld++;
      }
   }
   foreach($dirs as $dir){
      if(is_dir($dir)){
         $dir = basename($dir) . "/";
         borra_match($path.$dir,$match);
      }
   }
   return "$deld files deleted with a total size of $dsize bytes";
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
	<? $tit='Administrar Productos'; include('top.php'); ?>
    <form action="lista_producto.php" method="post" name="brinca" id="brinca">
      <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>">
      <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>">
    </form>
	<div class="main">
      <p>
      
		<?
                include('../conexion.php');

				$producto=$_POST['producto'];
				if (empty($producto)) $producto=$_GET['producto'];
				$autorizar=$_POST['autorizar'];
				if (empty($autorizar)) $autorizar=$_GET['autorizar'];
				
				$error=FALSE;

				// comenzar transaccción	
				$resultado = mysql_query("SET AUTOCOMMIT = 0");
				$resultado = mysql_query("START TRANSACTION");
				
                if (!empty($producto) AND empty($autorizar)) {  // si no está en proceso de autorización

                    $resultado= mysql_query("DELETE FROM producto WHERE clave = '$producto'" ,$conexion);
                      $totalRegistros = mysql_affected_rows() ;
                      if ($totalRegistros > 0) {
                        $mensaje='Registro eliminado.';

						borra_match("images/cms/productos/",$producto."_*.jpg");
						@unlink("images/cms/productos/pdf/".$producto.".pdf");
						@unlink("images/cms/productos/pdf/".$producto."_1.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_2.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_3.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_4.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_5.pdf");
						
				        echo '<script languaje="JavaScript">';
					    echo '  document.brinca.submit(); ';
					    echo '</script>';
						 						
					  }
                      else
                        $mensaje='No se eliminó el registro.';
				
				} 
				
				elseif (!empty($producto) AND !empty($autorizar))  {  // si está en proceso de autorización


				    $resultado= mysql_query("SELECT * FROM producto WHERE clave='$producto'",$conexion);
					$row = mysql_fetch_array($resultado);
					
					$original=$row['original'];

           		    $resultado= mysql_query("UPDATE producto SET estatus=1 WHERE clave='$original'",$conexion);


                    $resultado= mysql_query("DELETE FROM producto WHERE clave = '$producto'" ,$conexion);

                      $totalRegistros = mysql_affected_rows() ;
                      if ($totalRegistros > 0) {
                        $mensaje='Registro eliminado.';

						borra_match("images/cms/productos/",$producto."_*.jpg");
						@unlink("images/cms/productos/pdf/".$producto.".pdf");
						@unlink("images/cms/productos/pdf/".$producto."_1.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_2.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_3.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_4.pdf");
						@unlink("images/cms/productos/pdf/".$producto."_5.pdf");
						
				        echo '<script languaje="JavaScript">';
						echo '  document.brinca.action="lista_autorizar.php"; ';
					    echo '  document.brinca.submit(); ';
					    echo '</script>';
						 						
					  }
                      else
                        $mensaje='No se eliminó el Registro.';
				
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
