<?
   session_cache_limiter('private, must-revalidate');
   session_start();
   if (empty($_SESSION['usr_valido'])) {
     include('login.php');
         return;
   }
   $foto = $_POST['foto'];
   $producto = $_POST['producto'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form action="photo_mgr.php" method="post" name="brinca" id="brinca">
  <input name="producto" type="hidden" id="producto" value="<?= $producto; ?>">
</form>

<?

// borra una celda de un arreglo
function array_unset($array,$index) {
  $res=array();
  $i=0;
  foreach ($array as $item) {
   if ($i!=$index)
     $res[]=$item;
   $i++;
  }
  return $res;
}

	if (!empty($producto) AND !empty($foto)) {

	  $borrar=substr($foto,1);
	
	  include('../conexion.php');
	  $resultado= mysql_query("SELECT * FROM producto WHERE clave='$producto'",$conexion);
	  $row = mysql_fetch_array($resultado);  

      $CR=chr(13).chr(10);
	  
	  $fotos = $row['fotos'];
	  $foto = explode($CR,$fotos);  
	
	  $imagen_a_borrar=$foto[$borrar];


      $foto=array_unset($foto,$borrar);
      $fotos='';
      for ($i=0; $i<count($foto); $i++) {
        $fotos.=$foto[$i].$CR;
      }
      $fotos=substr($fotos,0,strlen($fotos)-2);

      $res= mysql_query("UPDATE producto SET fotos='$fotos' WHERE clave='$producto'",$conexion);
      mysql_close();
  
      @unlink('images/cms/productos/'.$imagen_a_borrar);
      @unlink('images/cms/productos/small/'.$imagen_a_borrar);
      @unlink('images/cms/productos/medium/'.$imagen_a_borrar);

    }

      echo '<script languaje="JavaScript">';
      echo '  document.brinca.submit(); ';
      echo '</script>';


?>

</body>
</html>