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
var ruta ='../../controlador/scg/sigesp_ctr_scg_estado_resultado.php'; //ruta del controlador
var fechaPrimera = obtenerPrimerDiaMes();
var formato1 = '';
var cencos = '';

barraherramienta = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//--------------------------------------------------------------------------------------------------------------------------------	
//**********************************************************************************************************************************	
//                                     		INICIO DEL FORMULARIO PROVEEDOR / BENEFICIARIO	
//**********************************************************************************************************************************
// Combo del A�o
	var anio = Ext.data.Record.create([
	    {name:'anuales'}
	]);

	var staniohas =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "anuales"},anio)
	});
	
	//creando objeto combo filtrar
	var cmbaniohas = new Ext.form.ComboBox({
		store : staniohas,
		fieldLabel : '',
		labelSeparator : '',
		editable : false,
		displayField : 'anuales',
		valueField : 'anuales',
		id : 'anioh',
		width : 90,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		emptyText:'Seleccione',
		mode : 'local'
	});
// Combo del A�o
//--------------------------------------------------------------------------------------------------------------------------------	
// Combo mes hasta (MES)
var meshasta = [ 
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
	
	var stmeshasta = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : meshasta
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbmeshasta = new Ext.form.ComboBox({
		store : stmeshasta,
		fieldLabel : 'Hasta',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'meshas',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local',
		emptyText:'Seleccione'
	});

	fieldsetIntervaloFechasMeses = new Ext.form.FieldSet({
			title:"Mes Hasta",
			style: 'position:absolute;left:10px;top:25px',
			border:true,
			width: 600,
			cls :'fondo',
			height: 75,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbmeshasta]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:150px;top:20px',
					border:false,
					items: [{
							layout:"form",
							border:false,
							labelWidth:50,
							items: [cmbaniohas]
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

	//creando funcion para llenar el combo
	function llenarComboMesHasAnio()
	{
		var myJSONObject ={
				"operacion": 'comboanio'
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datosc = resultado.responseText;  
				if(datosc!='')
				{
					var DatosAnio = eval('(' + datosc + ')');
					staniohas.loadData(DatosAnio);
				}
			}
		});
	}
	//cmbformato.setValue('1');
//----------------------------------------------------------------------------------------------------------------------------------

//Creacion del formulario pagos
	var Xpos = ((screen.width/2)-(380));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 630,
	height: 200,
	title: "<H1 align='center'>Rendimiento Financiero</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [	
        	fieldsetIntervaloFechasMeses,
        	{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:360px;top:5px',
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:100,
					items: [cmbtiporeporte]
					}]
			}]	
	});
	llenarComboMesHasAnio();
});
//----------------------------------------------------------------------------------------------------------------------------------
//**********************************************************************************************************************************	
//                                  INICIO DE FUNCIONES PARA LOS CATALOGOS DE BUSQUEDA Y VALIDACIONES 
//**********************************************************************************************************************************

function irImprimir()
{
	var pagina="";
	if (Ext.getCmp('tipoimp').getValue()=='P')
	{
		pagina = "reportes/sigesp_scg_rpp_rendimientofinanciero.php";		
	}
	else
	{
		pagina = "reportes/sigesp_scg_rpp_rendimientofinanciero_excel.php";
	}
	cmbmesdes  = '01';
	cmbmeshas  = Ext.getCmp('meshas').getValue();
	cmbagnodes = Ext.getCmp('anioh').getValue();
	cmbagnohas = Ext.getCmp('anioh').getValue();
	hidbot=1;
	if((cmbagnodes=="")||(cmbagnohas=="")||(cmbmesdes=="")||(cmbmeshas==""))
	{
		Ext.Msg.hide();
		alert ("Debe seleccionar los Parametros de Busqueda");
	}
	else
	{
		pagina = pagina+"?cmbmesdes="+cmbmesdes+"&cmbmeshas="+cmbmeshas+"&cmbagnodes="+cmbagnodes+"&cmbagnohas="+cmbagnohas+"&hidbot="+hidbot;
		window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
	}
}
