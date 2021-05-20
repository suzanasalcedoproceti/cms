<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	include("lib.php");
	$modulo=8;
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
<link href="css/styles_dashboard.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
<script type="text/javascript" src="js/lib.js"></script>
<script src="js/jquery.js" type="text/javascript" language="javascript1.2"></script>

<script language="JavaScript">
  function isEmail(string) {
    if (string.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1)
        return true;
    else
        return false;
  }
  function valida() {
    if (document.forma.nombre.value == "") {
     alert("Falta nombre.");
	 document.forma.nombre.focus();
     return;
    }
    if (document.forma.apellido_paterno.value == "") {
     alert("Falta apellido paterno.");
	 document.forma.apellido_paterno.focus();
     return;
    }
    if (document.forma.empresa.value == "") {
     alert("Indica la empresa a que pertenece.");
	 document.forma.empresa.focus();
     return;
    }    
	if (document.forma.email.value == "") {
     alert("Falta correo electrónico.");
	 document.forma.email.focus();
     return;
    }
	if (isEmail(document.forma.email.value) == false) {
	 alert("Formato de correo inválido");
	 document.forma.email.focus();
	 return;
	}
	if (document.forma.password.value == '') {
	 alert("Ingresa la contraseña");
	 document.forma.password.focus();
	 return;
	}	
	if (document.forma.password.value.length < 6) {
	 alert("Contraseña mínimo de 6 caracteres");
	 document.forma.password.focus();
	 return;
	}	
	if (!document.forma.pers_moral.checked && !document.forma.pers_fisica.checked) {
		alert("Indica el tipo de persona fiscal");
		return;
	
	}
	if (document.forma.razon_social.value == '') {
	 alert("Ingresa la Razón Social");
	 document.forma.razon_social.focus();
	 return;
	}	
	if (document.forma.pers_moral.checked && document.forma.rfc.value.length != 12 ) {
		alert("El RFC para personas morales debe tener 12 caracteres. (No ingresar espacios ni guiones)");
		document.forma.rfc.focus();
		return;
	}
	if (document.forma.pers_fisica.checked && document.forma.rfc.value.length != 13 ) {
		alert("El RFC para personas físicas debe tener 13 caracteres. (No ingresar espacios ni guiones)");
		document.forma.rfc.focus();
		return;
	}
	if (!validaRFC(document.forma.rfc.value,1)) {
		alert("Formato de RFC incorrecto");
		document.forma.rfc.focus();
		return;
	}

	if (document.forma.fact_calle.value == '') {
	 alert("Ingresa la calle del domicilio fiscal");
	 document.forma.fact_calle.focus();
	 return;
	}
	if (document.forma.fact_exterior.value == '') {
	 alert("Ingresa el número exterior ");
	 document.forma.fact_exterior.focus();
	 return;
	}
	if (document.forma.fact_colonia.value == '') {
	 alert("Ingresa la colonia");
	 document.forma.fact_colonia.focus();
	 return;
	}
	if (document.forma.fact_ciudad.value == '') {
	 alert("Ingresa la ciudad");
	 document.forma.fact_ciudad.focus();
	 return;
	}
	if (document.forma.fact_estado.value == '') {
	 alert("Ingresa el estado");
	 document.forma.fact_estado.focus();
	 return;
	}
	if (document.forma.fact_cp.value == '') {
	 alert("Ingresa el CP");
	 document.forma.fact_cp.focus();
	 return;
	}
	if (document.forma.fact_cp.value.length < 5) {
	 alert("Ingresa el CP correctamente");
	 document.forma.fact_cp.focus();
	 return;
	}

    if (document.forma.fact_email.value!="" && isEmail(document.forma.fact_email.value) == false) {
     alert("Correo electrónico de faturación inválido.");
     document.forma.fact_email.focus();
     return;
    }
	
	document.forma.action='graba_cliente_proyectos.php';
    document.forma.submit();
  }
  
  function descarta() {
   document.forma.action='lista_cliente_proyectos.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Clientes de Proyectos'; include('top.php'); ?>
	<?
        include('../conexion.php');

		$cliente=$_GET['cliente']+0;
		if (empty($cliente)) $cliente=$_POST['cliente']+0;

        if ($cliente>0) {
          $resultado= mysql_query("SELECT * FROM cliente WHERE clave=$cliente",$conexion);
          $row = mysql_fetch_array($resultado);
        }
		$empresa=$row['empresa'];
		$resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
		$rowEMP= mysql_fetch_array($resEMP); 
		if ($rowEMP['empresa_proyectos']!=1) {
//			return;
		}
		
	    // obtener datos de configuracion
	    $resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
	    $rowCFG = mysql_fetch_array($resultadoCFG);

		
		
      ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="texto">
          <tr>
            <td><strong>DATOS GENERALES DEL USUARIO</strong></td>
            <td>*Datos obligatorios</td>
          </tr>
          <tr>
            <td align="right" class="tcompara_det mBot20">Activo:</td>
            <td><input name="activo" type="checkbox" id="activo" value="1" <? if ($row['activo']) echo 'checked';?>/></td>
          </tr>
          <tr>
            <td align="right" class="tcompara_det mBot20">Solo consultas:</td>
            <td><input name="solo_consultas" type="checkbox" id="solo_consultas" value="1" <? if ($row['solo_consultas']) echo 'checked';?>/></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">*Nombres:</div></td>
            <td><input name="nombre" type="text" id="nombre" size="80" maxlength="100" value="<?=$row['nombre'];?>"/></td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*Apellido Paterno:</div></td>
            <td><input name="apellido_paterno" type="text" id="apellido_paterno" size="50" maxlength="50" value="<?=$row['apellido_paterno'];?>"/></td>
          </tr>
          <tr>
            <td class="mid"><div align="right">Apellido Materno:</div></td>
            <td><input name="apellido_materno" type="text" id="apellido_materno" size="50" maxlength="50" value="<?=$row['apellido_materno'];?>"/></td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*Empresa:</div></td>
            <td><select name="empresa" id="empresa" <? if (!empty($cliente) && 0) echo 'readonly';?>>
              <option value="" selected="selected">Seleccionar...</option>
              <?
					
					$resEMP = mysql_query("SELECT clave, nombre FROM empresa WHERE empresa_proyectos = 1",$conexion);
					while ($rowEMP = mysql_fetch_array($resEMP)) {
						echo '<option value="'.$rowEMP['clave'].'"';
						if ($rowEMP['clave']==$empresa) echo ' selected';
						echo '>'.$rowEMP['nombre'].'</option>';
					}
			  ?>
            </select></td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*E-mail:</div></td>
            <td><input name="email" type="text" id="email" size="50" maxlength="100" value="<?=$row['email'];?>" />
            (login de acceso)</td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*Contrase&ntilde;a para <strong>proyectoswhirlpool.com</strong>:</div></td>
            <td><input name="password" id="password" size="20" maxlength="15" value="<?=$row['password'];?>" type="password"/></td>
          </tr>
          <tr>
            <td >&nbsp;</td>
            <td >&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" ><strong>DATOS DE FACTURACI&Oacute;N DE LA EMPRESA</strong></td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*Tipo de persona:</div></td>
            <td><label>
              <input type="radio" name="persona_moral" id="pers_fisica" value="2" <? if ($row['persona_moral']==2) echo 'checked';?> />
              Persona F&iacute;sica</label>
              <label>
                <input type="radio" name="persona_moral" id="pers_moral" value="1"  <? if ($row['persona_moral']==1) echo 'checked';?>/>
                Persona Moral</label></td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*Raz&oacute;n Social:</div></td>
            <td><input name="razon_social" type="text" id="razon_social" size="80" maxlength="100" value="<?=$row['razon_social'];?>" /></td>
          </tr>
          <tr>
            <td class="mid"><div align="right">*R.F.C.:</div></td>
            <td><input name="rfc" type="text" id="rfc" size="20" maxlength="15" value="<?=$row['rfc'];?>" onblur="javascript:this.value=formatRFC(this.value);" /></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">*Calle::</div></td>
            <td><input name="fact_calle" type="text" id="fact_calle" size="60" maxlength="100" value="<?=$row['fact_calle'];?>"/>
              *Exterior:
                <input name="fact_exterior" type="text" id="fact_exterior" size="10" maxlength="10" value="<?=$row['fact_exterior'];?>"/>
              Interior:
              <input name="fact_interior" type="text" id="fact_interior" size="10" maxlength="10" value="<?=$row['fact_interior'];?>"/></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">*Colonia:</div></td>
            <td><input name="fact_colonia" type="text" id="fact_colonia" size="100" maxlength="100" value="<?=$row['fact_colonia'];?>"/></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">*Ciudad:</div></td>
            <td><input name="fact_ciudad" type="text" id="fact_ciudad" size="80" maxlength="100" value="<?=$row['fact_ciudad'];?>"/></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">*Estado:</div></td>
            <td><select name="fact_estado" id="estado">
              <option value="" selected="selected">Selecciona estado...</option>
              <?
					$resultadoEDO = mysql_query("SELECT * FROM estado ORDER BY clave",$conexion);
					while ($rowEDO = mysql_fetch_array($resultadoEDO)) {
					  echo '<option value="'.$rowEDO['clave'].'"';
					  if ($rowEDO['clave']==$row['fact_estado']) echo 'selected';
				  	  echo '>'.$rowEDO['nombre'].'</option>';
				    }
			  ?>
            </select></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">*C.P.:</div></td>
            <td><input name="fact_cp" type="text" id="fact_cp" size="7" maxlength="5" value="<?=$row['fact_cp'];?>"/></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">Tel&eacute;fono:</div></td>
            <td><input name="fact_telefono" type="text" id="fact_telefono" size="25" maxlength="20" value="<?=$row['fact_telefono'];?>"/></td>
          </tr>
          <tr>
            <td class="tcompara_det mBot20"><div align="right">*Correo para facturaci&oacute;n:</div></td>
            <td><input name="fact_email" type="text" id="fact_email" size="80" maxlength="80" value="<?=$row['fact_email'];?>"/></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
              <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="cliente" type="hidden" id="cliente" value="<?= $cliente; ?>" /></td>
            <input name="viene_de" type="hidden" id="viene_de" value="<?= $viene_de; ?>" />
            <td></td></td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
