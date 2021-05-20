<?
    if (!include('ctrl_acceso.php')) return;
	include('funciones.php');
	$modulo=8;
    include('../conexion.php');
	if (!op($modulo))  {
		$aviso = 'Usuario sin permiso para acceder a este módulo';
		$aviso_link = 'principal.php';
		include('mensaje_sistema.php');
		return;
	}
    
	if ($_POST['accion'] == 'recordar') {
		$CR='\r\n';
		$BR='<br>';

		// obtener configuracion
		$resMAIL= mysql_query("SELECT * FROM mail WHERE clave=4",$conexion);
		$rowMAIL= mysql_fetch_array($resMAIL);
		$resultadoCFG = mysql_query("SELECT * FROM config WHERE reg = 1",$conexion);
		$rowCFG = mysql_fetch_array($resultadoCFG);					

		require('../phpmailer/class.phpmailer.php');
		$mail = new phpmailer();
		$mail->SetLanguage('es','../phpmailer/language/');

        $mail->From     = $rowMAIL['from'];
        $mail->FromName = $rowMAIL['fromname'];
        $mail->AddReplyTo($rowMAIL['replyto'],$rowMAIL['replytoname']);
        $mail->Sender   = $rowMAIL['sender'];
        $mail->Host     = $rowMAIL['host'];
        $mail->Mailer   = 'smtp';
        $mail->SMTPAuth = ($rowMAIL['host']=='mailhost.whirlpool.com') ? false : true;
      if($rowMAIL['host']=='mailhost.whirlpool.com') $mail->SMTPSecure = 'tls';   
        $mail->Username = $rowMAIL['username'];
        $mail->Password = $rowMAIL['password'];    
        $mail->Port   = $rowMAIL['port'];
        $mail->isHTML(true);
        $mail->ClearAddresses();
		$mail->Subject = 'Recordatorio para activar cuenta de tiendawhirlpool.com';
    }

    if (!$_POST['mensaje']) {
	  	$mensaje = 
		   'Estimado(a) ##nombre##,'.chr(10).chr(10).'Hemos notado que tu proceso de activación de cuenta aún no ha terminado, te invitamos a ser parte de la experiencia de Tienda Whirlpool Online.'.chr(10).chr(10);
			
    } else $mensaje = $_POST['mensaje'];

	
   
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

<script language="JavaScript">
  function ir(form, Pag) {
    form.numpag.value = Pag;
    form.action='?';
    form.submit();
  }
  function ordena(orden) {
    document.forma.ord.value = orden;
	document.forma.numpag.value = 1;
    document.forma.action='?';
    document.forma.submit();
  }
  function enviar() {
	document.forma.accion.value = 'recordar';
    document.forma.action='?';
    document.forma.submit();
  }
</script>
</head>

<body>
<div id="container">
	<? $tit='Recordatorio a Clientes Inactivos'; include('top.php'); ?>
  <?
   $ver = $_POST['ver'];
   $numpag = $_POST['numpag'];
   $ord = $_POST['ord'];
   if (empty($ver)) $ver='20000';
   if (empty($numpag)) $numpag='1';
   if (empty($ord)) $ord='nombre';

   $empresa = $_POST['empresa'];
   $texto = $_POST['texto'];
   $tipo = $_POST['tipo'];


   if     ($ord=='nombre') $orden='ORDER BY cliente.nombre, cliente.apellido_paterno';
   elseif ($ord=='email') $orden='ORDER BY email';
   
   
?>
	<div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="7" cellspacing="0" class="texto">
          <tr>
            <td>&nbsp;</td>
            <td align="right"><table width="500" border="0" align="left" cellpadding="2" cellspacing="0" class="texto">
              <tr>
                <td valign="top"><div align="right">Empresa:</div></td>
                <td><div align="left"><span class="row1">
                  <select name="empresa" class="campo" id="empresa">
                    <option value="" selected="selected">Cualquier empresa...</option>
                    <?
				$resEMP = mysql_query("SELECT clave, nombre FROM empresa ORDER BY nombre",$conexion);
				while ($rowEMP = mysql_fetch_array($resEMP)) {
				  echo '<option value="'.$rowEMP['clave'].'"';
				  if ($rowEMP['clave']==$empresa) echo ' selected';
				  echo '>'.$rowEMP['nombre'].'</option>';
				}
			  ?>
                  </select>
                </span></div></td>
              </tr>
              <tr>
                <td valign="top"><div align="right">Tipo:</div></td>
                <td><div align="left">
                  <select name="tipo" class="campo" id="tipo">
                    <option value="">Cualquiera</option>
                    <option value="E" <? if ($tipo=='E') echo 'selected';?>>Empleados</option>
                    <option value="I" <? if ($tipo=='I') echo 'selected';?>>Invitados</option>
                  </select>
                </div></td>
              </tr>
              <tr>
                <td width="156" valign="top"><div align="right">Buscar:</div></td>
                <td width="594"><div align="left">
                    <input name="texto" type="text" class="campo" id="texto" value="<?= $texto; ?>" size="50" />
                    (Nombre, E-mail) <br />
                </div></td>
              </tr>

              <tr>
                <td>&nbsp;</td>
                <td><div align="left">
                  <input name="Submit" type="submit" class="boton" onclick="document.forma.numpag.value=1" value="Buscar" />
                </div></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><strong>Listado de clientes inactivos. Al final del listado selecciona la opci&oacute;n para recordarles activar su cuenta.</strong></td>
          </tr>
          <tr>
            <td bgcolor="#BBBBBB"><?
			
                  // obtener el total de registros que coinciden...
                  // y establecer algunas variables
					   

                     // construir la condición de búsqueda
                     $condicion = "WHERE activo = 0 AND email != '' AND empresa.empresa_publica = 0 ";

					 if (!empty($empresa)) 
					 	$condicion .= " AND empresa='$empresa' ";

					 if (!empty($tipo)) {
					 	if ($tipo=='I') 
						 	$condicion .= " AND invitado = 1 ";
						else
							$condicion .= " AND invitado = 0 ";
					}
					 
					 if (!empty($texto)) {
						// identificar si sólo hay 1 palabra o más de 1
						$trozos=explode(" ",$texto);
						$numero_palabras=count($trozos);
						if (1 || $numero_palabras==1) {
							//SI SOLO HAY UNA PALABRA DE BUSQUEDA SE ESTABLECE UNA INSTRUCION CON LIKE
							$condicion .= "AND (cliente.nombre LIKE '%$texto%'  OR cliente.apellido_paterno LIKE '%$texto%' OR cliente.apellido_materno LIKE '%$texto%'  OR email LIKE '%$texto%') ";	
						} else  { // más de 1 palabras
							//SI HAY UNA FRASE SE UTILIZA EL ALGORTIMO DE BUSQUEDA AVANZADO DE MATCH AGAINST
							//busqueda de frases con mas de una palabra y un algoritmo especializado
							//$condicion .= " SELECT titulo, descripcion , MATCH ( titulo, descripcion ) AGAINST ( '$texto' ) AS Score FROM anuncio WHERE MATCH ( titulo, descripcion, ciudad, estado ) AGAINST ( '$texto' ) ORDER BY score DESC";
							$condicion .= " AND MATCH ( cliente.nombre, email ) AGAINST ( '$texto' IN BOOLEAN MODE ) ";
						} 
					 }

                     // construir la condición de búsqueda
					   $query = "SELECT 1 FROM cliente LEFT JOIN empresa on cliente.empresa = empresa.clave $condicion";
                       $resultadotot = mysql_query($query,$conexion);
                       $totres = mysql_num_rows ($resultadotot);
                       $totpags = ceil($totres/$ver);
                       if ($totres==0)
                          $numpag = 0;
						  
					 echo 'Total de clientes en la lista: <b>'.$totres.'</b>';
					 
					    if ($_POST['inicial']) $inicial = $_POST['inicial']; else $inicial = 1;
						
						if ($_POST['final']) $final= $_POST['final']; else $final = $totres;

			
			  ?>            </td>
            <td align="right" bgcolor="#BBBBBB">
                <input name="cliente" type="hidden" id="cliente">
            	<input name="accion" type="hidden" id="accion">
                <input name="ord" type="hidden" id="ord" value="<?= $ord; ?>" />
                <input name="numpag" type="hidden" id="numpag" value="<?= $numpag; ?>" />
                <?


                     $regini = ($numpag * $ver) - $ver;
					 if ($regini<0) $regini=0;

                     // poner flechitas anterior, primero, último, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Página anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "Página ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente página"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Última página"></a>';
                     }
              ?>            </td>
          </tr>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td colspan="6" nowrap="nowrap">&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap"><strong>No.</strong></td>
            <td nowrap="nowrap" bgcolor="#DDDDDD"><b>Nombre </b></td>
            <td nowrap="nowrap"><b>E-mail</b></td>
            <td><b>Empresa</b></td>
            <td align="center"><strong>Tipo</strong></td>
            <td align="center"><strong>Acci&oacute;n</strong></td>
          </tr>
          <?
			 
			 $ic = 0;
			 $enviados = 0;
             $resultado= mysql_query("SELECT cliente.*, CONCAT(cliente.nombre,' ',cliente.apellido_paterno,' ',cliente.apellido_materno) AS nombre  FROM cliente LEFT JOIN empresa on cliente.empresa = empresa.clave $condicion $orden LIMIT $regini,$ver",$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			 	
				$ic++;
				$cve_empleado = $row['clave'];
				$cve_empresa=$row['empresa'];
	            $resEMP= mysql_query("SELECT * FROM empresa WHERE clave='$cve_empresa'",$conexion);
                $rowEMP= mysql_fetch_array($resEMP); 




          ?>
          <tr class="texto" valign="top" bgcolor="<? if ($color) echo '#FFFFFF'; else echo '#F4F4F2'; ?>">
            <td bgcolor="#FFFFFF"><?=$ic;?></td>
            <td bgcolor="#FFFFFF"><?= $row['nombre'];?></td>
            <td bgcolor="#FFFFFF"><?= $row['email']; ?></td>
            <td bgcolor="#FFFFFF"><?= $rowEMP['nombre']; ?></td>
            <td align="center" bgcolor="#FFFFFF">
			<? if ($row['invitado']==1) echo 'Invitado'; else echo 'Empleado'; 
 				   if ($row['cantidad_invitaciones']>0) echo ' ('.$row['cantidad_invitaciones'].')';

				?></td>
            <td align="center" bgcolor="#FFFFFF">
              <? if ($_POST['accion'] == 'recordar' && $ic >= $_POST['inicial'] && $ic <= $_POST['final']) { 
			  
			  		// enviar mail
					
					$imagen = '<img src="https://www.tiendawhirlpool.com/images/header/logo.png"><br>';
					$texto_mail = $imagen.$mensaje;
					$texto_mail = str_replace('##nombre##',$row['nombre'],$texto_mail);
					$texto_mail .= chr(10).chr(10).
					'Para activar tu cuenta, haz clic <a href="https://'.$rowCFG['url'].'/activar_cuenta.php?t='.$row['token'].'&c='.$row['clave'].'">aquí</a>'.chr(10).
					chr(10).'Gracias';
					$texto_mail  = str_replace(chr(10),'<br>',$texto_mail);
					
					
					$mail->MsgHTML($texto_mail);
					$mail->ClearAddresses();
					$mail->AddAddress($row['email'],$row['nombre']);

					$mail->Send();

					$error_mail = $mail->ErrorInfo;
					if ($error_mail) echo 'Error envío';
					else {
						echo 'Enviado';
						$enviados ++;
					}
					
					// actualizar contador de invitaciones
					$resultadoUC = mysql_query("UPDATE cliente SET cantidad_invitaciones = cantidad_invitaciones + 1 WHERE clave = $cve_empleado LIMIT 1");
			  
//			  		echo "<br>".$texto_mail;
			  
			  ?>
              <? } else echo '&nbsp;'; ?>
            </td>
          </tr>
          <?
                 } // WHILE
                 mysql_close();
              ?>
        </table>
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">
          <tr>
            <td colspan="2" align="right">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" nowrap="nowrap" bgcolor="#BBBBBB">Se enviaron <strong><?=$enviados;?></strong> invitaciones</td>
            <td align="right" bgcolor="#BBBBBB"><?

                     // poner flechitas anterior, primero, &uacute;ltimo, etc.
                     if ($numpag<=1) {
                         echo '<img src="images/primera_off.gif" width="16" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/anterior_off.gif" width="13" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,1);"><img src="images/primera_on.gif" border="0" width="16" height="11" align="absmiddle" alt="Primera p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.($numpag-1).');"><img src="images/anterior_on.gif" border="0" width="13" height="11" align="absmiddle" alt="P&aacute;gina anterior"></a>';
                     }
                     echo '&nbsp;&nbsp;<span class="texto">';
                     echo "P&aacute;gina ".$numpag." de ".$totpags;
                     echo "&nbsp;</span>&nbsp;";
                     if ($numpag>=$totpags) {
                         echo '<img src="images/siguiente_off.gif" width="13" height="11" align="absmiddle">&nbsp;';
                         echo '<img src="images/ultima_off.gif" width="16" height="11" align="absmiddle">';
                     } else {
                         echo '<a href="javascript:ir(document.forma,'.($numpag+1).');"><img src="images/siguiente_on.gif" border="0" width="13" height="11" align="absmiddle" alt="Siguiente p&aacute;gina"></a>&nbsp;';
                         echo '<a href="javascript:ir(document.forma,'.$totpags.');"><img src="images/ultima_on.gif" border="0" width="16" height="11" align="absmiddle" alt="&Uacute;ltima p&aacute;gina"></a>';
                     }
              ?></td>
          </tr>
          
          
          <? if ($_POST['accion'] != 'recordar') {  ?>
          
          <tr>
            <td colspan="2"><strong>Enviar correo a cada cliente de la lista, con liga para activaci&oacute;n de su cuenta</strong></td>
          </tr>
          <tr>
            <td>
              <div align="right">Enviar desde el  No. </div></td>
            <td><input name="inicial" type="text" class="campo" id="inicial" value="<?=$inicial;?>" size="5" maxlength="5" />
hasta el
  <input name="final" type="text" class="campo" id="final" size="5" maxlength="5" value="<?=$final;?>" /></td>
          </tr>
          <tr>
            <td width="13%" valign="top"><div align="right">Mensaje:</div></td>
            <td width="87%">
              <textarea name="mensaje" cols="80" rows="5" class="campo" id="mensaje"><?=$mensaje;?></textarea>
              <br />
            Haz clic <strong>aqu&iacute;</strong> para activar tu cuenta.
            <p>Gracias.</p></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input name="Submit2" type="button" class="boton" onclick="javascript:enviar();" value="ENVIAR" /></td>
          </tr>
          <? } ?>
          <tr>
            <td colspan="2" align="right">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
