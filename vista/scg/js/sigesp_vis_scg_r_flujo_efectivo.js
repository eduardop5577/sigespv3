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
		width: 705,
		height: 120,
		title: "Acumulado hasta",
		style: 'position:absolute;left:10px;top:45px', //150
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
				},{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:0px;top:55px',
					border:false,
					items: [{
						layout:"form",
						border:false,
						items: [{
							xtype: "radiogroup",
							fieldLabel: "",
							labelSeparator:"",
							binding:true,
							hiddenvalue:'',
							defaultvalue:'',	
							columns: [250,250],
							id:'formato',
							items: [{
								boxLabel: 'Variaci&#243;n', name: 'formato', inputValue: '1', checked:true,
							},{
								boxLabel: 'Detallado', name: 'formato', inputValue: '2',
							}]
						}]
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
	var Xpos = ((screen.width/2)-(375));
	frmPagos = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 750,
	height: 300,
	title: "<H1 align='center'>Flujo de Efectivo</H1>",
	frame: true,
	autoScroll: true,
	style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [fieldset,{
		layout: "column",
		defaults: {border: false},
		style: 'position:absolute;left:40px;top:10px',
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
	var formato = null;
	var radio= Ext.getCmp('formato');
	for (var j = 0; j < radio.items.length; j++) {
		if (radio.items.items[j].checked) {
			formato = radio.items.items[j].inputValue;
			break;
		}
	}
	
	var pagina = "";
	if (Ext.getCmp('tipoimp').getValue()=='P') {
		if (formato == 1) {
			pagina = "reportes/sigesp_scg_rpp_flujoefectivovariacion.php";
		} else {
			pagina = "reportes/sigesp_scg_rpp_flujoefectivodetallado.php";
		}
	}
	else {
		if (formato == 1) {
			pagina = "reportes/sigesp_scg_rpp_flujoefectivovariacion_excel.php";
		} else {
			pagina = "reportes/sigesp_scg_rpp_flujoefectivodetallado_excel.php";
		}	
	}
	
	cmbmes  = Ext.getCmp('mesdes').getValue();
	if (cmbmes!="") {
		pagina = pagina+"?cmbmes="+cmbmes;
		window.open(pagina,"catalogo","menubar=yes,toolbar=yes,scrollbars=yes,width=800,height=600,resizable=yes,location=yes");
	} else {
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe seleccionar un mes !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	
}
