<?

// script para generar y enviar correo cuando se graba el pedido, o cuando se procesa en CMS
	include ('d:\inetpub\wwwroot\conexion.php');
	require('d:\inetpub\wwwroot\phpmailer\class.phpmailer.php');
	$mail = new phpmailer();
	$mail->SMTPDebug=true;
	$mail->SetLanguage('es','d:\inetpub\wwwroot\phpmailer\language');
    $CR='\r\n';
    $BR='<br>';

    $resMAIL= mysql_query("SELECT * FROM mail WHERE clave=4",$conexion);
    $rowMAIL= mysql_fetch_array($resMAIL);
    
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
    $mail->Subject = 'Log Guias';
		$texto_mail='
		<html>
		<head>
		</head>
		<body>
        </table>
        <table width="100%" border="0" align="center" cellpadding="2" cellspacing="3">
          <tr class="texto">
            <td nowrap="nowrap">&nbsp;</td>
            <td nowrap="nowrap">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr class="texto" bgcolor="#F4F4F2">
            <td nowrap="nowrap""><b>Fecha y hora</b></td>
            <td nowrap="nowrap"><b>Mensaje</b></td>
            <td><b>Error</b></td>
          </tr>';
          
			 $query = "SELECT * FROM log_guias where fecha_hora between date_add(NOW(),interval -1 day) and NOW() order by fecha_hora";
             $resultado= mysql_query($query,$conexion);
             while ($row = mysql_fetch_array($resultado)){ 
			$texto_mail.='
          <tr class="texto" valign="top" bgcolor="">
            <td nowrap="nowrap" bgcolor="#FFFFFF">'.$row['fecha_hora'].'</td>
            <td valign="top" bgcolor="#FFFFFF">'.$row['mensaje'].'</td>
            <td valign="top" bgcolor="#FFFFFF">'.$row['error'].'</td>
		  </tr>';
                 } // WHILE
                 mysql_close();
            $texto_mail.='</table></body></html>';
	$mail->MsgHTML($texto_mail);
    //$mail->AddAddress('victor_lopez_openservice@whirlpool.com');
    $mail->AddAddress('ricardo_d_garza_tekna@whirlpool.com');
    $mail->AddAddress('hector_g_sanchez@whirlpool.com');
    $mail->AddAddress('alan_rodriguez_jinzai@whirlpool.com');
	if ($mail->Host != 'localhost') $mail->Send();
    $error_mail = $mail->ErrorInfo;
    if ($error_mail) $error .= 
       '<br />No se pudo enviar correo con detalles del pedido, favor de imprimir esta pantalla con datos del pedido.<br>'.$error_mail;	
	echo $texto_mail;

?>