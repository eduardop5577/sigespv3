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

var gridSolicitud=null;
var fromRevContabilizaSafdep=null;
var fechaHoy=obtenerFechaActual();
var anioHoy=fechaHoy.substring(6,10);
var dsSolicitud=null;
barraherramienta = true;

Ext.onReady(function(){
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
//-------------------------------------------------------------------------------------------------------------------------	
//Creacion del formulario
	var Xpos = ((screen.width/2)-(920/2));
	p1RevContabilizaSafdep = new Ext.FormPanel({
	applyTo: 'formulario',
	width: 910,
	height: 450,
	title: "<H1 align='center'>Reverso de Contabilizaci&#243;n a la Depreciaci&#243;n de Activos Fijos</H1>",
	frame:true,
	autoScroll:false,
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
	items: [fromRevContabilizaSafdep,
	        gridSolicitud]
	});	
	p1RevContabilizaSafdep.doLayout();
    });
//-------------------------------------------------------------------------------------------------------------------------



var arregloMes = [
					 ['-- Seleccione --','00'], 
					 ['Enero','01'],
					 ['Febrero','02'],
					 ['Marzo','03'],
					 ['Abril','04'],
					 ['Mayo','05'],
					 ['Junio','06'],
					 ['Julio','07'],
					 ['Agosto','08'],
					 ['Septiembre','09'],
					 ['Octubre','10'],
					 ['Noviembre','11'],
					 ['Diciembre','12'],
					 ]; // Arreglo que contiene los Documentos que se pueden controlar

	 var dataStoreMes = new Ext.data.SimpleStore({
		   fields: ['denominacion','valor'],
		   data : arregloMes // Se asocian los documentos disponibles
		 });
		//creando objeto combo tipo documentos
	var cmbmes = new Ext.form.ComboBox({
		store : dataStoreMes,
		fieldLabel : 'Mes ',
		labelSeparator : '',
		editable : false,
		displayField : 'denominacion',
		valueField : 'valor',
		id : 'mes',
		width:100,
		typeAhead: true,
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local',
		emptyText : '-- Seleccione --'
	});

//-------------------------------------------------------------------------------------------------------------------------	
	var reSolicitud = Ext.data.Record.create([
	                    {name: 'monto'},                      
                  	    {name: 'anio'}, 
                  	    {name: 'mes'},                  	    
                  	    {name: 'estact'},
						{name: 'fechaconta'},
						{name: 'fechanula'},
						{name: 'descripcion'},
						{name: 'comprobante'}
        
                  	]);
                  	
	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});
                  						
	var cmSolicitud = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
		  {header: "<CENTER>Comprobante</CENTER>", width: 30, sortable: true, dataIndex: 'comprobante'},
		  {header: "<CENTER>Descripcion</CENTER>", width: 80, sortable: true, dataIndex: 'descripcion'},
		  {header: "<CENTER>Monto</CENTER>", width: 20, sortable: true, dataIndex: 'monto', align: 'right', editor: new Ext.form.NumberField({allowBlank: false})}
	]);
                  	
	//creando datastore y columnmodel para la grid de depreciaciones
	gridSolicitud = new Ext.grid.EditorGridPanel({
			width:850,
	 		height:250,
			frame:true,
			title:"<H1 align='center'>Depreciaciones por Reversar</H1>",
			style: 'position:absolute;left:15px;top:145px',
			autoScroll:true,
			border:true,
			ds: dsSolicitud,
			cm: cmSolicitud,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
			stripeRows: true,
			viewConfig: {forceFit:true}
	});
//-------------------------------------------------------------------------------------------------------------------------	
	// Creando la ventana emergente de los detalles
	
	gridSolicitud.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = gridSolicitud.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovBancario = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spg_cuenta'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsMovBancario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovBancario)
				});
									
				var cmMovBancario = new Ext.grid.ColumnModel([
			        {header: "Estructura", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "Estatus", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "Cuenta", width: 40, sortable: true, dataIndex: 'spg_cuenta'},
			        {header: "Monto", width: 50, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			        {header: "Disponibilidad", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				var tit='Detalle Presupuestario de Gasto';
				
				//creando componente detalle comprobante
				var comMovBancario = new com.sigesp.vista.comDetalleComprobante({
					tituloVentana: 'Contabilizaci&#243;n de Activos Fijos',
					anchoVentana: 600,
					altoVentana: 500,
					anchoFormulario: 580,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Comprobante',
								id:'ndoc',
								valor: registro.get('comprobante'),
								ancho: 120 
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'cmov',
								valor:registro.get('descripcion'),
								ancho: 450
								},
								{
								tipo:'textfield',
								etiqueta:'Monto Total',
								id:'tip_mov',
								valor:registro.get('monto'),
								ancho: 100
								},
							    {
								tipo:'textfield',
								etiqueta:'Contabilizaci&#243;n',
								id:'b_mov',
								valor:'COMPROMETE, CAUSA Y PAGA',
								ancho: 180
								}],
						tienePresupuesto:true,
						tituloGridPresupuestario:tit,
						anchoGridPG :580,
						altoGridPG :150,
						dsPresupuestoGasto: dsMovBancario,
						cmPresupuestoGasto: cmMovBancario,
						rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsaf.php',
						paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto_ing',
																	'anio':registro.get('anio'),
																	'mes':registro.get('mes')}),
						tieneContable: true,
						anchoGridCO :580,
						altoGridCO :150,
						paramContable: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_contable',
																	   'anio':registro.get('anio'),
																	   'mes':registro.get('mes')}),
																   
				});
				//fin creando componente detalle comprobante
				
				comMovBancario.mostrarVentana();
			}
		}
	});

//-----------------------------------------------------------------------------------------------------------------------	
      		fromRevContabilizaSafdep = new Ext.form.FieldSet({ 
		    title:'Parametros de Busqueda',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 850,
			cls: 'fondo',
			height: 110,
			items: [{
		      	   	layout: "column",
					border: false,
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [cmbmes]
				         	}]
			       }, 
				   {				  
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:55px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Año',
									id: 'anio',
									width: 50,
									binding:true,
									hiddenvalue:'',
									value:anioHoy,
									allowBlank:false,
									autoCreate: {tag: 'input',type: 'text',size: '10',autocomplete: 'off',maxlength: '4'}
								}]
						}]
				   }]
      	})
	

function irBuscar()
{
	var mes        = Ext.getCmp('mes').getValue();
	var anio       = Ext.getCmp('anio').getValue();
	var estatus	   = '1';

	if((mes=="")||(mes=='00')||(anio==""))
	{
		Ext.Msg.alert('Mensaje','Debe llenar los campos mes y a&#241;o!');
	}
	else
	{
		obtenerMensaje('procesar','','Buscando Datos');
		var JSONObject = {
				'operacion'   : 'buscar_por_rev_contabilizar_depsaf',
				'mes' : mes,
				'anio' : anio,
				'estatus' : estatus
			}
		var ObjSon = JSON.stringify(JSONObject);
		var parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsaf.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request){
				Ext.Msg.hide();
				var datos = resultado.responseText;
				var objetoSaf = eval('(' + datos + ')');
				if(objetoSaf!=''){
					if(objetoSaf!='0'){
						if(objetoSaf.raiz == null || objetoSaf.raiz ==''){
							Ext.MessageBox.show({
								title:'Advertencia',
								msg:'No existen datos para mostrar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.WARNING
			 				});
							gridSolicitud.store.removeAll();
						}
						else{
							dsSolicitud.loadData(objetoSaf);
						}
					}
				}
			},
			failure: function (result,request) 
			{ 
				Ext.MessageBox.alert('Error', 'Error de comunicacion con el Servidor'); 
			}
		});
	}

}

function irCancelar()
{
	limpiarFormulario(fromRevContabilizaSafdep);
	gridSolicitud.store.removeAll();
}


function irProcesar()
{
	valido=true;
	grid = gridSolicitud.getSelectionModel().getSelections();
	cadenajson = "{'operacion':'rev_contabilizar_safdep','codsis':'"+sistema+"','nomven':'"+vista+"','arrDetalle':[";
	total = grid.length;
	if (total>0)
	{			
		for (i=0; i<total; i++)
		{
			if (i==0) 
			{
				cadenajson += "{'comprobante':'"+grid[i].get('comprobante')+"','descripcion':'"+grid[i].get('descripcion')+"'," +
				"'anio':'"+grid[i].get('anio')+"','monto':'"+grid[i].get('monto')+"','mes':'"+grid[i].get('mes')+"'}";                
			}
			else {
				cadenajson += ",{'comprobante':'"+grid[i].get('comprobante')+"','descripcion':'"+grid[i].get('descripcion')+"'," +
				"'anio':'"+grid[i].get('anio')+"','monto':'"+grid[i].get('monto')+"','mes':'"+grid[i].get('mes')+"'}";               
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsaf.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request) {
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado de Reverso Contabilizacion de Depreciaci&#243;n de Activos',
					anchoLabel: 200,
					labelTotal:'Total depreciaciones procesadas',
					valorTotal: arrResultado[0],
					labelProcesada:'Total depreciaciones reversadas',
					valorProcesada:arrResultado[1],
					labelError:'Total depreciaciones con error',
					valorError:arrResultado[2],
					tituloGrid:'Detalle de Resultados',
					dataDetalle:arrResultado[3]
				});
				//fin creando componente detalle comprobante
				
				comResultado.mostrarVentana();
				
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
irCancelar();
}


