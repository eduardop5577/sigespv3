/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta = true;
var gridSolicitud = null;
var	fromContabilizaSobvar = null;

Ext.onReady(function(){

	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	

	var reSolicitud = Ext.data.Record.create([
	    {name: 'codcon'},                      
	    {name: 'codvar'}, 
	    {name: 'fecvar'},
	    {name: 'monvar'},
	    {name: 'fechacontacontrato'},
	    {name: 'cod_pro'},
	    {name: 'obscon'},
	    {name: 'nompro'},
	    {name: 'codasi'},
	    {name: 'detalle'}
	]);

	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});

	var cmSolicitud = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Nro. Contrato</CENTER>", width: 40, sortable: true, dataIndex: 'codcon'},
	    {header: "<CENTER>Nro. Variaci&#243;n</CENTER>", width: 40, sortable: true, dataIndex: 'codvar'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecvar'},
	    {header: "<CENTER>Observaci&#243;n</CENTER>", width: 50, sortable: true, dataIndex: 'obscon'}
	]);

	//creando datastore y columnmodel para la grid de variaciones
	gridSolicitud = new Ext.grid.GridPanel({
		width:570,
		height:250,
		frame:true,
		title:"<H1 align='center'>Variaciones por Contabilizar</H1>",
		style: 'position:absolute;left:15px;top:200px',
		autoScroll:true,
		border:true,
		ds: dsSolicitud,
		cm: cmSolicitud,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	
	// Creando la ventana emergente de los detalles
	gridSolicitud.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovBancario = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spg_cuenta'},
				    {name: 'denominacion'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsMovBancario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovBancario)
				});
									
				var cmMovBancario = new Ext.grid.ColumnModel([
			        {header: "<CENTER>Estructura</CENTER>", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "<CENTER>Estatus</CENTER>", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "<CENTER>Cuenta</CENTER>", width: 60, sortable: true, dataIndex: 'spg_cuenta'},
			        {header: "<CENTER>Denominacion</CENTER>", width: 100, sortable: true, dataIndex: 'denominacion'},
			        {header: "<CENTER>Monto</CENTER>", width: 40, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			        {header: "<CENTER>Disponibilidad</CENTER>", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				
				//creando componente detalle comprobante
				var comMovBancario = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: "<H1 align='center'>Informaci&#243;n del Comprobante</H1>",
					anchoVentana: 720,
					altoVentana: 500,
					anchoFormulario: 680,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Contrato',
								id:'ndoc',
								valor: registro.get('codcon'),
								ancho: 200 
								},
						        {	
								tipo:'textfield',
								etiqueta:'Variaci&#243;n',
								id:'fmov',
								valor:registro.get('codvar'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'cmov',
								valor:registro.get('fecvar'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'tip_mov',
								valor:registro.get('obscon'),
								ancho: 300
								},
							    {
								tipo:'textfield',
								etiqueta:'Proveedor',
								id:'b_mov',
								valor:registro.get('cod_pro')+" - "+registro.get('nompro'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Contabilizaci&#243;n',
								id:'promov',
								valor:'COMPROMISO',
								ancho: 300
					}],
					
					tienePresupuesto:true,
					tituloGridPresupuestario:"<H1 align='center'>Detalle Presupuestario de Gasto</H1>",
					anchoGridPG :680,
					altoGridPG :150,
					dsPresupuestoGasto: dsMovBancario,
					cmPresupuestoGasto: cmMovBancario,
					rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto',
																	  'codasi':registro.get('codvar'),
																	  'codcon':registro.get('codcon')}), 
																   
				});  
				//fin creando componente detalle comprobante
				comMovBancario.mostrarVentana();
			}
		}
	});

	fromContabilizaSobvar = new Ext.form.FieldSet({
		    title:'Datos de la Variaci&#243;n',
		    style: 'position:absolute;left:15px;top:5px',
			border:true,
			width: 570,
			cls: 'fondo',
			height: 170,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Nro. de Contrato',
									id: 'codcon',
									width: 150,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '15'}
								}]
							}]
					}, 
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:50px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Nro. de Variaci&#243;n',
									id: 'codvar',
									width: 150,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '15'}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:80px',
					items: [{
							layout: "form",
							border: false,					
							labelWidth: 150,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha del Contrato",
									labelSeparator :'',
									name:"fecha",
									allowBlank:false,
									width:100,
									id:"fecha",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:110px',
					items: [{
							layout: "form",
							border: false,					
							labelWidth: 150,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha de Variaci&#243;n",
									labelSeparator :'',
									name:"fecvar",
									allowBlank:false,
									width:100,
									id:"fecvar",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	})
	//Creacion del formulario
	var Xpos = ((screen.width/2)-(300));
	plContabilizaSobvar = new Ext.FormPanel({
		applyTo: 'formulario',
		width:613,
		height: 500,
		title: "<H1 align='center'>Contabilizaci&#243;n de Variaciones</H1>",
		frame:true,
		autoScroll:false,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		items: [fromContabilizaSobvar,gridSolicitud]
	});	
	plContabilizaSobvar.doLayout();
});
function irCancelar(){
	limpiarFormulario(fromContabilizaSobvar);
	gridSolicitud.store.removeAll();
}
function irAnular(){

}

function irProcesar(){
	grid = gridSolicitud.getSelectionModel().getSelections();
	cadenajson = "{'operacion':'contabilizar_sobvar','codsis':'"+sistema+"','nomven':'"+vista+"', 'arrDetalle':[";
	total = grid.length;
	if(total>0)
	{
		for(var i=0; i<total; i++)
		{
			for (i=0; i<total; i++)
			{ 
				if (i==0) 
				{
					cadenajson += "{'codcon':'"+grid[i].get('codcon')+"','codvar':'"+grid[i].get('codvar')+"'," +
							       "'fecvar':'"+grid[i].get('fecvar')+"','monvar':'"+grid[i].get('monvar')+"'," +
							       "'fechacontacontrato':'"+grid[i].get('fechacontacontrato')+"'," +
							       "'cod_pro':'"+grid[i].get('cod_pro')+"'}";                
				}
				else
				{
					cadenajson += ",{'codcon':'"+grid[i].get('codcon')+"','codvar':'"+grid[i].get('codvar')+"'," +
							       "'fecvar':'"+grid[i].get('fecvar')+"','monvar':'"+grid[i].get('monvar')+"'," +
							       "'fechacontacontrato':'"+grid[i].get('fechacontacontrato')+"'," +
							       "'cod_pro':'"+grid[i].get('cod_pro')+"'}";                
				}
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Contabilizaci&#243;n de Variaciones',
					anchoLabel: 200,
					labelTotal:'Total variaciones procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total variaciones contabilizados',
					valorProcesada:arrResultado[1],
					labelError:'Total variaciones con error',
					valorError:arrResultado[2],
					tituloGrid:'Detalle de Resultados',
					dataDetalle:arrResultado[3]
				});
				//fin creando componente detalle comprobante
				
				comResultado.mostrarVentana();
				irCancelar();
			},
			failure: function (result,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
			}					
		});
	}
	else{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe seleccionar al menos un documento a procesar !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}

function irDescargar(){

}

function irImprimir(){

}
function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar variaciones
	var codcon  = Ext.getCmp('codcon').getValue();
	var codvar  = Ext.getCmp('codvar').getValue();
	var fecha	= Ext.getCmp('fecha').getValue();
	var fecvar	= Ext.getCmp('fecvar').getValue();

	var JSONObject = {
			'operacion'   : 'buscar_sobvar',
			'codcon'      : codcon,
			'codvar'      : codvar,
			'feccon'      : fecha,
			'fecvar'      : fecvar,
			'estatus'     : '0'
	}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoSobvar = eval('(' + datos + ')');
			if(objetoSobvar!=''){
				if(objetoSobvar!='0'){
					if(objetoSobvar.raiz == null || objetoSobvar.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						gridSolicitud.store.loadData(objetoSobvar);
					}
				}
				else{
					Ext.MessageBox.show({
						title:'Advertencia',
		 				msg:'Debe configurar en Empresa los digitos de las cuentas de gastos',
		 				buttons: Ext.Msg.OK,
		 				icon: Ext.MessageBox.WARNING
		 			});
				}
			}
		},
		failure: function (result,request) 
		{ 
			Ext.MessageBox.alert('Error', 'Error de comunicación con el servidor'); 
		}
	});


}