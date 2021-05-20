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
	// crear nombre temporal de archivo
	$ht = date("U");
	srand((double)microtime()*1000000);
	$token='';
	for ($i=1; $i<=4; $i++) {
	   $token=$token.chr(rand(97,122));
	} 
	$archivo_tmp = date("Ymd")."_".$token."_".$ht;
	
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
<script type="text/javascript" src="js/jquery.js"></script>
<link href="js/src/shadowbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/src/shadowbox.js"></script>
<script type="text/javascript">
	Shadowbox.init({
		language: 'es',
		players:  ['img', 'html', 'iframe', 'qt', 'wmp', 'swf', 'flv']
	});
</script>
<link href="./uploadify/uploadify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="./uploadify/swfobject.js"></script>
<script type="text/javascript" src="./uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  $('#file_upload').uploadify({
    'uploader'  : './uploadify/uploadify.swf',
    'script'    : './uploadify/uploadify.php',
    'cancelImg' : './uploadify/cancel.png',
    'folder'    : './uploads',
    'expressInstall' : './uploadify/expressInstall.swf',
	'buttonText' : 'SELECCIONA',
    'fileExt'   : '*.jpg',
    'fileDesc'  : 'Archivos de Imagen',
    'scriptData'  : {'identificador':'<?=$archivo_tmp;?>','tipo':'jpg'},
	'sizeLimit'   : 2000000,
	'auto'      : true,
    'onSelect' : function(event,queueID,fileObj){
					var ext = fileObj.name;
					ext = ext.substr(ext.length-4); //gets last 4 chars (extension type)
					document.getElementById('message').innerHTML="";
					switch (ext) {
						case "jpeg":
						case ".jpg":
						case ".JPG":
						// case ".gif":
						// filetype ok
						break;
						default:
						// alert("Data type invalid");
						document.getElementById('message').innerHTML="Error en tipo de archivo. Sólo JPG!";
						//$jQuery('#file_upload').uploadifyCancel(queueID);
						break;
					}
	},	
    'onAllComplete' : function(event,data) {
	   // document.getElementById('message').innerHTML="Archivo subido!";
	   valida();
    },
	'onError'     : function (event,ID,fileObj,errorObj) {
      document.getElementById('message').innerHTML="Error: "+errorObj.type;
    }
  });
});
</script>

<script language="JavaScript">
  function valida() {
   document.forma.action='graba_foto.php';
   document.forma.submit();
  }
  function borra(id) {
    document.forma.foto.value = id;
    document.forma.action='borra_foto.php';
    document.forma.submit();
  }
  function principal(id) {
    document.forma.principal.value = id;
    document.forma.action='foto_principal.php';
    document.forma.submit();
  }
</script>
<style type="text/css">
<!--
.style1 {font-size: small}
-->
</style>
</head>

<body>
<div id="container">
	<? $tit='Administrar Fotos de Productos'; include('top.php'); ?>
	<?
		
		$producto = $_GET['producto'];
		if (!$producto) $producto = $_POST['producto'];

        include("../conexion.php");
        
        $resultado= mysql_query("SELECT * FROM producto WHERE clave='$producto'",$conexion);
        $row = mysql_fetch_array($resultado);
    
        $CR=chr(13).chr(10);
        
        $fotos = $row['fotos'];
        $foto = explode($CR,$fotos);
        
        if (strlen($fotos)>0) $total_fotos=count($foto);
        else $total_fotos=0;
    
    ?>
	<div class="main">
      <form id="forma" name="forma" method="post" action="#" enctype="multipart/form-data"> 
        <table width="770" border="0" align="center" cellpadding="3" cellspacing="0">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          
          
          <tr>
            <td><div align="right">
                <p>Producto:</p>
            </div></td>
            <td><b>
              <?= $row['nombre'].' - '.$row['modelo']; ?>
              <input name="producto" type="hidden" id="producto" value="<?= $producto; ?>" />
	          <input type="hidden" name="archivo_tmp" id="archivo_tmp" value="<?=$archivo_tmp;?>" />
              <input name="foto" type="hidden" id="foto" />
              <input name="principal" type="hidden" id="principal" />
            </b></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <? if ($total_fotos<5) { ?>
          <tr>
            <td><div align="right">Imagen:</div></td>
            <td><input name="file_upload" type="file" id="file_upload" /><div id="message"></div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>Formato JPG en RGB: <strong>.jpg</strong> <span class="style1">(min&uacute;sculas)</span><br />
              Ancho m&aacute;ximo:<strong> 900 px </strong>(para zoom)<br />
            Tama&ntilde;o m&aacute;ximo: <strong>2mb, 72dpi</strong></td>
          </tr>
          <? } ?>
          <tr>
            <td>&nbsp;</td>
            <td><? if ($total_fotos>0) { ?>
                <table width="400" border="0" cellpadding="5" cellspacing="2" class="texto">
                  <tr bgcolor="#FFFFFF">
                    <td width="150"><div align="center"><strong>Fotos (
                            <?= $total_fotos; ?>
                      )</strong></div></td>
                    <td><div align="center"><strong>Versiones</strong></div></td>
                    <td><div align="center"><strong>Eliminar</strong></div></td>
                    <td><div align="center"><strong>Posici&oacute;n</strong></div></td>
                  </tr>
                  <? for ($i=0; $i<count($foto); $i++) { ?>
                  <tr bgcolor="#FFFFFF">
                    <td width="150" align="center">
                    	<img src="images/cms/productos/small/<?= $foto[$i].'?'.md5(time()); ?>" border="0" />					</td>
                    <td align="center">
                    	<?
						    if (file_exists('images/cms/productos/small/'.$foto[$i])) echo '<a href="images/cms/productos/small/'.$foto[$i].'?'.md5(time()).'" rel="shadowbox">Miniatura</a><br />';
						    if (file_exists('images/cms/productos/medium/'.$foto[$i])) echo '<a href="images/cms/productos/medium/'.$foto[$i].'?'.md5(time()).'" rel="shadowbox">Media</a><br />';
						    if (file_exists('images/cms/productos/'.$foto[$i])) echo '<a href="images/cms/productos/'.$foto[$i].'?'.md5(time()).'" rel="shadowbox">Ampliación</a><br />';
						    if (file_exists('images/cms/productos/big/'.$foto[$i])) echo '<a href="images/cms/productos/big/'.$foto[$i].'?'.md5(time()).'" rel="shadowbox">Zoom ficha</a><br />';
						?>                    </td>
                    <td align="center"><a onclick="return confirm('&iquest;Est&aacute;s seguro de Borrar la foto?')" href="javascript:borra('f<?= $i; ?>');"><img src="images/borrar.png" width="14" height="15" border="0" alt="Borrar foto" /></a></td>
              <td align="center"><? if ($i>0) { ?>
                        <a href="subir_foto.php?producto=<?= $producto; ?>&amp;nfoto=<?= $i; ?>"><img src="images/subir.png" border="0" alt="Subir foto" /></a>
                        <? } else echo '<img src="images/spacer.gif" width="18" height="18">'; 
			    if ($i<count($foto)-1) { ?>
                        <a href="bajar_foto.php?producto=<?= $producto; ?>&amp;nfoto=<?= $i; ?>"><img src="images/bajar.png" border="0" alt="Bajar foto" /></a>
                        <? } else echo '<img src="images/spacer.gif" width="18" height="18">'; ?>                    </td>
                  </tr>
                  <? } ?>
                </table>
            <? } // if fotos>0  ?>
           </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="desc" type="button" class="boton" onclick="document.location.href='lista_producto.php'" value="REGRESAR AL LISTADO" id="desc" /></td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
