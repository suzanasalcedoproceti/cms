<?
    if (!include('ctrl_acceso.php')) return;

	include('funciones.php');
	$modulo=4;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	$producto = $_POST['producto'];
	$archivo_tmp = $_POST['archivo_tmp'];   

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
	<? $tit='Administrar Fotos de Productos'; include('top.php'); ?>
<form action="photo_mgr.php" method="post" name="brinca" id="brinca">
  <input name="producto" type="hidden" id="producto" value="<?= $producto; ?>">
</form>
	<div class="main">
      <p>
<?

	$link='photo_mgr.php';


    include("../conexion.php");
	$producto = $_POST['producto'];
	
    $resultado= mysql_query("SELECT * FROM producto WHERE clave='$producto'",$conexion);
    $row = mysql_fetch_array($resultado);


$CR=chr(13).chr(10);

// llena un arreglo con las fotos del producto
$fotos = $row['fotos'];
if (empty($fotos)){ $siguiente=$producto.'_1.jpg'; }
else {
  $foto = explode($CR,$fotos);
  // determinar nombre de foto
  $f=$producto.'_1.jpg';
  if (!in_array($f,$foto)){ $siguiente=$f;
  }else{
    for ($i=0; $i<=count($foto); $i++) {
      $f=$producto.'_'.($i+1).'.jpg';
      if (!in_array($f,$foto)) { $siguiente=$f; $i=count($foto); 
	  }
    }
  }
}

// mover archivo si es que existe
if (file_exists('./uploads/'.$archivo_tmp.'.jpg'))
	copy('uploads/'.$archivo_tmp.'.jpg','images/cms/productos/'.$siguiente);

$subido = false;
if (file_exists('images/cms/productos/'.$siguiente)) 
	$subido = true;

// elimina imagen temporal
@unlink ('./uploads/'.$archivo_tmp.'.jpg');

//////////////////////// subir foto //////////////////////////////////

if ($_FILES['imagen']['name']!="") {  // si hay imagen a subir
  if ($_FILES['imagen']['type']!="image/pjpeg" AND $_FILES['imagen']['type']!="image/jpeg"){ $mensaje='Sólo se pueden subir archivos JPG'; 
  }else{
    $nombrefoto='images/cms/productos/'.$siguiente;
    if (file_exists($nombrefoto)) unlink($nombrefoto);
	
    if (is_uploaded_file($_FILES['imagen']['tmp_name'])) {
      copy($_FILES['imagen']['tmp_name'], $nombrefoto);
	  $subido=TRUE; 
	
	  }
	else { $mensaje.='Problema al subir la foto';	$subido=FALSE; }
  }
} // si hay imagen a subir

if ($subido) {  // si se subió el archivo

  // procesa imágenes
  $imagen_original='images/cms/productos/'.$siguiente;
  $imagen_normal='images/cms/productos/'.$siguiente;
  $imagen_small='images/cms/productos/small/'.$siguiente;
  $imagen_medium='images/cms/productos/medium/'.$siguiente;
  $imagen_big='images/cms/productos/big/'.$siguiente;
  $img = imagecreatefromjpeg($imagen_original);
  if ($img)  { //  si existe la imagen

    // CREA LA IMAGEN NORMAL (260 x ? max)
    $ancho = imagesx($img);
    $alto  = imagesy($img);
    $ancho_maximo = 260;
	
	if ($ancho > $ancho_maximo) {
		$ancho_nuevo = $ancho_maximo;
        $alto_nuevo  = $alto * ($ancho_nuevo/$ancho); 
	} else {
		$ancho_nuevo = $ancho;
		$alto_nuevo = $alto;
	}
				
    //crear imagen grande (normal)
    $imagen_nueva_normal = imagecreatetruecolor($ancho_nuevo,$alto_nuevo);
    //redimensionar y copiar imagen
    imagecopyresampled($imagen_nueva_normal, $img, 0,0,0,0, $ancho_nuevo, $alto_nuevo, $ancho, $alto);
    //graba la imagen
	imagejpeg($imagen_nueva_normal, $imagen_normal);



    // CREA LA IMAGEN BIG (900 x ? max)
    $ancho = imagesx($img);
    $alto  = imagesy($img);
    $ancho_maximo = 900;
	
	if ($ancho > $ancho_maximo) {
		$ancho_nuevo = $ancho_maximo;
        $alto_nuevo  = $alto * ($ancho_nuevo/$ancho); 
	} else {
		$ancho_nuevo = $ancho;
		$alto_nuevo = $alto;
	}
				
    //crear imagen big (zoom)
    $imagen_nueva_big = imagecreatetruecolor($ancho_nuevo,$alto_nuevo);
    //redimensionar y copiar imagen
    imagecopyresampled($imagen_nueva_big, $img, 0,0,0,0, $ancho_nuevo, $alto_nuevo, $ancho, $alto);
    //graba la imagen
	imagejpeg($imagen_nueva_big, $imagen_big);


    // CREA LA IMAGEN MEDIA (108x98max)
    $ancho = imagesx($img);
    $alto  = imagesy($img);
    $ancho_maximo = 108;
    $alto_maximo = 98;
	$ancho_nuevo = $ancho;
	$alto_nuevo  = $alto;
	$proporcion = $ancho_maximo/$alto_maximo;
				
	if (($ancho/$alto)>$proporcion) {
	  if ($ancho>$ancho_maximo) {
		$ancho_nuevo = $ancho_maximo;
        $alto_nuevo  = $alto * ($ancho_nuevo/$ancho); }}
    else
	  if ($alto>$alto_maximo) {
		$alto_nuevo = $alto_maximo;
        $ancho_nuevo  = $ancho * ($alto_nuevo/$alto); }

    //crear imagen media
    $imagen_nueva_media = imagecreatetruecolor($ancho_nuevo,$alto_nuevo);
    //redimensionar y copiar imagen
    imagecopyresampled($imagen_nueva_media, $img, 0,0,0,0, $ancho_nuevo, $alto_nuevo, $ancho, $alto);
    //graba la imagen
	imagejpeg($imagen_nueva_media, $imagen_medium);


    // CREA LA IMAGEN MINIATURA (85x67 max)
    $ancho = imagesx($img);
    $alto  = imagesy($img);
    $ancho_maximo = 85;
    $alto_maximo = 67;
	$ancho_nuevo = $ancho;
	$alto_nuevo  = $alto;
	$proporcion = $ancho_maximo/$alto_maximo;
				
	if (($ancho/$alto)>$proporcion) {
	  if ($ancho>$ancho_maximo) {
		$ancho_nuevo = $ancho_maximo;
        $alto_nuevo  = $alto * ($ancho_nuevo/$ancho); }}
    else
	  if ($alto>$alto_maximo) {
		$alto_nuevo = $alto_maximo;
        $ancho_nuevo  = $ancho * ($alto_nuevo/$alto); }

    //crear imagen small
    $imagen_nueva_small = imagecreatetruecolor($ancho_nuevo,$alto_nuevo);
    //redimensionar y copiar imagen
    imagecopyresampled($imagen_nueva_small, $img, 0,0,0,0, $ancho_nuevo, $alto_nuevo, $ancho, $alto);
    //graba la imagen
	imagejpeg($imagen_nueva_small, $imagen_small);

  } // si existe la imagen         
}  // si se subió el archivo
								
// actualiza la tabla de productos
if ($subido) { // si se copió la foto

  if (strlen($fotos)>0) $fotos.=$CR;
 
  $fotos.=$siguiente;

  include('../conexion.php');
  $resJUG= mysql_query("UPDATE producto SET fotos='$fotos' WHERE clave='$producto'",$conexion);
  
  mysql_close();

	echo '<script languaje="JavaScript">';
	echo '  document.brinca.submit(); ';
	echo '</script>';

}

$mensaje='ERROR: '.$mensaje.'<br><br>No se subió la foto...';

?>
      </p>
      <p>&nbsp;</p>
      <? include('mensaje.php'); ?>
      <p>&nbsp;</p>
      </div>
</div>
</body>
</html>
