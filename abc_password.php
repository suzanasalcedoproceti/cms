<?
    if (!include('ctrl_acceso.php')) return;
   	include('funciones.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Panel de Control</title>
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" src="js/lib.js"></script>
<script type="text/javascript" src="js/menu.js"></script>

<script language="JavaScript">
  function valida() {
    if (document.forma.password.value == "") {
     alert("Falta contraseña actual.");
	 document.forma.password.focus();
     return;
     }
    if (document.forma.password1.value == "") {
     alert("Falta contraseña nueva.");
	 document.forma.password1.focus();
     return;
    }
	if (!isPwd(document.forma.password1.value)) {
	 alert("Contraseña inválida. Al menos 8 caracteres, al menos 1 mayúscula, 1 minúscula, 1 letra y 1 número");
	 document.forma.password1.focus();
	 return;
	}
    if (document.forma.password1.value != document.forma.password2.value) {
     alert("Contraseñas diferentes.");
	 document.forma.password2.focus();
     return;
    }
    if (document.forma.password.value == document.forma.password1.value) {
     alert("Debes establecer una contraseña diferente a la anterior");
	 document.forma.password2.focus();
     return;
   }
   document.forma.pwd.value=MD5(document.forma.password.value);
   document.forma.pwd1.value=MD5(document.forma.password1.value);
   document.forma.password.value = '';

   document.forma.action='graba_password.php';
   document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Cambiar contraseña'; include('top.php'); ?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <input type="hidden" name="pwd" id="pwd" />
        <input type="hidden" name="pwd1" id="pwd1" />
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Contrase&ntilde;a actual:</div></td>
            <td><input name="password" type="password" class="campo" id="password" size="20" maxlength="10" />            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Nueva contrase&ntilde;a:</div></td>
            <td><input name="password1" type="password" class="campo" id="password1" size="20" maxlength="10" />            </td>
          </tr>
          <tr>
            <td><div align="right">Confirmar contrase&ntilde;a:</div></td>
            <td><input name="password2" type="password" class="campo" id="password2" size="20" maxlength="10" />
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
               </td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
