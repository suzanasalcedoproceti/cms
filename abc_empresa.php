<?php
// Cambios
// Nov 2015
//    Precios especiales configurables en config
// Oct 2016 : B+ : Se agregan meses sin intereses (3,9,10,12,18,24) para NetPay

    if (!include('ctrl_acceso.php')) return;
  include('funciones.php');
  $modulo=6;
  if (!op($modulo))  {
    $aviso = 'Usuario sin permiso para acceder a este módulo';
    $aviso_link = 'principal.php';
    include('mensaje_sistema.php');
    return;
  }
  $texto = $_GET['texto'];
  if (!$texto) $texto = $_POST['texto'];
  
  function get_nombre_lista($lista) {
    include("../conexion.php");
    $resLIS = mysql_query("SELECT nombre FROM lista_precios WHERE clave = '$lista'",$conexion);
    $rowLIS = mysql_fetch_assoc($resLIS);
    return $rowLIS['nombre']; 
  }

    function get_nombre_lista_tienda($lista) {
    include("../conexion.php");
    $resLIS = mysql_query("SELECT nombre FROM tienda WHERE clave = '$lista'",$conexion);
    $rowLIS = mysql_fetch_assoc($resLIS);
    return $rowLIS['nombre']; 
  }

  function get_nombre_lista_producto($lista) {
    include("../conexion.php");
    $resLIS = mysql_query("SELECT modelo FROM producto WHERE clave = '$lista'",$conexion);
    $rowLIS = mysql_fetch_assoc($resLIS);
    return $rowLIS['modelo']; 
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
<script src="js/jquery_1.10.js"></script>
<script src="js/jquery-ui.js"></script>

<script language="JavaScript">
  function valida() {
   if (document.forma.estatus.value == "") {
     alert("Falta indicar el esatus de la empresa.");
   document.forma.estatus.focus();
     return;
   }   
   if (document.forma.ftipo.value == "") {
     alert("Falta indicar el tipo de cliente.");
   document.forma.ftipo.focus();
     return;
   }   
   if (document.forma.nombre.value == "") {
     alert("Falta empresa.");
   document.forma.nombre.focus();
     return;
   }
   if (document.forma.lista_precios.value == "") {
     alert("Falta indicar la lista de precios que le corresponde para WEB.");
   document.forma.lista_precios.focus();
     return;
   }
   if (document.forma.lista_precios_pos.value == "") {
     alert("Falta indicar la lista de precios que le corresponde para POS.");
   document.forma.lista_precios_pos.focus();
     return;
   }
   if (document.forma.cliente_sap.value == "") {
     alert("Falta No. de Cliente SAP.");
   document.forma.cliente_sap.focus();
     return;
   }
   if (document.forma.invita_amigos.checked && document.forma.lista_precios_invitados.value == "") {
    alert("Debes indicar la lista de precios para invitados");
  document.forma.lista_precios_invitados.focus();
  return;
   }
   $(":input").not("[name=estatus], [name=desc], [name=grabar]")
        .prop("disabled", false);
  // combina claves de divisiones en un string separado por comas
  var string_di = '';
  for (var i=0; i < document.forma.lista_divisiones.length; i++) {
    string_di += ' '+document.forma.lista_divisiones.options[i].value+',';
  }
  document.forma.divisiones.value = string_di;

  // combina claves de listas de precios POS seleccionadas en un string separado por comas
  var string_lis = '';
  for (var i=0; i < document.forma.lista_listas.length; i++) {
    string_lis += document.forma.lista_listas.options[i].value+',';
  }
  document.forma.listas_permitidas_pos.value = string_lis;

  var string_lis = '';
  for (var i=0; i < document.forma.tienda_listas_sel.length; i++) {
    string_lis += document.forma.tienda_listas_sel.options[i].value+',';
  }
  document.forma.pos_cb_tiendas.value = string_lis;

  var string_lis = '';
  for (var i=0; i < document.forma.cb_productos_sel.length; i++) {
    string_lis += document.forma.cb_productos_sel.options[i].value+',';
  }
  document.forma.pos_cb_productos.value = string_lis;

   document.forma.action='graba_empresa.php';
   document.forma.submit();
  }
  function rechaza() {
   document.forma.action='borra_empresa.php';
   document.forma.submit();
  }
  function descarta() {
   document.forma.action='lista_empresa.php';
   document.forma.submit();
  }
  function agregaDiv(inForm,texto,valor) {
    var siguiente = inForm.lista_divisiones.options.length;
    var encontrado = false;
    for (var i=0; i < inForm.lista_divisiones.length; i++) {
      if (inForm.lista_divisiones.options[i].value == valor) {
        encontrado = true;
      }
    }
    if (!encontrado) {
      eval("inForm.lista_divisiones.options[siguiente]=" + "new Option(texto,valor,false,true)");
    }
  }
  function eliminaDiv(inForm,indice) {
    var i = inForm.lista_divisiones.options.length;
    inForm.lista_divisiones.options[indice] = null;
  }
  function agregaLis(inForm,texto,valor) {
    var siguiente = inForm.lista_listas.options.length;
    var encontrado = false;
    for (var i=0; i < inForm.lista_listas.length; i++) {
      if (inForm.lista_listas.options[i].value == valor) {
        encontrado = true;
      }
    }
    if (!encontrado) {
      eval("inForm.lista_listas.options[siguiente]=" + "new Option(texto,valor,false,true)");
    }
  }
  function agregaLisTiendas(inForm,texto,valor) {
    var siguiente = inForm.tienda_listas_sel.options.length;
    var encontrado = false;
    for (var i=0; i < inForm.tienda_listas_sel.length; i++) {
      if (inForm.tienda_listas_sel.options[i].value == valor) {
        encontrado = true;
      }
    }
    if (!encontrado) {
      eval("inForm.tienda_listas_sel.options[siguiente]=" + "new Option(texto,valor,false,true)");
    }
  }
  function agregaLisProductos(inForm,texto,valor) {
    var siguiente = inForm.cb_productos_sel.options.length;
    var encontrado = false;
    for (var i=0; i < inForm.cb_productos_sel.length; i++) {
      if (inForm.cb_productos_sel.options[i].value == valor) {
        encontrado = true;
      }
    }
    if (!encontrado) {
      eval("inForm.cb_productos_sel.options[siguiente]=" + "new Option(texto,valor,false,true)");
    }
  }
  function eliminaLis(inForm,indice) {
    var i = inForm.lista_listas.options.length;
    inForm.lista_listas.options[indice] = null;
  }
  function eliminaLisTiendas(inForm,indice) {
    var i = inForm.tienda_listas_sel.options.length;
    inForm.tienda_listas_sel.options[indice] = null;
  }
    function eliminaLisProductos(inForm,indice) {
    var i = inForm.cb_productos_sel.options.length;
    inForm.cb_productos_sel.options[indice] = null;
  }
<?php
$empresa=$_POST['empresa'];
if (empty($empresa)) $empresa=$_GET['empresa'];
?>
$(document).ready(function () {
  if ($('#estatus').val()==1) {
               $(":input").not("[name=estatus], [name=desc] <?php if (!empty($empresa))  echo ',[name=grabar]'; ?>")
        .prop("disabled", false);
            }
            else {
                $(":input").not("[name=estatus], [name=desc] <?php if (!empty($empresa))  echo ',[name=grabar]'; ?>")
        .prop("disabled", true);
            }


        $('#estatus').change(function () {
            if ($('#estatus').val()==1) {
               $(":input").not("[name=estatus], [name=desc] <?php if (!empty($empresa))  echo ',[name=grabar]'; ?>")
        .prop("disabled", false);
            }
            else {
                $(":input").not("[name=estatus], [name=desc] <?php if (!empty($empresa))  echo ',[name=grabar]'; ?>")
        .prop("disabled", true);
            }
        });
    });
jQuery.fn.filterByText = function(textbox) {
    return this.each(function() {
        var select = this;
        var options = [];
        $(select).find('option').each(function() {
            options.push({value: $(this).val(), text: $(this).text()});
        });
        $(select).data('options', options);

        $(textbox).bind('change keyup', function() {
            var options = $(select).empty().data('options');
            var search = $.trim($(this).val());
            var regex = new RegExp(search,"gi");

            $.each(options, function(i) {
                var option = options[i];
                if(option.text.match(regex) !== null) {
                    $(select).append(
                        $('<option>').text(option.text).val(option.value)
                    );
                }
            });
        });
    });
};
var cat = null;
var subcat = null;
$(function() {
    $('#cb_productos').filterByText($('#filtro_prod'));
    $('#cb_categoria').click(function(){
      cat = this.value;
      if(cat=='todas')
      {
        $('#cb_subcategoria option').each(function(){
           $(this).show();
           });
        $('#cb_productos option').each(function(){
           $(this).show();
           });
      }
      else
      {
        $('#cb_subcategoria option').each(function(){
          rel = $(this).attr('rel');
            if(cat == rel){
              $(this).show();
            }else{
              $(this).hide();
            }
        });
      }
    });


    $('#cb_subcategoria').click(function(){
      subcat = this.value;
      $('#cb_productos option').each(function(){
        rel = $(this).attr('rel');
          if(subcat == rel){
            $(this).show();
          }else{
            $(this).hide();
          }
      });

    });
});

</script>
</head>

<body>
<div id="container">
  <? $tit='Administrar Empresas'; include('top.php'); ?>
  <?
        include('../conexion.php');

    $empresa=$_POST['empresa'];
    $autorizar=$_GET['autorizar'];
    
    if (empty($empresa)) $empresa=$_GET['empresa'];
        
        if (!empty($empresa)) {
          $resultado= mysql_query("SELECT * FROM empresa WHERE clave='$empresa'",$conexion);
          $row = mysql_fetch_array($resultado);
        }

        
      ?>
  <div class="main">
      <form action="" method="post" name="forma" id="forma">
        <table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" class="texto">

          <tr>
            <td><div align="right"><strong>Datos Generales</strong></div></td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">* Estatus:</div></td>
            <td colspan="8"><select name="estatus" class="campo" id="estatus">
            <option value="" <? if ($row['estatus']=='') echo 'selected';?>>Selecciona...</option>
            <option value="0" <? if ($row['estatus']=='0') echo 'selected';?>>Inactivo</option>
            <option value="1" <? if ($row['estatus']=='1') echo 'selected';?>>Activo</option>
            </select></td>
          </tr>
                    <tr>
            <td><div align="right">* Tipo de clientes:</div></td>
            <td colspan="8"><select name="ftipo" class="campo" id="ftipo">
              <option value="" <? if ($row['cliente_tipo_id']=='') echo 'selected';?>>Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM cliente_tipo",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['id'].'"';
            if ($row['cliente_tipo_id']==$rowLP['id']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td width="17%"><div align="right">* Empresa:</div></td>
            <td colspan="8"><input name="nombre" type="text" class="campo" id="nombre" value="<?= $row['nombre']; ?>" size="115" maxlength="100" />            </td>
          </tr>
          <tr>
            <td><div align="right">RFC:</div></td>
            <td colspan="8"><input name="rfc" type="text" class="campo" id="rfc" value="<?= $row['rfc']; ?>" size="20" maxlength="15" /></td>
          </tr>
          <tr>
            <td><div align="right">* Lista de precios WEB:</div></td>
            <td colspan="8"><select name="lista_precios" class="campo" id="lista_precios">
              <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_precios']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td><div align="right">* Lista de precios POS (default):</div></td>
            <td colspan="8"><select name="lista_precios_pos" class="campo" id="lista_precios_pos">
                <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_precios_pos']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td align="right">Listas de precios seleccionables en POS (incluir la default):</td>
            <td colspan="8"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Listas de precios disponibles:</strong><br />
                  (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Listas  seleccionadas:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td><select name="cat_listas" size="5" class="campo" id="cat_listas" ondblclick="agregaLis(document.forma,this.options[this.selectedIndex].text,this.value);" style="width:180px">
                   <?php
              $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
            while ($rowLP = mysql_fetch_assoc($resLP)) {
              echo '<option value="'.$rowLP['clave'].'">'.$rowLP['nombre'].'</option>';
              } // while
              
                      ?>
                </select></td>
                <td>&nbsp;</td>
                <td><select name="lista_listas" size="5" class="campo" id="lista_listas" ondblclick="eliminaLis(document.forma,this.selectedIndex);" style="min-width:200px;">
                  <?  $op=explode(',',$row['listas_permitidas_pos']);
            for ($i=0; $i<=count($op)-1; $i++) {
              $clavelis=trim($op[$i]);
              if ($clavelis)
              echo $CR.'<option value="'.$clavelis.'">'.get_nombre_lista($clavelis).'</option>';
            }
          ?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><div align="right">* No. Cliente SAP:</div></td>
            <td colspan="8"><input name="cliente_sap" type="text" class="campo" id="cliente_sap" value="<?= $row['cliente_sap']; ?>" size="20" maxlength="20" /> 
              (Dato usado para layout de exportaci&oacute;n de pedidos: PARTN_NUMB, y para Importaci&oacute;n de Puntos; En Proyectos es el valor de SOLD_TO)<br /></td>
          </tr>
          <tr>
            <td valign="top">&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td align="right" valign="top">Empresa de Proyectos?</td>
            <td colspan="8"><label>
              <input type="radio" name="empresa_proyectos" id="empresa_proyectos" value="1" <? if ($row['empresa_proyectos']==1) echo 'checked';?> />
              S&iacute;</label>
&nbsp;&nbsp;
<label>
  <input type="radio" name="empresa_proyectos" id="empresa_proyectos" value="0" <? if ($row['empresa_proyectos']==0) echo 'checked';?> />
  No </label>
&nbsp;&nbsp;Cuenta con Cr&eacute;dito? 
<label>
  <input type="radio" name="empresa_proyectos_credito" id="empresa_proyectos_credito" value="1" <? if ($row['empresa_proyectos_credito']==1) echo 'checked';?> />
  S&iacute;</label>
&nbsp;&nbsp;
<label>
  <input type="radio" name="empresa_proyectos_credito" id="empresa_proyectos_credito" value="0" <? if ($row['empresa_proyectos_credito']==0) echo 'checked';?> />
  No </label></td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Es empresa Whirlpool?</div></td>
            <td colspan="8"><label>
              <input type="radio" name="empresa_whirlpool" id="empresa_whirlpool" value="1" <? if ($row['empresa_whirlpool']==1) echo 'checked';?> />
S&iacute;</label>
&nbsp;&nbsp;
<label>
<input type="radio" name="empresa_whirlpool" id="empresa_whirlpool" value="0" <? if ($row['empresa_whirlpool']==0) echo 'checked';?> />
No </label>
             &nbsp;&nbsp;&nbsp;&nbsp;(La empresa Whirlpool maneja puntos, puntos Flex/PEP y l&iacute;mite de KAD mayores)</td>
          </tr>
          <tr>
            <td valign="top">&nbsp;</td>
            <td colspan="8"><strong>Solo para empresas Whirlpool:</strong></td>
          </tr>
          <tr>
            <td nowrap="nowrap"><div align="right">Lista de precios especiales POS:</div></td>
            <td colspan="8"><select name="lista_precios_especiales_pos" class="campo" id="lista_precios_especiales_pos">
              <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_precios_especiales_pos']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td nowrap="nowrap"><div align="right">Lista de precios especiales TW:</div></td>
            <td colspan="8"><select name="lista_precios_especiales_tw" class="campo" id="lista_precios_especiales_tw">
                <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_precios_especiales_tw']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select> 
              (Default, si el empleado no tuviera Banda)</td>
          </tr>
          <tr>
            <td colspan="9" valign="top"><strong>Lista de precios especiales por banda</strong></td>
          </tr>
          <tr>
            <td align="right">2</td>
            <td colspan="8"><select name="lista_pe_2" class="campo" id="lista_pe_2">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_2']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td align="right">3</td>
            <td colspan="8"><select name="lista_pe_3" class="campo" id="lista_pe_3">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_3']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td align="right">4</td>
            <td colspan="8">
            <select name="lista_pe_4" class="campo" id="lista_pe_4">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_4']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select>
            </td>
          </tr>
          <tr>
            <td align="right">5</td>
            <td colspan="8">
            <select name="lista_pe_5" class="campo" id="lista_pe_5">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_5']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select>
            </td>
          </tr>
          <tr>
            <td align="right">5A</td>
            <td colspan="8">
            <select name="lista_pe_5A" class="campo" id="lista_pe_5A">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_5A']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select>
            </td>
          </tr>
          <tr>
            <td align="right">6</td>
            <td colspan="8">
            <select name="lista_pe_6" class="campo" id="lista_pe_6">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_6']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select>
            </td>
          </tr>
          <tr>
            <td align="right">7</td>
            <td colspan="8">
            <select name="lista_pe_7" class="campo" id="lista_pe_7">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_7']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select>
            </td>
          </tr>
          <tr>
            <td align="right">8</td>
            <td colspan="8">
            <select name="lista_pe_8" class="campo" id="lista_pe_8">
              <option value="" selected="selected">Selecciona...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios WHERE exclusiva_proyectos = 0",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_pe_8']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>          
          <tr>
            <td colspan="3" valign="top"><strong>Cuadro Basico</strong></td>
          </tr>
          <tr>
            <td align="right">Seleccionar Tiendas:</td>
            <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Tiendas disponibles:</strong><br />
                  (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Tiendas seleccionadas:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td><select name="tiendas_listas" size="5" class="campo" id="tiendas_listas" ondblclick="agregaLisTiendas(document.forma,this.options[this.selectedIndex].text,this.value);" style="width:180px">
                   <?php
              $resLP = mysql_query("SELECT * FROM tienda",$conexion);
            while ($rowLP = mysql_fetch_assoc($resLP)) {
              echo '<option value="'.$rowLP['clave'].'">'.$rowLP['nombre'].'</option>';
              } // while
              
                      ?>
                </select></td>
                <td>&nbsp;</td>
                <td><select name="tienda_listas_sel" size="5" class="campo" id="tienda_listas_sel" ondblclick="eliminaLisTiendas(document.forma,this.selectedIndex);" style="min-width:200px;">
                  <?  
            $resLP = mysql_query("SELECT tienda FROM cuadro_basico WHERE empresa_clave=$empresa GROUP BY tienda",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['tienda'].'">'.get_nombre_lista_tienda($rowLP['tienda']).'</option>';
          } // while
          ?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td align="right">Seleccionar productos:</td>
            <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Categorias:</strong></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Subcategorias:</strong></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Productos:</strong><br />
                   
          <!-- VALG - 06.05.16 - BEGIN -->
          Buscar: <input type="text" name="filtro_prod" id="filtro_prod" >
          <!-- VALG - 06.05.16 - END -->  </td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Productos seleccionados:</strong></td>
              </tr>
              <tr>
                <td><select name="cb_categoria" size="5" class="campo" id="cb_categoria" style="width:180px">
                <option value="todas">TODAS</option>
                   <?php
              $resLP = mysql_query("SELECT * FROM categoria",$conexion);
            while ($rowLP = mysql_fetch_assoc($resLP)) {
              echo '<option value="'.$rowLP['clave'].'">'.$rowLP['nombre'].'</option>';
              } // while
              
                      ?>
                </select></td>
                <td>&nbsp;</td>
                <td><select name="cb_subcategoria" size="5" class="campo" id="cb_subcategoria" style="width:180px">
                <option value="todas">TODAS</option>
                   <?php
              $resLP = mysql_query("SELECT * FROM subcategoria",$conexion);
            while ($rowLP = mysql_fetch_assoc($resLP)) {
              echo '<option rel="'.$rowLP['categoria'].'" value="'.$rowLP['clave'].'">'.$rowLP['nombre'].'</option>';
              } // while
              
                      ?>
                </select></td>
                <td>&nbsp;</td>
                <td><select name="cb_productos" size="5" class="campo" id="cb_productos" ondblclick="agregaLisProductos(document.forma,this.options[this.selectedIndex].text,this.value);" style="width:180px">
                   <?php
              $resLP = mysql_query("SELECT * FROM producto",$conexion);
            while ($rowLP = mysql_fetch_assoc($resLP)) {
              echo '<option rel="'.$rowLP['subcategoria'].'" rel2="'.$rowLP['categoria'].'" value="'.$rowLP['clave'].'">'.$rowLP['modelo'].'</option>';
              } // while
              
                      ?>
                </select></td>
                <td>&nbsp;</td>
                <td><select name="cb_productos_sel" size="5" class="campo" id="cb_productos_sel" ondblclick="eliminaLisProductos(document.forma,this.selectedIndex);" style="min-width:200px;">
                  <?  
            $resLP = mysql_query("SELECT producto_clave FROM cuadro_basico WHERE empresa_clave=$empresa GROUP BY producto_clave",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['producto_clave'].'">'.get_nombre_lista_producto($rowLP['producto_clave']).'</option>';
          } // while
          ?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Lista de precios (Cuadro Basico) Precios especiales:</div></td>
            <td colspan="2"><select name="cb_lista_precios_especial" class="campo" id="cb_lista_precios_especial">
              <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php

          $resLP = mysql_query("SELECT precio_especial FROM cuadro_basico WHERE empresa_clave=$empresa GROUP BY precio_especial",$conexion);
          $row_cb_pe = mysql_fetch_row($resLP);
          $resLP = mysql_query("SELECT * FROM lista_precios",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row_cb_pe[0]==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right">Lista de precios Entrega Inmediata:</div></td>
            <td colspan="2"><select name="cb_lista_precios_entrega_inmediata" class="campo" id="cb_lista_precios_entrega_inmediata">
              <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php
              $resLP = mysql_query("SELECT precio_inmediata FROM cuadro_basico WHERE empresa_clave=$empresa GROUP BY precio_inmediata",$conexion);
          $row_cb_pe = mysql_fetch_row($resLP);
          $resLP = mysql_query("SELECT * FROM lista_precios",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row_cb_pe[0]==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top"><div align="right">Divisi&oacute;n de Personal:</div></td>
            <td colspan="8"><table border="0" cellpadding="0" cellspacing="0" class="texto">
              <tr>
                <td><strong>Divisiones disponibles:</strong><br />
                  (dbl clic para seleccionar)</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td><strong>Divisiones  seleccionadas:</strong><br />
                  (dbl clic para eliminar de la lista)</td>
              </tr>
              <tr>
                <td>
                  <select name="cat_divisiones" size="10" class="campo" id="cat_divisiones" ondblclick="agregaDiv(document.forma,this.options[this.selectedIndex].text,this.value);" style="width:180px">
                    <?    
                $CR = chr(10);
              $query = "SELECT division FROM division_personal ORDER BY division";
                $resDIV = mysql_query($query,$conexion);
                          while ($rowDIV = mysql_fetch_array($resDIV)) { 
                      echo $CR.'<option value="'.$rowDIV['division'].'" >'.$rowDIV['division'].'</option>';
                          }
                      ?>
                  </select>
                </td>
                <td>&nbsp;</td>
                <td><select name="lista_divisiones" size="10" class="campo" id="lista_divisiones" ondblclick="eliminaDiv(document.forma,this.selectedIndex);" style="min-width:200px;">
                    <?  
              $query = "SELECT division FROM empresa_division WHERE empresa = '$empresa' ORDER BY division";
                $resDIV = mysql_query($query,$conexion);
                          while ($rowDIV = mysql_fetch_array($resDIV)) { 
                      echo $CR.'<option value="'.$rowDIV['division'].'" >'.$rowDIV['division'].'</option>';
                          }
          ?>
                </select></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td valign="top">&nbsp;</td>
            <td colspan="7">&nbsp;</td>
            <td valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td valign="top"><div align="right">
              <p>Lista de dominios  autorizados<br />
                para correo de registro:</p>
              </div></td>
            <td width="27%" colspan="7"><textarea name="dominio" cols="45" rows="5" class="campo" id="dominio"><?=$row['dominio'];?></textarea></td>
            <td width="56%" valign="top"><strong>Instrucciones:</strong><br />
              Para dominios libres, dejar en blanco.<br />
              Para dominio &uacute;nico, poner el dominio en primer rengl&oacute;n &uacute;nicamente<br />
            Para m&uacute;ltiples dominios, poner un dominio por rengl&oacute;n<br />
            No incluir @ en dominios</td>
          </tr>
          <tr>
            <td><div align="right">P&uacute;blico en general:</div></td>
            <td colspan="8"><input name="empresa_publica" type="checkbox" id="empresa_publica" value="1" <? if ($_SESSION['usr_service']==1) echo 'readonly onclick="javascript: return false;"'; ?> <? if ($row['empresa_publica']==1||$_SESSION['usr_service']==1) echo 'checked'; ?> /></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"><strong>Familiares y Amigos</strong></div></td>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8"><input name="invita_amigos" type="checkbox" id="invita_amigos" value="1" <? if ($row['invita_amigos']) echo 'checked';?>/>
              Los empleados de esta empresa pueden invitar<strong> Familiares y Amigos</strong></td>
          </tr>
          <tr>
            <td><div align="right">Con la lista de precios:</div></td>
            <td colspan="8"><select name="lista_precios_invitados" class="campo" id="lista_precios_invitados">
                <option value="" selected="selected">Selecciona Lista de Precios...</option>
              <?php
          $resLP = mysql_query("SELECT * FROM lista_precios",$conexion);
          while ($rowLP = mysql_fetch_assoc($resLP)) {
            echo '<option value="'.$rowLP['clave'].'"';
            if ($row['lista_precios_invitados']==$rowLP['clave']) echo 'selected';
            echo '>'.$rowLP['nombre'].'</option>';
          } // while
        ?>
            </select> 
            (Ya no aplica; se toma lista de precios de Invitados Whirlpool)</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"><strong>Meses Sin Intereses</strong></div></td>
            <td align="center"><strong>3</strong></td>
            <td align="center"><strong>6</strong></td>
            <td align="center"><strong>9</strong></td>
            <td align="center"><strong>10</strong></td>
            <td align="center"><strong>12</strong></td>
            <td align="center"><strong>18</strong></td>
            <td align="center"><strong>24</strong></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td align="center"><input name="msi03" type="checkbox" id="msi03" value="1" <? if ($row['msi03']) echo 'checked';?>/></td>
            <td align="center"><input name="msi06" type="checkbox" id="msi06" value="1" <? if ($row['msi06']) echo 'checked';?>/></td>
            <td align="center"><input name="msi09" type="checkbox" id="msi09" value="1" <? if ($row['msi09']) echo 'checked';?>/></td>
            <td align="center"><input name="msi10" type="checkbox" id="msi10" value="1" <? if ($row['msi10']) echo 'checked';?>/></td>
            <td align="center"><input name="msi12" type="checkbox" id="msi12" value="1" <? if ($row['msi12']) echo 'checked';?>/></td>
            <td align="center"><input name="msi18" type="checkbox" id="msi18" value="1" <? if ($row['msi18']) echo 'checked';?>/></td>
            <td align="center"><input name="msi24" type="checkbox" id="msi24" value="1" <? if ($row['msi24']) echo 'checked';?>/></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"><strong>Puntos para Empleados</strong></div></td>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8"><input name="puntos" type="checkbox" id="puntos" value="1" <? if ($row['puntos']) echo 'checked';?>/>
              Los empleados de esta empresa generan y usan puntos</td>
          </tr>
          <? if (empty($autorizar)) { ?>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"><strong>Inter&eacute;s para Orden de Compra:</strong></div></td>
            <td colspan="8"><select name="tipo_interes" class="campo" id="tipo_interes">
                <option value="" selected="selected">Selecciona el interés...</option>
                <option value="a" <? if ($row['tipo_interes']=='a') echo 'selected';?>>A</option>
                <option value="b" <? if ($row['tipo_interes']=='b') echo 'selected';?>>B</option>
            </select></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td><div align="right"><strong>Combos de Productos</strong></div></td>
            <td colspan="7">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8"><input name="combos" type="checkbox" id="combos" value="1" <? if ($row['combos']) echo 'checked';?>/>
              Esta empresa ve combos de productos</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8"><input name="grabar" type="button" class="boton" onclick="valida();" value="GRABAR" />
                <input name="desc" type="button" class="boton" onclick="descarta();" value="REGRESAR AL LISTADO" id="desc" />
            <input name="empresa" type="hidden" id="empresa" value="<?= $empresa; ?>" />            
            <input name="divisiones" type="hidden" id="divisiones" value="<?= $divisiones; ?>" />            
            <input name="texto" type="hidden" id="texto" value="<?= $texto; ?>" />            
            <input name="listas_permitidas_pos" type="hidden" id="listas_permitidas_pos" value="" />            
            <input name="pos_cb_tiendas" type="hidden" id="pos_cb_tiendas" value="" />            
            <input name="pos_cb_productos" type="hidden" id="pos_cb_productos" value="" />          
            </td>
          </tr>
          <? } else { ?>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8"><input name="grabar" type="button" class="boton" onclick="valida();" value="AUTORIZAR" />
              <input name="rechazar" type="button" class="boton" onclick="rechaza();" value="RECHAZAR" />
                <input name="empresa" type="hidden" id="empresa" value="<?= $empresa; ?>" /> 
              <input name="divisiones" type="hidden" id="divisiones" value="<?= $divisiones; ?>" />            
              <input name="texto" type="hidden" id="texto" value="<?= $texto; ?>" />            
              <input name="autorizar" type="hidden" id="autorizar" value="<?= $autorizar; ?>" />
                <input name="pos_cb_tiendas" type="hidden" id="pos_cb_tiendas" value="" />            
            <input name="pos_cb_productos" type="hidden" id="pos_cb_productos" value="" />           </td>
          </tr>
          <? } ?>
          <tr>
            <td>&nbsp;</td>
            <td colspan="8">&nbsp;</td>
          </tr>
        </table>
      </form>    
    </div>
</div>
</body>
</html>
