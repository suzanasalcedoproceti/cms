<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
  include('../conexion.php');
  /*$modulo=9;
  if (!op($modulo))  {
    $aviso = 'Usuario sin permiso para acceder a este módulo';
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
  // ^[a-zA-ZÀ-ÿ\u00f1\u00d1]+(\s*[a-zA-ZÀ-ÿ\u00f1\u00d1]*)*[a-zA-ZÀ-ÿ\u00f1\u00d1]+$
  var patron = /^[0-9a-zA-Z\sÑñ\u00E0-\u00FC\u00f1\u00d1]*$/;
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

   $resultado = mysql_query('SELECT * FROM servicios',$conexion);
   while($row = mysql_fetch_array($resultado)){
   ?>
   claves.push("<?php echo strtoupper($row['tipo_servicio']); ?>");
   nombres.push("<?php echo strtoupper($row['descripcion']); ?>");
   <?php
   }
   ?>
   if (document.forma.clave.value.trim() == "") {
     alert("Ingresar un tipo de servicio v�lido.");
   document.forma.clave.focus();
     return;
   }

   if (validaSoloTexto(document.forma.clave.value)==false) {
     alert("Ingresar un identificador v�lido.");
   document.forma.clave.focus();
     return;
   }
 
   if (document.forma.nombre.value.trim() == "") {
     alert("Ingresar un identificador v�lido.");
   document.forma.nombre.focus();
     return;
   }


    if (document.forma.condition_type_fisica.value.trim() == "") {
     alert("Ingresar un identificador v�lido.");
   document.forma.condition_type_fisica.focus();
     return;
   }

 
   document.forma.action='graba_servicios.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_servicios.php';
   document.forma.submit();
  }

</script>
</head>
<body>
<div id="container">
	<? $tit='Administrar Servicios'; include('top.php'); ?>
	<?
		$idservicio=$_POST['servicio'];
		if (empty($idservicio)) $idservicio=$_GET['servicio'];

        if (!empty($idservicio)) {
          $resultado= mysql_query("SELECT * FROM servicios WHERE idservicio='$idservicio'",$conexion);
          $row = mysql_fetch_array($resultado);
        }
        
      ?> 
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td><div align="right">Tipo de servicio:</div></td>
            <td><select name="clave" class="campo" required="required" id="clave">
              <option value=""   <? if ($row['tipo_servicio']=='') echo 'selected';?>>Selecciona</option>
              <option value="ENTREGA" <? if ($row['tipo_servicio']=='ENTREGA') echo 'selected';?>>ENTREGA</option>
              <option value="ADICIONAL" <? if ($row['tipo_servicio']=='ADICIONAL') echo 'selected';?>>ADICIONAL</option>
              <option value="ESPECIAL" <? if ($row['tipo_servicio']=='ESPECIAL') echo 'selected';?>>ESPECIAL</option>
            </select>   

              </td>
          </tr>
          <tr>
            <td><div align="right">Descripci&oacute;n:</div></td>
            <td><input name="nombre" type="text" class="campo" required="required" id="nombre" style="text-transform:capitalize;" value="<?= $row['descripcion']; ?>" size="40" maxlength="100"  />            </td>
          </tr>
          <tr>
            <td><div align="right">Condition Type:</div></td>
            <td><input name="condition_type_fisica" type="text" class="campo" required="required" id="condition_type_fisica" style="text-transform:capitalize;" value="<?= $row['condition_type_fisica']; ?>" size="40" maxlength="100"  />            </td>
          </tr>
 
          <tr>
            <td><div align="right">Material:</div></td>
            <td><input name="material" type="text" class="campo" required="required" id="material" style="text-transform:capitalize;" value="<?= $row['material']; ?>" size="40" maxlength="50"  />            </td>
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
            <td>
              <input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
              <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" /> 
      <input name="idservicio" type="hidden"  id="idservicio"  value="<?= $row['idservicio']; ?>" />  
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
