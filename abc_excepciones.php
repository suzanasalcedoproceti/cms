<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=9;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
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
<script type="text/javascript" src="js/jquery-3.2.1.js"></script>
<script language="JavaScript">  
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

  function validadatos() {  
    var long= document.forma.cp.value.length; 
    var tipop= document.forma.tipo_producto.value;
 
   if (long<5) {
     alert("Código Postal inválido.");
	 document.forma.cp.focus();
     return;
   } 
   if (tipop =="") {
     alert("Selecciona un tipo de producto.");
   document.forma.tipo_producto.focus();
     return;
   } 

   document.forma.action='graba_excepcioncp.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_sucursalesphp';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_excepcioncp.php';
   document.forma.submit();
  }
</script>

</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Excepciones '; include('top.php'); ?>
	<?
        include('../conexion.php');

		$sucursal=$_POST['sucursal'];

		if (empty($sucursal)) $sucursal=$_GET['sucursal'];
            $rr="SELECT * FROM sucursales WHERE idSucursal='$sucursal'";
        if (!empty($sucursal)) {
      
          $resultado= mysql_query("SELECT * FROM sucursales WHERE idsuc='$sucursal'",$conexion);
          $row = mysql_fetch_array($resultado);
		      $estado=$row['cve_estado'];
          $colonascr=$row['colonia'];
          $planta=$row['planta'];
        }
  
        
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
<input name="colscr" type="hidden" id="colscr" value="<?= $row['colonia']; ?>"/> 
        <table width="80%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
           <tr>
            <td><div align="right">CP:</div></td>
            <td><input name="cp" type="text" class="campo" id="cp" onkeypress="return isNumberKey(event)" value="" size="7" maxlength="5" />   
            </td>
          </tr>
          <tr>
            <td><div align="right">Tipo de producto:</div></td>
            <td><select name="tipo_producto" class="camporeq" id="tipo_producto">
              <option value=""   <? if ($row['tipo_producto']=='') echo 'selected';?>>Selecciona</option> 
               <? $resultadotpr = mysql_query("SELECT * FROM tipo_producto ORDER BY nombre",$conexion);
                      while ($rowtpr = mysql_fetch_array($resultadotpr)) {
                      echo '<option value="'.$rowtpr['nombre'].'"';
                     if ($rowtpr['nombre']==$tipo_producto) echo 'selected';
                     echo '>'.$rowtpr['clave'].' </option>';
                 } ?>
            </select>    </td>
          </tr>
           <tr>
             <td><div align="right">Tipo Servicio:</div></td>
             <td><select name="tipo_servicio" class="camporeq" id="tipo_servicio">
               <option value="4"  'selected'>Entrega Domicilio</option> 
            </select></td>
          </tr> 
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="validadatos();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="sucursal" type="hidden" id="sucursal" value="<?= $sucursal; ?>" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>

       <br> <br>
      </form>    
    </div>
</div>
</body>
</html>
