<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=1;
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
	include("../conexion.php");
	
	$usuario = $_GET['usuario'];
	
	if (!empty($usuario)) {
	  $resultado= mysql_query("SELECT * FROM usuario WHERE clave='$usuario'",$conexion);
	  $row = mysql_fetch_array($resultado);
	}
	
	 $resMEN= mysql_query("SELECT * FROM menu",$conexion);
	 $cant_menu= mysql_num_rows($resMEN);
	 
	 $resMEN= mysql_query("SELECT * FROM menu WHERE tabla!=''",$conexion);
	 $cant_menu2= mysql_num_rows($resMEN);
		 
        
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
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" src="js/lib.js"></script>

<script language="JavaScript">
function agregaOp(inForm,texto,valor) {
	var siguiente = inForm.lista_opciones.options.length;
	var encontrado = false;
	for (var i=0; i < inForm.lista_opciones.length; i++) {
	    if (inForm.lista_opciones.options[i].value == valor) {
			encontrado = true;
		}
    }
	if (!encontrado) {
	    eval("inForm.lista_opciones.options[siguiente]=" + "new Option(texto,valor,false,true)");
	}
}
function eliminaOp(inForm,indice) {
	var i = inForm.lista_opciones.options.length;
    inForm.lista_opciones.options[indice] = null;
}

function agregaAu(inForm,texto,valor) {
	var siguiente = inForm.lista_autorizar.options.length;
	var encontrado = false;
	for (var i=0; i < inForm.lista_autorizar.length; i++) {
	    if (inForm.lista_autorizar.options[i].value == valor) {
			encontrado = true;
		}
    }
	if (!encontrado) {
	    eval("inForm.lista_autorizar.options[siguiente]=" + "new Option(texto,valor,false,true)");
	}
}
function eliminaAu(inForm,indice) {
	var i = inForm.lista_autorizar.options.length;
    inForm.lista_autorizar.options[indice] = null;
}

</script>

<script language="JavaScript">
  function valida() {
    if (document.forma.nombre.value == "") {
     alert("Falta nombre.");
	 document.forma.nombre.focus();
     return;
     }
    if (document.forma.login.value == "") {
     alert("Falta login.");
	 document.forma.login.focus();
     return;
     }
	 
	<? if (empty($usuario)) { ?>

    if (document.forma.password.value == "") {
     alert("Falta contraseña.");
	 document.forma.password.focus();
     return;
    }
	<? } ?>
    if (document.forma.password.value != "") {
		
		if (!isPwd(document.forma.password.value)) {
			alert("Contraseña inválida. Al menos 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 letra y 1 número");
			document.forma.password.focus();
			return;
		}
		if (document.forma.password.value != document.forma.password2.value) {
		 	alert("Contraseñas diferentes, favor de verificar.");
		 	document.forma.password.focus();
		 	return;
		}
	  	document.forma.pwd.value=MD5(document.forma.password.value);
    }
	
    if (document.forma.lista_opciones.length == "0") {
     alert("Faltan opciones.");
	 document.forma.cat_opciones.focus();
     return;
     }

	// combina claves de opciones seleccionadas en un string separado por comas
	var string_op = '';
	for (var i=0; i < document.forma.lista_opciones.length; i++) {
	  string_op += ' '+document.forma.lista_opciones.options[i].value+',';
	}
	document.forma.opciones.value = string_op;

	// combina claves de autorizar seleccionadas en un string separado por comas
	var string_au = '';
	for (var i=0; i < document.forma.lista_autorizar.length; i++) {
	  string_au += ' '+document.forma.lista_autorizar.options[i].value+',';
	}
	document.forma.autorizar.value = string_au;

  	document.forma.password.value='';
    document.forma.action='graba_usuario.php';
    document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_usuario.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Administrar Usuarios'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="pwd" id="pwd" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Nombre:</div></td>
            <td><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="115" maxlength="100" />            </td>
          </tr>
          <tr>
            <td><div align="right">Login:</div></td>
            <td><input name="login" type="text" class="campo" id="login" value="<?= $row['login']; ?>" size="20" maxlength="10" /></td>
          </tr>
          <tr>
            <td><div align="right">Contrase&ntilde;a:</div></td>
            <td><input name="password" type="password" class="campo" id="password" size="20" maxlength="10" /></td>
          </tr>
          <tr>
            <td><div align="right">Confirmar:</div></td>
            <td><input name="password2" type="password" class="campo" id="password2" size="20" maxlength="10" /></td>
          </tr>
          <tr>
            <td><div align="right">Correo:</div></td>
            <td><input name="email" type="text" class="campo" id="email" value="<?= $row['email']; ?>" size="60" maxlength="50" /></td>
          </tr>
          <tr>
            <td><div align="right">Usuario Service:</div></td>
            <td><input name="service" type="checkbox" id="service" value="1" <? if ($row['service']==1) echo 'checked'; ?> /></td>
          </tr>
          <tr>
            <td><div align="right">Activo:</div></td>
            <td><input name="activo" type="checkbox" id="activo" value="1" <? if ($row['activo']==1 OR empty($usuario)) echo 'checked'; ?> /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>NOTA:</strong> Los cambios de privilegios aplican hasta que el usuario vuelve a loggearse.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>M&Oacute;DULOS A LOS QUE TIENE ACCESO EL USUARIO:</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Opciones disponibles:</strong><br />
                (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Opciones seleccionadas:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td><span class="rotulo">
                  <select name="cat_opciones" size="<?= $cant_menu; ?>" class="campo" id="cat_opciones" ondblclick="agregaOp(document.forma,this.options[this.selectedIndex].text,this.value);">
                    <?  $resMEN= mysql_query("SELECT * FROM menu ORDER BY opcion",$conexion);
                          while ($rowMEN = mysql_fetch_array($resMEN)) { 
                            echo $CR.'<option value="'.$rowMEN['clave'].'">'.$rowMEN['opcion'].'</option>';
                          }
                      ?>
                  </select>
                </span></td>
                <td>&nbsp;</td>
                <td><select name="lista_opciones" size="<?= $cant_menu; ?>" class="campo" id="lista_opciones" ondblclick="eliminaOp(document.forma,this.selectedIndex);">
                    <?  $op=explode(',',$row['opciones']);
						for ($i=0; $i<=count($op)-2; $i++) {
						  $claveop=trim($op[$i]);
						  $resMEN= mysql_query("SELECT * FROM menu WHERE clave=$claveop",$conexion);
						  $rowMEN = mysql_fetch_array($resMEN);
						  echo $CR.'<option value="'.$claveop.'">'.$rowMEN['opcion'].'</option>';
						}
					?>
                </select></td>
              </tr>
            </table>
            <input name="opciones" type="hidden" id="opciones" value="" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><strong>INFORMACI&Oacute;N QUE PUEDE AUTORIZAR EL USUARIO:</strong></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><table border="0" cellpadding="0" cellspacing="0" class="texto">
                <tr>
                  <td><strong>Opciones disponibles:</strong><br />
                    (dbl clic para seleccionar)</td>
                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                  <td><strong>Opciones seleccionadas:</strong><br />
                    (dbl clic para eliminar de la lista)</td>
                </tr>
                <tr>
                  <td><span class="rotulo">
                    <select name="cat_autorizar" size="<?= $cant_menu2; ?>" class="campo" id="cat_autorizar" ondblclick="agregaAu(document.forma,this.options[this.selectedIndex].text,this.value);">
                      <?  $resMEN= mysql_query("SELECT * FROM menu WHERE tabla!='' ORDER BY opcion",$conexion);
                          while ($rowMEN = mysql_fetch_array($resMEN)) { 
                            echo $CR.'<option value="'.$rowMEN['clave'].'">'.$rowMEN['opcion'].'</option>';
                          }
                      ?>
                    </select>
                  </span></td>
                  <td>&nbsp;</td>
                  <td><select name="lista_autorizar" size="<?= $cant_menu2; ?>" class="campo" id="lista_autorizar" ondblclick="eliminaAu(document.forma,this.selectedIndex);">
                      <?  $au=explode(',',$row['autorizar']);
						for ($i=0; $i<=count($au)-2; $i++) {
						  $claveau=trim($au[$i]);
						  $resMEN= mysql_query("SELECT * FROM menu WHERE clave=$claveau",$conexion);
						  $rowMEN = mysql_fetch_array($resMEN);
						  echo $CR.'<option value="'.$claveau.'">'.$rowMEN['opcion'].'</option>';
						}
					?>
                  </select></td>
                </tr>
              </table>
                <input name="autorizar" type="hidden" id="autorizar" value="" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="usuario" type="hidden" id="usuario" value="<?= $usuario; ?>" />            </td>
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
