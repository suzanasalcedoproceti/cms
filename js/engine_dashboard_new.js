$(document).ready(function() {

    $('#res').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'odd', autoShow: true, fixedColumns: 1 });

	$.tabs("graficas");

	$(".link_filtro").click(function(event){
		event.preventDefault();
		$(".pantalla").fadeIn("fast");
		$("#seleccionar_filtros").fadeIn("fast");
		$(".pantalla").css({'height': $(document).height() - 150});
	});

	$(".pantalla, .cerrar_filtro").click(function() {
		$(".pantalla").fadeOut("fast");
		$("#seleccionar_filtros").fadeOut("fast");
	});


	$("#cerrar_filtro").click(function(event){
		event.preventDefault();
		$('.pantalla').hide();
		$("#seleccionar_filtros").fadeOut("fast");
	});
	$('#forma_dashboard').submit(function(){
		$("#buscando").val("1");
	});

	$('#fecha_ini, #fecha_fin').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif' } );

  	$('#fechas_ini').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/icons/calendar.png', rangeSelect: true, numberOfMonths: 2} );

	$('#fechas').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2} );

  	$('#fecha_fac').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2} );

	// loaded();

	$(".btn_tabla").click(function(event){
		event.preventDefault();
		$("#tabla").show();
		$("#mostrar_graficas").hide();
	});
	$(".btn_grafica").click(function(event){
		event.preventDefault();
		$("#tabla").hide();
		xajax_grafica(document.forma_dashboard.eje_x.value, document.forma_dashboard.eje_y.value, document.forma_dashboard.serie_x.value);
		$("#mostrar_graficas").show();
	});



	$("#link_filtro").click(function(event){
		event.preventDefault();
		$("#seleccionar_filtros").fadeIn("fast");
		$("#link_filtro").css('display','none');
	});


});
