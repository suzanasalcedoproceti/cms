$(document).ready(function() {

    $('#res').fixedHeaderTable({ footer: false, cloneHeadToFoot: false, altClass: 'odd', autoShow: true, fixedColumns: 1 });

	$.tabs("graficas");

	$("#link_filtro").click(function(event){
		event.preventDefault();
		$("#seleccionar_filtros").fadeIn("fast");
		$("#link_filtro").css('display','none');
	});
	$("#cerrar_filtro").click(function(event){
		event.preventDefault();
		$("#seleccionar_filtros").fadeOut("fast");
		$("#link_filtro").css('display','block');
	});
	$('#forma_dashboard').submit(function(){
		$("#buscando").val("1");
	});

	$('#fecha_ini, #fecha_fin').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif' } );

  	$('#fechas').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2} );

  	$('#fecha_fac').datepick({showOn: 'both', buttonImageOnly: true, buttonImage: 'images/calendario.gif', rangeSelect: true, numberOfMonths: 2} );

	// loaded();


	$("#link_graficas").click(function(event){
		event.preventDefault();
		$("#mostrar_graficas").fadeIn("fast");
		$("#link_graficas").css('display','none');
//		chart.draw(data1, options1);
		xajax_grafica(document.forma_dashboard.eje_x.value, document.forma_dashboard.eje_y.value, document.forma_dashboard.serie_x.value);
	});
	$("#cerrar_graficas").click(function(event){
		event.preventDefault();
		$("#mostrar_graficas").fadeOut("fast");
		$("#link_graficas").css('display','block');
	});

	$("#link_filtro").click(function(event){
		event.preventDefault();
		$("#seleccionar_filtros").fadeIn("fast");
		$("#link_filtro").css('display','none');
	});


});
