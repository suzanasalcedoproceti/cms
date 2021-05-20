<?
   session_cache_limiter('private, must-revalidate');
   session_start();
   if (empty($_SESSION['usr_valido'])) {
     include('login.php');
         return;
   }
	$nfoto = $_GET['nfoto'];
	$producto = $_GET['producto'];
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
	if (!empty($producto) AND !empty($nfoto)) {

	  include('../conexion.php');
	  $resultado= mysql_query("SELECT * FROM producto WHERE clave='$producto'",$conexion);
	  $row = mysql_fetch_array($resultado);  

      $CR=chr(13).chr(10);

	  $fotos = $row['fotos'];
	  $foto = explode($CR,$fotos);  

      $string = $foto[$nfoto-1].$CR.$foto[$nfoto];
      $new_string = $foto[$nfoto].$CR.$foto[$nfoto-1];

      $fotos_new = str_replace($string,$new_string,$fotos);

	  $res= mysql_query("UPDATE producto SET fotos='$fotos_new' WHERE clave='$producto'",$conexion);

	  mysql_close();
	  

    }

      echo '<script languaje="JavaScript">';
      echo '  document.brinca.submit(); ';
      echo '</script>';


?>

</body>
</html>