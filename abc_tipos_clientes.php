<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
  include('../conexion.php');
  /*$modulo=9;
  if (!op($modulo))  {
    $aviso = 'Usuario sin permiso para acceder a este mÃ³dulo';
    $aviso_link = 'principal.php';
    include('mensaje_sistema.php');
    return;
  }*/
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


<script language="JavaScript">


function validaSoloTexto(cadena){
  // ^[a-zA-ZÃ€-Ã¿\u00f1\u00d1]+(\s*[a-zA-ZÃ€-Ã¿\u00f1\u00d1]*)*[a-zA-ZÃ€-Ã¿\u00f1\u00d1]+$
  var patron = /^[0-9a-zA-Z\sÃ‘Ã±\u00E0-\u00FC\u00f1\u00d1]*$/;
  // En caso de querer validar cadenas con espacios usar: /^[a-zA-Z\s]*$/
  if(!cadena.search(patron))
    return true;
  else
    return false;
}

  function valida() {
   var claves=[""];
   var nombres=[""];
   <?php

   $resultado = mysql_query('SELECT * FROM cliente_tipo',$conexion);
   while($row = mysql_fetch_array($resultado)){
   ?>
   claves.push("<?php echo strtoupper($row['id']); ?>");
   nombres.push("<?php echo strtoupper($row['nombre']); ?>");
   <?php
   }
   ?>
   if (document.forma.clave.value.trim() == "") {
     alert("Ingresar un identificador válido.");
   document.forma.clave.focus();
     return;
   }

   if (validaSoloTexto(document.forma.clave.value)==false) {
     alert("Ingresar un identificador válido.");
   document.forma.clave.focus();
     return;
   }
   if (claves.includes(document.forma.clave.value.toUpperCase()) == true) {
     alert("El identificador ya se encuentra registrado. Favor de introducir otro válido");
   document.forma.clave.focus();
     return;
   }
   if (document.forma.nombre.value.trim() == "") {
     alert("Ingresar tipo de cliente válido.");
   document.forma.nombre.focus();
     return;
   }
   if (validaSoloTexto(document.forma.nombre.value)==false) {
     alert("Ingresar un tipo de cliente válido.");
   document.forma.nombre.focus();
     return;
   }
      if (nombres.includes(document.forma.nombre.value.toUpperCase()) == true) {
     alert("El tipo de cliente ya se encuentra registrado. Favor de introducir otro válido");
   document.forma.nombre.focus();
     return;
   }    
   document.forma.action='graba_tipos_clientes.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_tipos_clientes.php';
   document.forma.submit();
  }


</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Tipo de Cliente'; include('top.php'); ?>
	<?
		$id=$_POST['id'];
		if (empty($id)) $id=$_GET['id'];

        if (!empty($id)) {
          $resultado= mysql_query("SELECT * FROM cliente_tipo WHERE id='$id'",$conexion);
          $row = mysql_fetch_array($resultado);
        }
        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td><div align="right">Identificador:</div></td>
            <td><input name="clave" type="text" class="campo" required="required" id="clave" style="text-transform:uppercase;" value="<?= $row['id']; ?>" size="1" maxlength="1" />            </td>
          </tr>
          <tr>
            <td><div align="right">Tipo de Cliente:</div></td>
            <td><input name="nombre" type="text" class="campo" required="required" id="nombre" style="text-transform:capitalize;" value="<?= $row['nombre']; ?>" size="20" maxlength="20"  />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
