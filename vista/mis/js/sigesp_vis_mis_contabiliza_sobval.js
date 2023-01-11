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
var	fromContabilizaSobval = null;

Ext.onReady(function(){

	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//creando store para la afectacion
	var reTipoDocumento = Ext.data.Record.create([
          {name: 'codtipdoc'},
          {name: 'dentipdoc'}
    ]);
	
	var dsTipoDocumento =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reTipoDocumento)				
	});
	//fin creando store para el combo afectacion
	
	//creando objeto combo afectacion
	var cmbTipoDocumento = new Ext.form.ComboBox({
		store: dsTipoDocumento,
		labelSeparator :'',
		fieldLabel:'Tipo de Documento',
		displayField:'dentipdoc',
		valueField:'codtipdoc',
		name: 'tipo_documento',
		width:250,
		listWidth: 250, 
		id:'tipo_doc',
		typeAhead: true,
		emptyText:'----Seleccione----',
		selectOnFocus:true,
		mode:'local',
		triggerAction:'all',
		valor:''
	});
	//Fin creando objeto combo afectacion

	//Funcion que agrega los datos al combo de afectacion
    function llenarComboTipoDocumento()
	{
    	var myJSONObject ={
				"operacion": 'buscar_tipodocumento_valuacion'
		};	
		var ObjSon=JSON.stringify(myJSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) { 
				var datos = resultado.responseText;
				if(datos!='')
				{
					var Datos = eval('(' + datos + ')');
				}
				dsTipoDocumento.loadData(Datos);
			}//fin del success
		});//fin del ajax request	
	}
	
	var reSolicitud = Ext.data.Record.create([
	    {name: 'codcon'},                      
	    {name: 'codval'}, 
	    {name: 'fecha'},
	    {name: 'obsval'},
	    {name: 'fecinival'},
	    {name: 'fechacontacontrato'},
	    {name: 'detalle'},
	    {name: 'cod_pro'},
	    {name: 'nompro'}
	]);

	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});

	var cmSolicitud = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Nro. Contrato</CENTER>", width: 40, sortable: true, dataIndex: 'codcon'},
	    {header: "<CENTER>Nro. Valuaci&#243;n</CENTER>", width: 40, sortable: true, dataIndex: 'codval'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha'},
	    {header: "<CENTER>Observaci&#243;n</CENTER>", width: 50, sortable: true, dataIndex: 'obsval'},
	]);

	//creando datastore y columnmodel para la grid de valuaciones
	gridSolicitud = new Ext.grid.GridPanel({
		width:570,
		height:200,
		frame:true,
		title:"<H1 align='center'>Valuaciones por Contabilizar</H1>",
		style: 'position:absolute;left:15px;top:250px',
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
					{header: "<CENTER>Estatus</CENTER>", width: 60, sortable: true, dataIndex: 'estcla'},
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
								etiqueta:'Contrato Asociado',
								id:'ndoc',
								valor: registro.get('codcon'),
								ancho: 200 
								},
						        {	
								tipo:'textfield',
								etiqueta:'Valuaci&#243;n',
								id:'fmov',
								valor:registro.get('codval'),
								ancho: 100
								},
								{	
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fval',
								valor:registro.get('fecinival'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'cmov',
								valor:registro.get('obsval'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Proveedor',
								id:'promov',
								valor:registro.get('cod_pro')+" - "+registro.get('nompro'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Contabilizaci&#243;n',
								id:'conmov',
								valor:'CAUSA',
								ancho: 300
								},
						],
						tienePresupuesto:true,
						tituloGridPresupuestario:"<H1 align='center'>Detalle Presupuestario de Gasto</H1>",
						anchoGridPG :680,
						altoGridPG :150,
						dsPresupuestoGasto: dsMovBancario,
						cmPresupuestoGasto: cmMovBancario,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
						paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto_val',
																		  'codcon':registro.get('codcon'),
																		  'codigo':registro.get('codval')}),
						tieneContable: true,
						anchoGridCO :680,
						altoGridCO :100, 
						paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_contable_val',
																	    'codcon':registro.get('codcon'),
																		'codant':registro.get('codval'),
																		'codpro':registro.get('cod_pro')})
																   
				});
				//fin creando componente detalle comprobante
				comMovBancario.mostrarVentana();
			}
		}
	});

	fromContabilizaSobval = new Ext.form.FieldSet({
		    title:'Datos de la Valuaci&#243;n',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 570,
			cls: 'fondo',
			height: 220,
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
					border: false,
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:140px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 200,			
							items: [{
									layout: "form",
									border: false,
									labelWidth: 150,			
									items: [cmbTipoDocumento]
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
									fieldLabel: 'Nro. de Valuaci&#243;n',
									id: 'codval',
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
									id:"feccon",
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
									fieldLabel:"Fecha de Valuaci&#243;n",
									labelSeparator :'',
									name:"fecinival",
									allowBlank:false,
									width:100,
									id:"fecval",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:170px',
					items: [{
							layout: "form",
							border: false,					
							labelWidth: 150,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha de Contabilizaci&#243;n",
									labelSeparator :'',
									name:"fechaconta",
									allowBlank:false,
									width:100,
									id:"fechaconta",
									value:new Date().format('d/m/Y'),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	})
	//Creacion del formulario
	var Xpos = ((screen.width/2)-(300));
	plContabilizaSobval = new Ext.FormPanel({
		applyTo: 'formulario',
		width:623,
		height: 450,
		title: "<H1 align='center'>Contabilizaci&#243;n de Valuaciones</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;',
		items: [fromContabilizaSobval,gridSolicitud]
	});	
	fromContabilizaSobval.doLayout();
	llenarComboTipoDocumento();
});

function irCancelar()
{
	limpiarFormulario(fromContabilizaSobval);
	gridSolicitud.store.removeAll();
}


function irProcesar()
{
	grid = gridSolicitud.getSelectionModel().getSelections();
	fecha = Ext.getCmp('fechaconta').getValue().format('Y-m-d');
	total = grid.length;
	tipo_doc = Ext.getCmp('tipo_doc').getValue();
	if(Ext.getCmp('tipo_doc').getValue()=='')
	{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe llenar el Tipo de Documento, para procesar la informaci&#243;n !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		cadenajson = "{'operacion':'contabilizar_sobval','codsis':'"+sistema+"','nomven':'"+vista+"', 'codtipdoc':'"+tipo_doc+"', 'feccon':'"+fecha+"', 'arrDetalle':[";
		if(total>0)
		{
			for(var i=0; i<total; i++)
			{
				for (i=0; i<total; i++)
				{ 
					if (i==0) 
					{
						cadenajson += "{'codcon':'"+grid[i].get('codcon')+"','codval':'"+grid[i].get('codval')+"'," +
								       "'fecha':'"+grid[i].get('fecha')+"','obsval':'"+grid[i].get('obsval')+"'," +
									   "'fechacontacontrato':'"+grid[i].get('fechacontacontrato')+"'}";                
					}
					else
					{
						cadenajson += ",{'codcon':'"+grid[i].get('codcon')+"','codval':'"+grid[i].get('codval')+"'," +
								       "'fecha':'"+grid[i].get('fecha')+"','obsval':'"+grid[i].get('obsval')+"'," +
									   "'fechacontacontrato':'"+grid[i].get('fechacontacontrato')+"'}";                
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
						tituloVentana: 'Resultado Contabilizaci&#243;n de Valuaciones',
						anchoLabel: 200,
						labelTotal:'Total valuaciones procesados',
						valorTotal: arrResultado[0],
						labelProcesada:'Total valuaciones contabilizados',
						valorProcesada:arrResultado[1],
						labelError:'Total valuaciones con error',
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
}

function irBuscar()
{
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar valuaciones
	var codcon = Ext.getCmp('codcon').getValue();
	var codval = Ext.getCmp('codval').getValue();
	var feccon = Ext.getCmp('feccon').getValue();
	var fecval = Ext.getCmp('fecval').getValue();

	var JSONObject = {
			'operacion' : 'buscar_sobval',
			'codcon'    : codcon,
			'codval'    : codval,
			'feccon'    : feccon,
			'fecval'    : fecval,
			'estatus'   : '0'
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
			var objetoSobval = eval('(' + datos + ')');
			if(objetoSobval!=''){
				if(objetoSobval!='0'){
					if(objetoSobval.raiz == null || objetoSobval.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						gridSolicitud.store.loadData(objetoSobval);
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
		}	
	});
}