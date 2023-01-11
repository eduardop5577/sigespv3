/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var frmPagos = null;  //instancia del formulario de pagos
var Actualizar = null;

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//--------------------------------------------------------------------------------------------
	// Combo del A�o
	var anio = [ 
				 [ '2011', '2011' ],
				 [ '2012', '2012' ],
				 [ '2013', '2013' ],
				 [ '2014', '2014' ],
				 [ '2015', '2015' ],
				 [ '2016', '2016' ],
				 [ '2017', '2017' ], 
	             [ '2018', '2018' ],
				 [ '2019', '2019' ],
				 [ '2020', '2020' ],
				 [ '2021', '2021' ],
				 [ '2022', '2022' ],
				 [ '2023', '2023' ],
				 [ '2024', '2024' ],
				 [ '2025', '2025' ],
				 [ '2026', '2026' ],
				 [ '2027', '2027' ],
				 [ '2028', '2028' ],
				 [ '2029', '2029' ],
				 [ '2030', '2030' ]
				 ];
	
	var staniodes = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : anio
	}); //Fin de store para el tipo de impresion

	//creando objeto combo filtrar
	var cmbaniodes = new Ext.form.ComboBox({
		store : staniodes,
		fieldLabel : 'A&#241;o',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'anio',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});

	//--------------------------------------------------------------------------------------------------------------------------------	
	// Combo mes desde (MES)
	var mesdesde = [ 
				 [ 'Enero', '01' ], 
	             [ 'Febrero', '02' ],
				 [ 'Marzo', '03' ],
				 [ 'Abril', '04' ],
				 [ 'Mayo', '05' ],
				 [ 'Junio', '06' ],
				 [ 'Julio', '07' ],
				 [ 'Agosto', '08' ],
				 [ 'Septiembre', '09' ],
				 [ 'Octubre', '10' ],
				 [ 'Noviembre', '11' ],
				 [ 'Diciembre', '12' ],
				 ];
	
	var stmesdesde = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : mesdesde
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmesdesde = new Ext.form.ComboBox({
		store : stmesdesde,
		fieldLabel : 'Mes',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'mesdes',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});
	
	//**********************************************************************************************************************************
	//creacion del formulario de datos de intervalo de fechas
	fieldset = new Ext.form.FieldSet({
		width: 700,
		height: 70,
		title: "Periodo",
		style: 'position:absolute;left:10px;top:55px', //150
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:200px;top:15px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:30,
						items: [cmbmesdesde]
					}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:370px;top:15px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:30,
						items: [cmbaniodes]
					}]
				}]
	});	
//----------------------------------------------------------------------------------------------------------------------------------

	var opcimp = [ 
				 [ 'PDF', 'P' ], 
	             [ 'EXCEL', 'E' ] 
				 ];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtiporeporte = new Ext.form.ComboBox({
		store : stOpcimp,
		fieldLabel : 'Tipo Impresi&#243;n',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipoimp',
		width : 150,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbtiporeporte.setValue('P');

//----------------------------------------------------------------------------------------------------------------------------------	
	//Creacion del formulario del reporte
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 733,
	height: 300,
	title: "<H1 align='center'>Movimientos Cuentas de Patrimonio</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [
	        fieldset,
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:240px;top:10px',
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:100,
					items: [cmbtiporeporte]
					}]
			}]	
	});
	irNuevo();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************
function irNuevo(){
	
}

function irImprimir()
{
	var pagina = "";
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		pagina = "reportes/sigesp_scg_rpp_movimientopatrimonio.php";
	}
	else
	{
		pagina = "reportes/sigesp_scg_rpp_movimientopatrimonio_excel.php";	
	}
	
	cmbmes  = Ext.getCmp('mesdes').getValue(); 
	cmbagno = Ext.getCmp('anio').getValue();
	pagina = pagina+"?cmbmes="+cmbmes+"&cmbagno="+cmbagno;
	window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
}
