<?
   if (!include("ctrl_acceso.php")) return;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Panel de Control</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/styles.css" rel="stylesheet" type="text/css" />
<link href="css/menu.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 6]><link rel="stylesheet" href="css/menu_ie6.css" /><![endif]-->
<script type="text/javascript" src="js/menu.js"></script>
</head>
<body>
<div id="container">
<?  $tit='Bienvenido Administrador del Sistema'; include('top.php'); ?>
<div class="main">
		<table width="450" border="0" cellspacing="0" cellpadding="1" align="center"  class="mensaje">
		  <tr> 
			<th><br /> Mensaje del Sistema</tr>
		  <tr> 
			<td class="row1" valign="top" align="center"> <br>
			  <br>
			  <img src="images/folder_announce_new.gif" width="19" height="18"> 
			  <?= $aviso;
				  if (empty($aviso_link)) $aviso_link = "javascript:history.go(-1);";
				  if (empty($aviso_texto)) $aviso_texto = "Regresar"; ?>
			  <p>[ <a href="<?=$aviso_link;?>" class="texto"><?=$aviso_texto;?></a> ]<br>
				<br>
			  </p>
			</td>
		  <tr> 
			<th>&nbsp;</th>
		  </tr>
	  </table>
 </div>
</div>
</body>
</html>