<?
session_start();
	  
if (!empty($_SESSION['usr_valido'])) header('Location: principal.php');

require ('xajax/xajax_core/xajax.inc.php');
$xajax = new xajax(); 

function valida($forma) {

    $respuesta = new xajaxResponse(); 

    $respuesta->Assign('mensaje','innerHTML','<img src="images/loader.gif" alt="" />'); 

	$_SESSION['usr_valido'] = ''; 

	include_once('../conexion.php');
	include_once('lib.php');

	$login=mysql_real_escape_string($forma['login']);
	$pwd=mysql_real_escape_string($forma['pwd']);
    $resUSR= mysql_query("SELECT * FROM usuario WHERE login = '$login' LIMIT 1",$conexion);
    $rowUSR= mysql_fetch_array($resUSR);
	$password = genera_password($pwd, $rowUSR['password']);	

	if ($password != $rowUSR['password']) {
		$respuesta->Assign('mensaje','innerHTML','<div>Acceso inv&aacute;lido<br />por favor verifica tus datos...</div>');
	} else {
	
		if ($rowUSR['activo']==0) {
			$respuesta->Assign('mensaje','innerHTML','<div>Usuario inactivo.</div>');
		} else {
	  
			$_SESSION['usr_valido'] = $rowUSR['clave'];
			$_SESSION['ss_nombre'] = $rowUSR['nombre'];
			$_SESSION['ss_opciones'] = $rowUSR['opciones'];
			$_SESSION['ss_autorizar'] = $rowUSR['autorizar'];
			$_SESSION['usr_service'] = $rowUSR['service'];

			$respuesta->script('document.forma.action="principal.php"; document.forma.submit()');
							
		}
	}

    return $respuesta;
} 

$xajax->register(XAJAX_FUNCTION, 'valida'); 
$xajax->processRequest(); 
   
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
<? 
   $xajax->printJavascript("xajax/"); 
?>
</head>

<body onLoad="document.forma.login.focus();">
<div id="container">
	<? $tit='Acceso al Panel de Control'; $home=1; include('top.php'); ?>
	<div class="main">
           <form action="" method="post" name="forma" id="forma">
            <table width="300" border="0" align="center" cellpadding="5" cellspacing="0">
              <tr>
                <td class="texto">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="texto"><div align="right">Usuario:</div></td>
                <td><input name="login" type="text" class="campo" id="login" size="25" maxlength="20" AUTOCOMPLETE="off" />                </td>
              </tr>
              <tr>
                <td class="texto"><div align="right">Contrase&ntilde;a:</div></td>
                <td><input type="password" name="password" class="campo" id="password" size="25" maxlength="20" AUTOCOMPLETE="off" />                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <div align="center"><div id="mensaje"></div>
                </div></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td><input type="hidden" name="pwd" id="pwd" />
                <input name="entrar" type="button" class="boton" value="ENTRAR" onclick="document.forma.pwd.value=MD5(document.forma.password.value); document.forma.password.value=''; xajax_valida(xajax.getFormValues('forma'));" />
                </td>
              </tr>
            </table>
            <p>&nbsp;</p>
          </form>
    </div>
</div>
</body>
</html>
