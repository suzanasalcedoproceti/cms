<?
	ini_set ('error_reporting', 'E_ALL ~E_NOTICE');
	ini_set ("display_errors","1" );
	ini_set('post_max_size','200M'); ini_set('upload_max_filesize','200M'); ini_set('max_execution_time','200M'); ini_set('max_input_time','200M'); ini_set('memory_limit','200M'); set_time_limit(65536);
//echo 'paso 1';
include('d:/inetpub/wwwroot/conexion.php');
include('d:/inetpub/wwwroot/admin/lib.php');
include('d:/inetpub/wwwroot/libprod.php');
include('d:/inetpub/wwwroot/phpmailer/class.phpmailer.php');
	
$mail = new phpmailer();
//$mail->SetLanguage('es','d:/inetpub/wwwroot/phpmailer/language/');
//$folio_pedido = $_POST['folio_pedido'];
//CURDATE(),pedido.fecha),count(detalle_pedido.modelo),count(dashboard.material)
// obtener datos de configuraci&oacute;n  (ya se toma de la categoria)
  $resultadoCFG = mysql_query("SELECT disponibilidad_venta, url FROM config WHERE reg = 1");
  $rowCFG = mysql_fetch_array($resultadoCFG);

$sql = "     
SELECT p.folio as folio_pedido ,
    p.*,
    p.total AS total_del_pedido,
    pedido_netpay.* FROM pedido p
INNER JOIN detalle_pedido dp ON p.folio=dp.pedido
INNER JOIN dashboard db ON dp.pedido=db.folio_pedido and dp.modelo=db.material
INNER JOIN cliente c ON c.clave=p.cliente
LEFT JOIN pedido_netpay ON pedido_netpay.folio = p.folio
WHERE p.fecha > '2017-02-01' AND
MOD(DATEDIFF(CURDATE(),p.fecha),10) = 0 AND DATEDIFF(CURDATE(),p.fecha) > 0 AND
p.estatus = 1 AND p.tipo_venta<>'PR' AND p.tienda<>29
AND dp.sustituido=0
AND p.folio not in (SELECT folio_refacturacion from pedido)
AND db.reason_rejection='' AND LEFT(db.planta,2)<>'FM'
AND (db.billing_doc = '') AND db.sufijo <> 'G' 
AND dp.es_flete=0 AND dp.es_garantia = 0
AND p.folio NOT IN (SELECT folio_pedido FROM pedido_seguimiento WHERE DATE_FORMAT(fecha,'%Y%c%d')=DATE_FORMAT(now(),'%Y%c%d') AND tipo='Seguimiento')
AND db.planta NOT IN ('RM08', 'RM50' , 'RM11')
AND db.entrega = 'domicilio'
AND c.email<>'' 
GROUP BY p.folio LIMIT 30
    ";
// (seguimiento = 1 AND (ds_pedidos_entregados = 0 OR (ds_pedidos_entregados = ps_total and ds_pedidos_entregados < ds_pedidos) ))
// (ds_pedidos_entregados > 0 OR (ds_pedidos_entregados > ps_total and ds_pedidos_entregados < ds_pedidos) )
$resultado = mysql_query($sql,$conexion);
while($row = mysql_fetch_assoc($resultado)){
	//echo "<pre>";
		//print_r($row);
	//echo "</pre>";


	$folio_pedido = $row['folio_pedido'];
	$texto_mail = file_get_contents('d:/inetpub/wwwroot/mail/mail_seguimiento.html');
	$texto_partida =  file_get_contents('d:/inetpub/wwwroot/mail/detalle_pedido_seguimiento.html');
	$cliente = $row['cliente'];
// script para generar y enviar correo cuando se graba el pedido, o cuando se procesa en CMS
    $resultadoCLI = mysql_query("SELECT CONCAT(nombre,' ',apellido_paterno,' ',apellido_materno) AS nombre, email, empresa,invitado_por,numero_empleado FROM cliente WHERE clave = $cliente",$conexion);
    $rowCLI = mysql_fetch_array($resultadoCLI);
	
	//print_r($rowCLI);
    $CR='\r\n';
    $BR='<br>';

    $folio_pedido_largo = str_replace('-','',$row['fecha']).$folio_pedido;
    $folio_pedido_largo = trim(substr($folio_pedido_largo,2,50));
    
    $texto_mail = str_replace('##folio_pedido##',$folio_pedido_largo,$texto_mail);
    $texto_mail = str_replace('##folio_banco##',$row['auth_code'],$texto_mail);
	$texto_banco_detalle = '';
	
	if($row['auth_code']){
	$texto_banco_detalle = '<h3 style="font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:18px; margin:10px 0 15px 0; color:#333;"><strong>Aut. Banco:</strong></h3>
                	<h1 style="font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:15px; margin:0; font-weight:normal; color:#333;">'.$row['auth_code'].'</h1>
					<h3 style="font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:18px; margin:10px 0 15px 0; color:#333;"><strong>Banco:</strong></h3>
                	<h1 style="font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:15px; margin:0; font-weight:normal; color:#333;">'.$row['bank_name'].'</h1>  
					<h3 style="font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:18px; margin:10px 0 15px 0; color:#333;"><strong>Tarjeta:</strong></h3>
                	<h1 style="font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:15px; margin:0; font-weight:normal; color:#333;">'.str_repeat("*",12).$row['card_number'].'</h1>  ';
	
	
	
	}

  if($row['fdp_tdd'] > 0){
    $texto_banco_detalle .= '<h3 style="font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:18px; margin:10px 0 15px 0; color:#333;"><strong>Aut. Banco:</strong></h3>
              <h1 style="font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:15px; margin:0; font-weight:normal; color:#333;">'.$row['fdp_tdd_folio'].'</h1>
      <h3 style="font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:18px; margin:10px 0 15px 0; color:#333;"><strong>Banco:</strong></h3>
              <h1 style="font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:15px; margin:0; font-weight:normal; color:#333;">'.$row['fdp_tdd_banco'].'</h1>  
       ';

  }
  if($row['fdp_tdc'] > 0 and $row['auth_code']!=''){
    $texto_banco_detalle .= '<h3 style="font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:18px; margin:10px 0 15px 0; color:#333;"><strong>Aut. Banco:</strong></h3>
              <h1 style="font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:15px; margin:0; font-weight:normal; color:#333;">'.$row['fdp_tdc_folio'].'</h1>
      <h3 style="font-family:Helvetica, Arial, sans-serif; font-size:16px; line-height:18px; margin:10px 0 15px 0; color:#333;"><strong>Banco:</strong></h3>
              <h1 style="font-family:Helvetica, Arial, sans-serif; font-size:15px; line-height:15px; margin:0; font-weight:normal; color:#333;">'.$row['fdp_tdc_banco'].'</h1>  
       ';

  }
	
	$texto_mail = str_replace('##banco_detalle##',$texto_banco_detalle,$texto_mail);
	
	 
    //$texto_mail = str_replace('##fecha_pedido##',fecha($row['fecha']),$texto_mail);
    $texto_mail = str_replace('##fecha_pedido##',fecha(date("Y-m-d")),$texto_mail);
    
    $texto_mail = str_replace('##nombre_envio##',$row['envio_nombre'],$texto_mail);
    $texto_mail = str_replace('##dir_envio##',$row['envio_calle']." ".$row['envio_exterior']." ".$row['envio_interior'],$texto_mail);
    $texto_mail = str_replace('##col_envio##',$row['envio_colonia'],$texto_mail);
    $texto_mail = str_replace('##cd_envio##',$row['envio_ciudad_nombre'],$texto_mail);
    $texto_mail = str_replace('##edo_envio##',$row['envio_estado'],$texto_mail);
    $texto_mail = str_replace('##cp_envio##',$row['envio_cp'],$texto_mail);
    $texto_mail = str_replace('##tel_envio##',$row['envio_telefono_casa'],$texto_mail);
    
    if ($row['requiere_factura']) {
        $texto_mail = str_replace('##enc_fact##','Datos de Facturaci&oacute;n',$texto_mail);
        $texto_mail = str_replace('##nombre_fact##',$row['fact_razon_social'],$texto_mail);
        $texto_mail = str_replace('##rfc_fact##','RFC: '.$row['fact_rfc'],$texto_mail);
        $texto_mail = str_replace('##dir_fact##',$row['fact_calle'],$texto_mail);
        $texto_mail = str_replace('##col_fact##',$row['fact_colonia'],$texto_mail);
        $texto_mail = str_replace('##cd_edo_fact##',$row['fact_ciudad'].', '.$row['fact_estado'],$texto_mail);
        $texto_mail = str_replace('##cp_fact##','C.P. '.$row['fact_cp'],$texto_mail);
        $texto_mail = str_replace('##tel_fact##','Tel. '.$row['fact_telefono'],$texto_mail);
    } else {
        $texto_mail = str_replace('##enc_fact##','',$texto_mail);
        $texto_mail = str_replace('##nombre_fact##','',$texto_mail);
        $texto_mail = str_replace('##rfc_fact##','',$texto_mail);
        $texto_mail = str_replace('##dir_fact##','',$texto_mail);
        $texto_mail = str_replace('##col_fact##','',$texto_mail);
        $texto_mail = str_replace('##cd_edo_fact##','',$texto_mail);
        $texto_mail = str_replace('##cp_fact##','',$texto_mail);
        $texto_mail = str_replace('##tel_fact##','',$texto_mail);
    }
	
	$txt_formas_pago= '';
  if ($row['fdp_tdd']>0) $txt_formas_pago .= "Tarjeta de D&eacute;bito: <strong>".number_format($row['fdp_tdd'],2)."</strong> <br>";
  if ($row['fdp_tdc']>0) $txt_formas_pago .= "Tarjeta de Cr&eacute;dito: <strong>".number_format($row['fdp_tdc'],2)."</strong> <br>";
	if ($row['fdp_cep']>0) $txt_formas_pago .= "Pago Directo: <strong>".number_format($row['fdp_cep'],2)."</strong> <br>";
	if ($row['fdp_puntos']>0) $txt_formas_pago .= "Puntos: <strong>".number_format($row['fdp_puntos'],2)."</strong><br>";
	if ($row['fdp_puntos_flex']>0) $txt_formas_pago .= "Puntos Flex: <strong>".number_format($row['fdp_puntos_flex'],2)."</strong><br>";
	if ($row['fdp_puntos_pep']>0) $txt_formas_pago .= "Puntos PEP: <strong>".number_format($row['fdp_puntos_pep'],2)."</strong><br>";
	
	if ($row['fdp_efectivo']>0) $txt_formas_pago .= "Efectivo: <strong>".number_format($row['fdp_efectivo'],2)."</strong> <br>";
	if ($row['fdp_tdconline']>0) $txt_formas_pago .= "Tarjeta de Cr&eacute;dito on line: <strong>".number_format($row['fdp_tdconline'],2)."</strong> <br>";
	if ($row['fdp_cheque']>0) $txt_formas_pago .= "Cheque: <strong>".number_format($row['fdp_cheque'],2)."</strong> ".$row['fdp_cheque_banco']."<br>";
	if ($row['fdp_deposito']>0) $txt_formas_pago .= "Dep&oacute;sito: <strong>".number_format($row['fdp_deposito'],2)."</strong> ".$row['fdp_deposito_banco']."<br>";
	if ($row['fdp_credito_nomina']>0) $txt_formas_pago .= "Cr&eacute;dito N&oacute;mina: <strong>".number_format($row['fdp_credito_nomina'],2)."</strong><br>";
	
    $texto_mail = str_replace('##formas_pago##',$txt_formas_pago,$texto_mail);
    
    $texto_proveedor = '';
    $texto_proveedor_resaltado = '';
    $correo_tipo = '';
    $texto_titulo_correo = '';
    $texto_mensaje_correo = '';

	// seguimiento
      $correo_tipo = 'Seguimiento';
      $texto_titulo_correo = 'Tu Orden Continúa en Proceso';
      $texto_mensaje_correo = 'Tu orden continúa en proceso, te recordamos que está programado para entregarse aproximadamente en 30 días hábiles a partir del '.fecha($row['fecha']).', recuerda que esta es una fecha estimada y está sujeta a la disponibilidad de inventario. Órdenes con más de un producto pueden llegar en uno o varios envíos.';
      

	
    // si el pedido no cumple con alguna de las caracteristicas, salta al siguiente pedido
    
//echo 'paso n<br>';
    $texto_mail = str_replace('##txt_proveedor##',$texto_proveedor,$texto_mail);
    $texto_mail = str_replace('##mensaje_texto_proveedor##',$texto_proveedor_resaltado,$texto_mail);
    $texto_mail = str_replace('##titulo_correo##',$texto_titulo_correo,$texto_mail);
    $texto_mail = str_replace('##mensaje_correo##',$texto_mensaje_correo,$texto_mail);

	
    $query = "SELECT detalle_pedido.*, marca.nombre AS nombre_marca, pedido.origen, pedido.tipo_venta,'' as guia
                 FROM detalle_pedido 
                 LEFT JOIN marca ON detalle_pedido.marca = marca.clave
                 LEFT JOIN pedido ON detalle_pedido.pedido = pedido.folio
                 WHERE pedido = $folio_pedido AND detalle_pedido.es_flete=0 AND detalle_pedido.es_garantia = 0 AND detalle_pedido.sustituido=0 and 
                 IFNULL((SELECT  COUNT(*) FROM dashboard AS ds 
				 WHERE ds.material = detalle_pedido.modelo 
				 AND ds.folio_pedido=$folio_pedido 
				 AND ds.reason_rejection='' AND LEFT(ds.planta,2)<>'FM' 
				 AND ds.billing_doc = ''
				 AND ds.planta NOT IN ('RM08', 'RM50' , 'RM11')
				 AND ds.entrega = 'domicilio'
				 AND ds.sufijo <> 'G'
				 LIMIT 1),0)>0 ORDER BY partida";
				 //echo "<br>".$query;

    $resultadoDP = mysql_query($query,$conexion);
    $resultadoDG = mysql_query($query,$conexion);

    $texto_partidas = '';				
    $hay_ocurre = 0;
    $primer_producto = '';
    $primer = 0;
    while ($rowDP = mysql_fetch_array($resultadoDP)) {
         $primer_producto = ($primer == 0) ? $rowDP['nombre'] : $primer_producto;
         $primer++;
        //print_r($rowDP).'<br>';
        if ($costo_entrega>0) 
        {
          $txt_costo_entrega = '$ '.number_format($rowDP['costo_entrega'],2); 
        }
        else 
        {
          $txt_costo_entrega = 'Sin costo';       
        }
//echo $txt_costo_entrega.'<br>';
        $origen_pedido = $rowDP['origen'];
        $tipo_venta = $rowDP['tipo_venta'];

        $texto_partidax = $texto_partida;
        $texto_partidax = str_replace('##nombre_producto##',$rowDP['nombre'],$texto_partidax);
        $texto_partidax = str_replace('##marca_producto##',$rowDP['nombre_marca'],$texto_partidax);
        $texto_partidax = str_replace('##modelo_producto##',$rowDP['modelo'],$texto_partidax);
        $texto_partidax = str_replace('##precio_producto##',$rowDP['precio_empleado'],$texto_partidax);
        $texto_partidax = str_replace('##cantidad_producto##',$rowDP['cantidad'],$texto_partidax);
        $texto_partidax = str_replace('##costo_entrega##',$txt_costo_entrega,$texto_partidax);
        $texto_partidax = str_replace('##subtotal_producto##',number_format($rowDP['subtotal'],2),$texto_partidax);
        
        if ($rowDP['es_garantia']  && $rowDP['folio_garantia']) {
          $folio_garantia=$rowDP['folio_garantia']; 
          $resultadoG = mysql_query("SELECT * FROM garantia WHERE folio = $folio_garantia");
          $rowG=mysql_fetch_array($resultadoG);
            if ($rowG['token']) {
            if ($pagado)  
                $link_g = '
                <a href="https://'.$rowCFG['url'].'/pdf_garantia.php?folio='.$folio_garantia.'&token='.$rowG['token'].'" target=_blank>Descargar</a>';
            else 
                $link_g = "Entrega Digital";
                $texto_partidax = str_replace('##entrega_producto##',$link_g,$texto_partidax); 
            }
          } 
          else 
          { 
            $xfecha_entrega = '';
            switch ($rowDP['tipo_entrega']) {
                case 'domicilio': 
                  $hay_domicilio ++;
                  if($rowDP['guia'])
                  {
                    $xfecha_entrega .= $rowDP['guia']; 
                  }
                  else
                  {
                    $xfecha_entrega .= 'Domicilio'; 
                  }
                break; 
                case 'inmediata': 
                  $xfecha_entrega .= '*';
                  $hay_inmediata ++; 
                break;                
                case 'ocurre'   :
                  $xfecha_entrega .= '***';
                  $hay_ocurre ++; 
                  $clave_ocurre = $rowDP['sucursal_ocurre']; 
                  $xfecha_entrega = fecha($rowDP['fecha_entrega']);
                break;
            }
            $texto_partidax = str_replace('##entrega_producto##',$xfecha_entrega,$texto_partidax);
        }                
        $texto_partidas .= $texto_partidax;
    }
    
    if ($origen=='mas') { // subject de acuerdo al origen del pedido (EN CMS se pagan CEP y Debito de TW, de MAS y Proyectos)
        $nombre_tienda = 'Whirlpool MAS';
        $politicas_entrega = '<a href="http://'.$rowCFG['url_mas'].'/politicas.php">Pol&iacute;ticas de entrega</a>';
    } else {
      
      $politicas_entrega = '';
      if ($tipo_venta=='PR') 
          $nombre_tienda = 'Whirlpool Proyectos';
      else
          $nombre_tienda = 'TiendaWhirlpool.com';
    }

    $texto_mail = str_replace('##politicas_entrega##',$politicas_entrega,$texto_mail);

    // insertar html de partidas en cuerpo
    $texto_mail = str_replace('##total_pedido##',number_format($row['total_del_pedido'],2),$texto_mail);
    $texto_mail = str_replace('##detalle_pedido##',$texto_partidas,$texto_mail);

    $txt_garantias = '';
    /*
    if ($hay_ocurre) {
        $resultadoSA = mysql_query("SELECT * FROM sucursal_ocurre WHERE clave = $clave_ocurre");
        $rowSA = mysql_fetch_array($resultadoSA);
    
        $txt_garantias .= '<p>
          <strong>* Sucursal para recoger tu mercanc&iacute;a a ocurre</strong></div>
          <div class="p25">
          <p>
          <strong>'.$rowSA['nombre'].'</strong>
          <br /><br>
          <strong>Domicilio:</strong><br />
          '.$rowSA['direccion'].'
          <br /><br />
          <strong>Tel&eacute;fonos:</strong>
          '.$rowSA['telefonos'].'
          <br /><br />
          <strong>Fax:</strong>
          '.$rowSA['fax'].'
          <br /><br />
          <strong>Correo:</strong>
          '.$rowSA['email'].'
          </p></div>';
    }						
    */

    if ($hay_garantias && $pagado) {
                                        
        $txt_garantias .= '<p><strong>Felicidades, su garant&iacute;a de f&aacute;brica ha sido extendida.</strong> 
             Usted acaba de adquirir la mejor protecci&oacute;n para su nuevo electrodom&eacute;stico.</p>
            <p>Le recordamos los beneficios que las Garant&iacute;as Extendidas de la familia de productos Whirlpool tienen para usted:  </p>
            <p>-Revisiones y servicios de reparaci&oacute;n que requiera de manera gratuita. 
              <br />
              -Refacciones originales e ilimitadas. 
              <br />
              -Red de Servicio T&eacute;cnico especializado disponible en toda la rep&uacute;blica
              <br />
              -Reparaciones por problemas de variaciones de voltaje.  </p>
            <p><strong>&#161;Importante&#33;</strong><br /> 
              Le recomendamos conserve la factura de su producto y el  contrato de GARANT&Iacute;A EXTENDIDA.  <br />
              En caso de requerir un servicio ser&aacute; requisito presentar estos documentos a nuestro personal t&eacute;cnico.  <br />
              Los beneficios de la GARANT&Iacute;A EXTENDIDA  inician cuando vence la Garant&iacute;a original de F&aacute;brica.  </p>
            <p> En caso de requerir alg&uacute;n servicio cont&aacute;ctenos a los siguientes tel&eacute;fonos:
              <br />
             <strong>Centro de Atenci&oacute;n a Clientes Whirlpool:  </strong><br />
              <b>Monterrey y &Aacute;rea Metropolitana</b>  8329 2100  </p>
            <p><b>Resto de la Rep&uacute;blica</b>  0181 8329 2100
              <br />
              Horarios de atenci&oacute;n telef&oacute;nica<br> 
              - Lunes a viernes de 8:00 am a 7:00 pm <br />
              - S&aacute;bados 8:00 am a 5:00 pm <br />
              - Domingos 10:00 am a 5:00 pm <br />
              
              Whirlpool agradece su preferencia. </p>';
    }
    
    $txt_cierre = 'Si tienes alguna duda responde a este correo o contáctanos vía telefónica al (81) 8215 5900 opción 3 de lunes a viernes de 9:00 a 18:00 y sábados de 9:00 a 14:00.
';
    $texto_mail = str_replace('##txt_cierre##',$txt_cierre,$texto_mail);
    $texto_mail = str_replace('##txt_garantias##',$txt_garantias,$texto_mail);

    $resMAIL= mysql_query("SELECT * FROM mail WHERE clave=4",$conexion);
    $rowMAIL= mysql_fetch_array($resMAIL);
    //print_r($rowMAIL);
	
	 $mail->CharSet = "UTF-8";
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

    $asunto = '';
    switch ($correo_tipo) {
      case 'Seguimiento':
        $asunto = 'Seguimiento de tu Orden - '.$folio_pedido_largo;
        break;
      case 'Entrega Parcial':
        $asunto = 'Próxima entrega de tu '.$primer_producto;
        break;     
      default:
        # code...
        break;
    }

	   //echo $texto_mail;
    $mail->Subject = $asunto;
	  

    $mail->MsgHTML($texto_mail);
	if($rowCLI['email'])
	{ $mail->AddAddress($rowCLI['email'],$rowCLI['nombre']); 
	}

	  $mail->AddBCC('dorlee_garcia_sycnos@whirlpool.com','Dorlee Garcia');
 
    $texto_pedido=$texto_mail;
      //echo "enviando mail a: ".$rowCLI['email']." -> ".$rowCLI['nombre'];
    if ($mail->Host != 'localhost') $mail->Send();
	  $error_mail = $mail->ErrorInfo;
    
    if ($error_mail){ 
      // Error
	  echo $error_mail;
	  $sql = "INSERT INTO pedido_seguimiento (folio_pedido, tipo, fecha, asunto, correo, productos_guia,guia,partida) VALUES(
					".$folio_pedido.",
					'Error',
					NOW(),
					'".$error_mail."',
					'".$rowCLI['email']."',
          '0',
          '',
          0
				)";
		mysql_query($sql);
	}else{
        $asunto = addslashes($asunto);

 
		$sql = "INSERT INTO pedido_seguimiento (folio_pedido, tipo, fecha, asunto, correo, productos_guia,guia,partida) VALUES(
					".$folio_pedido.",
					'".$correo_tipo."',
					NOW(),
					'".$asunto."',
					'".$rowCLI['email']."',
          '0',
          '',
          0
				)";
		mysql_query($sql);
		//echo $sql.'  Seguimiento<br>';
    }
	}

