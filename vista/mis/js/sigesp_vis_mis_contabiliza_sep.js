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

//creando datastore y columnmodel para la grid de solicitudes
var reSolicitud = Ext.data.Record.create([
    {name: 'numsol'}, 
    {name: 'fecregsol'},
    {name: 'consol'},
    {name: 'fechaconta'},
    {name: 'nompro'},
    {name: 'cod_pro'},
    {name: 'nombene'},
    {name: 'apebene'},
    {name: 'ced_bene'},
    {name: 'tipo_destino'},
    {name: 'fechanula'}
]);

var dsSolicitud =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
});

var cmSolicitud = new Ext.grid.ColumnModel([
    new Ext.grid.CheckboxSelectionModel(),
    {header: "<CENTER>Nro Solicitud</CENTER>", width: 20, sortable: true, dataIndex: 'numsol'},
    {header: "<CENTER>Fecha</CENTER>", width: 25, sortable: true, dataIndex: 'fecregsol'},
    {header: "<CENTER>Concepto</CENTER>", width: 30, sortable: true, dataIndex: 'consol'}
]);

//creando datastore y columnmodel para la grid de solicitudes
var gridSolicitud = new Ext.grid.GridPanel({
	width:880,
	height:250,
	frame:true,
	title:'',
	style: 'position:absolute;left:10px;top:215px',
	autoScroll:true,
	border:true,
	ds: dsSolicitud,
	cm: cmSolicitud,
	sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	stripeRows: true,
	viewConfig: {forceFit:true}
});
//fin creando grid para las solicitudes

gridSolicitud.on({
	'rowcontextmenu': {
		fn: function(grid, numFila, evento){
			var registro = grid.getStore().getAt(numFila);
		
			//creando datastore y columnmodel para la grid de detalles presupuestarios
			var reMovPresupuestario = Ext.data.Record.create([
			    {name: 'estructura'}, 
			    {name: 'estcla'},
			    {name: 'spg_cuenta'},
			    {name: 'denominacion'},
			    {name: 'operacion'},
			    {name: 'monto'},
			    {name: 'disponibilidad'}
			]);
		
			var dsMovPresupuestario =  new Ext.data.Store({
				reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovPresupuestario)
			});
		
			var cmMovPresupuestario = new Ext.grid.ColumnModel([
			    {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
			    {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			    {header: "Cuenta", width: 60, sortable: true, dataIndex: 'spg_cuenta'},
			    {header: "Denominacion", width: 100, sortable: true, dataIndex: 'denominacion'},
			    {header: "Monto", width: 40, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			    {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
			]);
			//fin creando datastore y columnmodel para la grid de detalles presupuestarios
			if(registro.get('tipo_destino')=='B')
			{
				var tit_dest="Beneficiario";
				var nombre_dest=registro.get('ced_bene')+" - "+registro.get('apebene')+", "+registro.get('nombene');
			}
			else
			{
				var tit_dest="Proveedor";
				var nombre_dest=registro.get('cod_pro')+" - "+registro.get('nompro');
			}
		
			//creando componente detalle comprobante
			var comDetalleModificacion = new com.sigesp.vista.comDetalleComprobante({
				tituloVentana: 'Contabilizacion de Solicitud de Ejecucion Presupuestaria',
				anchoVentana: 720,
				altoVentana: 500,
				anchoFormulario: 680,
				altoFormulario:150,
				arrCampos:[{
				tipo:'textfield',
				etiqueta:'Comprobante',
				id:'cmpmod',
				valor: registro.get('numsol'),
				ancho: 100 
				},
				{
				tipo:'textfield',
				etiqueta:'Fecha',
				id:'fecmod',
				valor:registro.get('fecregsol'),
				ancho: 200
				},
				{	
				tipo:'textarea',
				etiqueta:'Descripci&#243;n',
				id:'cmpdes',
				valor:registro.get('consol'),
				ancho: 350
				},
				{
				tipo:'textfield',
				etiqueta:tit_dest,
				id:'prosoc',
				valor:nombre_dest,
				ancho: 300
				}],
				tienePresupuesto:true,
				tituloGridPresupuestario:'Detalle Presupuestario de Gasto',
				anchoGridPG :680,
				altoGridPG :150,
				dsPresupuestoGasto: dsMovPresupuestario,
				cmPresupuestoGasto: cmMovPresupuestario,
				rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsep.php',
				paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles',
					'numsol':registro.get('numsol')}),
				tieneContable: false,		
			});
			//fin creando componente detalle comprobante
			comDetalleModificacion.mostrarVentana();
		}
	}
});


//creando store para el destino
var destino = [
               ['Proveedor','P'],
               ['Beneficiario','B']
               ]; // Arreglo que contiene los Documentos que se pueden controlar

var stdestino = new Ext.data.SimpleStore({
	fields : [ 'etiqueta', 'valor' ],
	data : destino
});
//fin creando store para el combo tipo iva

//creando objeto combo tipo iva
var cmbdestino = new Ext.form.ComboBox({
	store : stdestino,
	fieldLabel : 'Destino ',
	labelSeparator : '',
	editable : false,
	displayField : 'etiqueta',
	valueField : 'valor',
	id : 'cmbdestino',
	width:130,
	typeAhead: true,
	emptyText:'Seleccione',
	triggerAction:'all',
	forceselection:true,
	binding:true,
	mode:'local',
	listeners: {'select':CatalogoDestino}
});
//-------------------------------------------------------------------------------------------------------------------------					
//-------------------------------------------------------------------------------------------------------------------------	

var datosNuevo={'raiz':[{'cod_pro':'','nompro':''}]};	

//creando funcion que construye formulario principal Contabilizar
var	fromBusquedaSEP = new Ext.form.FieldSet({
	    title:'Datos de la Solicitud',
	    style: 'position:absolute;left:10px;top:10px',
		border:true,
		width: 920,
		cls: 'fondo',
		height: 180,
		items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 150,
						items: [{
								xtype: 'textfield',
								fieldLabel: 'Numero Solicitud',
								labelSeparator :'',
								id: 'numsol',
								autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
								width: 130,
								listeners: {
									'onClick': function(){
								}			
								},
								allowBlank:false
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:40px',
				items: [{
						layout: "form",
						border: true,
						labelWidth: 150,
						items: [cmbdestino]
						},
						{
						layout: "form",
						border: false,
						labelWidth: 10,
						items: [{
								xtype: 'textfield',
								fieldLabel: '',
								labelSeparator :'',
								id: 'cod_pro',
								disabled:true,
								autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
								width: 130,
								listeners: {
									'onClick': function(){
									}			
								},
								allowBlank:false
							}]
						},
						{
						layout: "form",
						border: false,
						labelWidth: 10,
						items: [{
								xtype: 'textfield',
								fieldLabel: '',
								labelSeparator :'',
								id: 'nompro',
								disabled:true,
								autoCreate: {tag: 'input',type: 'text',size: '15',autocomplete: 'off',maxlength: '15'},
								width: 300,
								listeners: {
									'onClick': function(){
								}			
								},
								allowBlank:false
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:70px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 150,
						items: [{
								xtype: 'datefield',
								labelSeparator :'',
								fieldLabel:"Fecha de Registro",
								name:"Fecregdoc",
								allowBlank:true,
								width:130,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								id:"fecregdoc",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:100px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 150,
						items: [{
								xtype: 'datefield',
								labelSeparator :'',
								fieldLabel:"Fecha de Aprobacion",
								name:"Fecaprdoc",
								allowBlank:true,
								width:130,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								id:"fecaprdoc",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
				
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:20px;top:130px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 150,
						items: [{
								xtype: 'datefield',
								labelSeparator :'',
								fieldLabel:"Fecha de Contabilizacion",
								name:"Fechaconta",
								allowBlank:true,
								width:130,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								id:"fechaconta",
								value:fecha_hoy,
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
							}]
						}]
				}]
});


/***********************************************************************************
/***********************************************************************************
 * @Funcion para buscar los Proveedores o Beneficiarios segun sea el caso
 * @parametros: 
 * @retorno:
 * @fecha de creacion: 04/07/2012.
 * @autor: Ing. Luis Anibal Lang.
 ************************************************************************************
 * @fecha modificacion:
 * @descripcion:
 * @autor:
 ***********************************************************************************/		
function CatalogoDestino()
{
	valor=Ext.getCmp('cmbdestino').getValue();
	if(valor=="P")
	{
		//creando datastore y columnmodel para el catalogo de agencias
		var registro_parametro = Ext.data.Record.create([
		    {name: 'cod_pro'},
		    {name: 'nompro'}
		]);

		var dsparametro =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_parametro)
		});

		var colmodelcatparametro = new Ext.grid.ColumnModel([
		    {header: "Codigo", width: 20, sortable: true,   dataIndex: 'cod_pro'},
		    {header: "Nombre", width: 40, sortable: true, dataIndex: 'nompro'}
		]);
		//fin creando datastore y columnmodel para el catalogo de agencias

		comcatproveedor = new com.sigesp.vista.comCatalogo({
			titvencat: 'Catalogo de Proveedores',
			anchoformbus: 450,
			altoformbus:130,
			anchogrid: 450,
			altogrid: 400,
			anchoven: 500,
			altoven: 400,
			datosgridcat: dsparametro,
			colmodelocat: colmodelcatparametro,
			arrfiltro:[{etiqueta:'Codigo',id:'copro',valor:'codpro'},
			           {etiqueta:'Descripcion',id:'nopro',valor:'nompro'}],
           rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
           parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'catalogo'}),
           tipbus:'L',
           setdatastyle:'F',
           formulario:fromBusquedaSEP
		});
		comcatproveedor.mostrarVentana();
	}
	else
	{
		//creando datastore y columnmodel para el catalogo de agencias
		var registro_parametro = Ext.data.Record.create([
		    {name: 'ced_bene'},
		    {name: 'nombene'}
		]);

		var dsparametro =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_parametro)
		});

		var colmodelcatparametro = new Ext.grid.ColumnModel([
		    {header: "Cedula", width: 20, sortable: true,   dataIndex: 'ced_bene'},
		    {header: "Nombre", width: 40, sortable: true, dataIndex: 'nombene'}
		]);
		//fin creando datastore y columnmodel para el catalogo de agencias

		comcatproveedor = new com.sigesp.vista.comCatalogo({
			titvencat: 'Catalogo de Beneficiario',
			anchoformbus: 450,
			altoformbus:130,
			anchogrid: 450,
			altogrid: 400,
			anchoven: 500,
			altoven: 400,
			datosgridcat: dsparametro,
			colmodelocat: colmodelcatparametro,
			arrfiltro:[{etiqueta:'Codigo',id:'copro',valor:'ced_bene'},
			           {etiqueta:'Nombre',id:'conom',valor:'nombene'}],
           rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
           parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscarBeneficiarios'}),
           tipbus:'L',
           setdatastyle:'F',
           formulario:fromBusquedaSEP
		});
		comcatproveedor.mostrarVentana();
	}
}

barraherramienta    = true;
var fechaconta = new Date(Ext.getCmp('fechaconta').getValue());
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var Xpos = ((screen.width/2)-(920/2));
	var	fromContabilzarSEP = new Ext.FormPanel({
		applyTo: 'formularioSEP',
		width: 920,
		height: 600,
		style: 'position:absolute;left:40px;top:80px',
		title: "<H1 align='center'>Contabilizacion de Solicitud de Ejecucion Presupuestaria</H1>",
		frame: true,
		autoScroll:true,
		items: [
		        fromBusquedaSEP,
		        gridSolicitud
		        ]
	});


	fromContabilzarSEP.doLayout();
	
	
});


function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	var numsol      = Ext.getCmp('numsol').getValue();
	var destino     = Ext.getCmp('cmbdestino').getValue();
	var cod_pro     = Ext.getCmp('cod_pro').getValue();
	var nompro      = Ext.getCmp('nompro').getValue();
	var fecregdoc   = Ext.getCmp('fecregdoc').getValue();
	var fecaprdoc   = Ext.getCmp('fecaprdoc').getValue();
	var fechaconta  = Ext.getCmp('fechaconta').getValue();

	var JSONObject = {
			'operacion' : 'buscar_por_contabilizar',
			'numsol'    : numsol,
			'tipo'      : destino,
			'codigo'    : cod_pro,
			'fecreg'    : fecregdoc,
			'fecapr'    : fecaprdoc
	}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsep.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoSep = eval('(' + datos + ')');
			if(objetoSep!=''){
				if(objetoSep!='0'){
					if(objetoSep.raiz == null || objetoSep.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridSolicitud.store.removeAll();
					}
					else{
						dsSolicitud.loadData(objetoSep);
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
			Ext.MessageBox.alert('Error', 'Error de comunicacion con el Servidor'); 
		}
	});
}

function irCancelar(){
	limpiarFormulario(fromBusquedaSEP);
	gridSolicitud.store.removeAll();	
}


function irProcesar(){
	valido=true;
	fecha=Ext.getCmp('fechaconta').getValue().format('Y/m/d');
	arrSolicitud = gridSolicitud.getSelectionModel().getSelections();
	total = arrSolicitud.length;
	cadenajson = "{'operacion':'contabilizar','codsis':'"+sistema+"','nomven':'"+vista+"','fecha':'"+fecha+"','arrDetalle':[";
	if (total>0)
	{			
		for (i=0; i<total; i++)
		{
			if (i==0) 
			{
				cadenajson += "{'numsol':'"+arrSolicitud[i].get('numsol')+"'}";                
			}
			else {
				cadenajson += ",{'numsol':'"+arrSolicitud[i].get('numsol')+"'}";                
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsep.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: "<H1 align='center'>Resultado Contabilizaci&#243;n de Solicitudes de Ejecuci&#243;n Presupuestaria</H1>",
					anchoLabel: 200,
					labelTotal:'Total solicitudes procesadas',
					valorTotal: arrResultado[0],
					labelProcesada:'Total solicitudes contabilizadas',
					valorProcesada:arrResultado[1],
					labelError:'Total solicitudes con error',
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
				Ext.MessageBox.alert('Error', 'Error al procesar la Informacion'); 
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