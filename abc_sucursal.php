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
  function isEmail(string) {
    if (string.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
        return true;
    else
        return false;
  }

  function valida() { 
 
   if (document.forma.nombre.value == "") {
     alert("Falta nombre de la sucursal.");
	 document.forma.nombre.focus();
     return;
   }
   if (document.forma.telefonos.value == "") {
     alert("Falta indicar el tel\u00e9fono.");
	 document.forma.telefonos.focus();
     return;
   }

  if (document.forma.calle.value == "") {
       alert("Falta indicar la calle.");
     document.forma.calle.focus();
     return;
   }
  if (document.forma.numext.value == "") {
       alert("Falta indicar el n\u00famero exterior.");
     document.forma.numext.focus();
     return;
   }
  if (document.forma.cp.value == "") {
       alert("Falta indicar el c\u00f3digo postal.");
     document.forma.cp.focus();
     return;
   }

 
   document.forma.action='graba_sucursales.php';
   document.forma.submit();
  }

  function rechaza() {
   document.forma.action='borra_sucursalesphp';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_sucursal.php';
   document.forma.submit();
  }
</script>


</script>

<script>
$(document).ready(function(){
   $("#cp").keyup(function(event){ 
     var id_cp = $(this).val(); 
     var tamano=id_cp.length;
     if(tamano==5){
          $.post("postcolonia.php", { id_cp: id_cp }, function(data,valor,data2) {
               //  $("#esta").html(valor); 
              //   $("#colonias").html(data); 
                 $("#mnpios").html(data);
                //console.log(valor);
                console.log("data="+data);
                //console.log(data2);
            });  
            } //verificar tamaño igual a 5 

    });

 function dirsucursal() {
   var id_cp = $("#cp").val(); 
   var id_scr = $("#idsuc").val();
   var colscr = $("#colscr").val();
    
          $.post("postcolonia.php", { id_cp: id_cp,id_scr: id_scr,colscr:colscr}, function(data,valor,data2) {
               //  $("#esta").html(valor); 
              //   $("#colonias").html(data); 
                 $("#mnpios").html(data); 
            });     
 }
dirsucursal();
});
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Sucursales '; include('top.php'); ?>
	<?
        include('../conexion.php');

		$sucursal=$_POST['sucursal'];
   
		if (empty($sucursal)) $sucursal=$_GET['sucursal'];
            $rr="SELECT * FROM sucursales WHERE idSucursal='$sucursal'";
        if (!empty($sucursal)) {
      
          $resultado= mysql_query("SELECT * FROM sucursales WHERE idSucursal='$sucursal'",$conexion);
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
           <input name="idsuc" type="hidden" class="campo" id="idsuc" value="<?= $row['idsuc']; ?>" size="3" maxlength="2" />
          <tr>
            <td><div align="right">Sucursal:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombresucursal']; ?>" size="50" maxlength="100" />  </td>
          </tr>
           <tr>
            <td><div align="right">Tel&eacute;fono:</div></td>
            <td><label>
              <input name="telefonos" type="text" class="campo" id="telefonos" value="<?=$row['telefono'];?>" size="50" maxlength="50" />
            </label></td>
          </tr>
          <tr>
            <td><div align="right">Correo electr&oacute;nico:</div></td>
            <td><input name="email" type="text" class="campo" id="email" value="<?= $row['email']; ?>" size="50" maxlength="50" /></td>
          </tr>
          <tr>
            <td><div align="right">Calle:</div></td>
            <td><input name="calle" type="text" class="campo" id="calle" value="<?= $row['calle']; ?>" size="50" maxlength="35" /></td>
          </tr>
          <tr>
            <td><div align="right">N&uacute;mero exterior:</div></td>
            <td><input name="numext" type="text" class="campo" id="numext" value="<?= $row['numext']; ?>" size="7" maxlength="10" /></td>
          </tr>
          <tr>
            <td><div align="right">N&uacute;mero interior:</div></td>
            <td><input name="numint" type="text" class="campo" id="numint" value="<?= $row['numint']; ?>" size="7" maxlength="10" /></td>
          </tr>

          <tr>
            <td><div align="right">C.P.:</div></td>
            <td><input name="cp" type="text" class="campo" id="cp" value="<?= $row['cp']; ?>" size="7" maxlength="5" /></td>
          </tr>

          <?php   if (!empty($estado)) { }?>
          <tr>
            <td colspan="2"> 

        <table width="100%" border="0 align="left" cellpadding="3" cellspacing="0" class="texto">

 
           <tr>
            <td width="18%"><div align="right"></div></td>
            <td > <div id="mnpios" >  </div>   </td>
          </tr>
        </table>
        </td>
          </tr>


          <tr>
            <td><div align="right">Planta:</div></td>
            <td><input name="planta" type="text" class="campo" id="planta" value="<?= $row['planta']; ?>" size="7" maxlength="5" /></td>
          </tr>
           <tr>
            <td><div align="right">SL:</div></td>
            <td><input name="slct" type="text" class="campo" id="slct" value="<?= $row['SL']; ?>" size="7" maxlength="5" /></td>
          </tr>

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
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
