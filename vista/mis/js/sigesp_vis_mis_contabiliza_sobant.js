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
var fromContabilizaSobval = null;
var gridSolicitud = null;

Ext.onReady(function(){

	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	
	var date = new Date();
	var dia = date.getDate();
	var mes = date.getMonth()+1;
	var anio = date.getFullYear();
	if (mes < 10)
	{
		mes="0"+mes;
	}
	if (dia < 10)
	{
		dia="0"+dia;
	}
	
	var fecha_hoy=dia+"/"+mes+"/"+anio;

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
				"operacion": 'buscar_tipodocumento'
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
    
	//-------------------------------------------------------------------------------------------------------------------------	
   
    var reSolicitud = Ext.data.Record.create([
	    {name: 'codcon'},                      
	    {name: 'codant'}, 
	    {name: 'fecant'},
	    {name: 'fechaconta'},
	    {name: 'fechaanulada'},
	    {name: 'monto'},
	    {name: 'fechacontacontrato'},
	    {name: 'cod_pro'},
	    {name: 'nompro'}
	]);

	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});

	var cmSolicitud = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Nro Contrato</CENTER>", width: 40, sortable: true, dataIndex: 'codcon'},
	    {header: "<CENTER>Nro Anticipo</CENTER>", width: 40, sortable: true, dataIndex: 'codant'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecant'},
	    {header: "<CENTER>Monto</CENTER>", width: 50, sortable: true, dataIndex: 'monto'},
	]);

	//creando datastore y columnmodel para la grid de anticipos
	gridSolicitud = new Ext.grid.GridPanel({
		width:570,
		height:250,
		frame:true,
		title:"<H1 align='center'>Anticipos por Contabilizar</H1>",
		style: 'position:absolute;left:15px;top:245px',
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
								etiqueta:'Contrato',
								id:'ndoc',
								valor: registro.get('codcon'),
								ancho: 200 
								},
						        {	
								tipo:'textfield',
								etiqueta:'Anticipo',
								id:'fmov',
								valor:registro.get('codant'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Proveedor',
								id:'cmov',
								valor:registro.get('cod_pro')+" - "+registro.get('nompro'),
								ancho: 300
								}],
						tienePresupuesto:true,
						tituloGridPresupuestario:"<H1 align='center'>Detalle Presupuestario de Gasto</H1>",
						anchoGridPG :680,
						altoGridPG :150,
						dsPresupuestoGasto: dsMovBancario,
						cmPresupuestoGasto: cmMovBancario,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
						paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto_ant',
																		  'codcon':registro.get('codcon'),
																		  'codigo':registro.get('codant')}),
						tieneContable: true,
						anchoGridCO :680,
						altoGridCO :100, 
						paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_contable_ant',
																	    'codcon':registro.get('codcon'),
																		'codant':registro.get('codant'),
																		'codpro':registro.get('cod_pro')})
																   
				});
				//fin creando componente detalle comprobante
				comMovBancario.mostrarVentana();
			}
		}
	});

	fromContabilizaSobant = new Ext.form.FieldSet({
		    title:'Datos del Anticipo',
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
									fieldLabel: 'Código de Contrato',
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
					style: 'position:absolute;left:15px;top:170px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,			
							items: [cmbTipoDocumento]
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
									fieldLabel: 'Código de Anticipo',
									id: 'codant',
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
									name:"feccon",
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
									fieldLabel:"Fecha de Anticipo",
									labelSeparator :'',
									name:"fecant",
									allowBlank:false,
									width:100,
									id:"fecant",
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:140px',
					items: [{
							layout: "form",
							border: false,					
							labelWidth: 150,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha de Contabilizaci&#243;n",
									labelSeparator :'',
									name:"fecha",
									allowBlank:false,
									width:100,
									id:"fechaconta",
									value:fecha_hoy,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	})
	//Creacion del formulario
	var Xpos = ((screen.width/2)-(300));
	plContabilizaSobant = new Ext.FormPanel({
		applyTo: 'formulario',
		width:623,
		height: 520,
		title: "<H1 align='center'>Contabilizaci&#243;n de Anticipos</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',

		items: [fromContabilizaSobant,gridSolicitud]
	});	
	plContabilizaSobant.doLayout();
	llenarComboTipoDocumento();
});

function irCancelar(){
	limpiarFormulario(fromContabilizaSobval);
	gridSolicitud.store.removeAll();
}

function irProcesar()
{
	grid = gridSolicitud.getSelectionModel().getSelections();
	fecha = Ext.getCmp('fechaconta').getValue().format('Y-m-d');
	total = grid.length;
	tipo_doc = '';
	if(Ext.getCmp('tipo_doc').getValue()=='')
	{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe llenar el Tipo de Documento, para procesar la informaci&#243;n !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else
	{
		tipo_doc =Ext.getCmp('tipo_doc').getValue();
		cadenajson = "{'operacion':'contabilizar_sobant','codsis':'"+sistema+"','nomven':'"+vista+"', 'codtipdoc':'"+tipo_doc+"', 'feccon':'"+fecha+"', 'arrDetalle':[";
		if(total>0)
		{
			for(var i=0; i<total; i++)
			{
				for (i=0; i<total; i++)
				{ 
					if (i==0) 
					{
						cadenajson += "{'codcon':'"+grid[i].get('codcon')+"','codant':'"+grid[i].get('codant')+"'," +
								       "'fecant':'"+grid[i].get('fecant')+"','fechaconta':'"+grid[i].get('fechaconta')+"'," +
									   "'fechaanulada':'"+grid[i].get('fechaanulada')+"','monto':'"+grid[i].get('monto')+"'," +
						               "'fechacontacontrato':'"+grid[i].get('fechacontacontrato')+"','cod_pro':'"+grid[i].get('cod_pro')+"'}";                
					}
					else
					{
						cadenajson += ",{'codcon':'"+grid[i].get('codcon')+"','codant':'"+grid[i].get('codant')+"'," +
								       "'fecant':'"+grid[i].get('fecant')+"','fechaconta':'"+grid[i].get('fechaconta')+"'," +
									   "'fechaanulada':'"+grid[i].get('fechaanulada')+"','monto':'"+grid[i].get('monto')+"'," +
						               "'fechacontacontrato':'"+grid[i].get('fechacontacontrato')+"','cod_pro':'"+grid[i].get('cod_pro')+"'}";                
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
						tituloVentana: 'Resultado Contabilizaci&#243;n de Anticipos',
						anchoLabel: 200,
						labelTotal:'Total anticipos procesados',
						valorTotal: arrResultado[0],
						labelProcesada:'Total anticipos contabilizados',
						valorProcesada:arrResultado[1],
						labelError:'Total anticipos con error',
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
	//buscar anticipos
	var codcon   = Ext.getCmp('codcon').getValue();
	var codant  = Ext.getCmp('codant').getValue();
	var feccon	 = Ext.getCmp('feccon').getValue();
	var fecant	 = Ext.getCmp('fecant').getValue();

	var JSONObject = {
			'operacion'   : 'buscar_sobant',
			'codcon'      : codcon,
			'codant'      : codant,
			'feccon'      : feccon,
			'fecant'      : fecant,
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
			var objetoSobant = eval('(' + datos + ')');
			if(objetoSobant!=''){
				if(objetoSobant!='0'){
					if(objetoSobant.raiz == null || objetoSobant.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
					}
					else{
						gridSolicitud.store.loadData(objetoSobant);
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
			Ext.MessageBox.alert('Error', 'Error de comunicación con el Servidor'); 
		}	
	});
}