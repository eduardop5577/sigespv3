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

	//creando datastore y columnmodel para la grid de solicitudes
	var reSolicitud = Ext.data.Record.create([
	    {name: 'numsol'}, 
	    {name: 'fecregsol'},
	    {name: 'consol'},
	    {name: 'fechaconta'},
		{name: 'fechanula'}
	]);
	
	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});
						
	var cmSolicitud = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "Nº Solicitud", width: 30, sortable: true, dataIndex: 'numsol'},
        {header: "Fecha", width: 30, sortable: true, dataIndex: 'fecregsol'},
        {header: "Concepto", width: 60, sortable: true, dataIndex: 'consol'}
	]);
	
	//creando datastore y columnmodel para la grid de solicitudes
	var gridSolicitud = new Ext.grid.GridPanel({
	 		width:900,
	 		height:250,
			frame:true,
			title:'',
			autoScroll:true,
     		border:true,
     		ds: dsSolicitud,
       		cm: cmSolicitud,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});
	//fin creando grid para las solicitudes
				
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
		fieldLabel : 'Destino: ',
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
var datosNuevo={'raiz':[{'cod_pro':'','nompro':''}]};	
	
	//creando funcion que construye formulario principal Contabilizar
	var	fromBusquedaRD = new Ext.FormPanel({
			width: 900,
			height: 200,
			title: '',
			frame: true,
			autoScroll:true,
			items: [{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'textfield',
									fieldLabel: 'Recepcion Documentos',
									labelSeparator :'',
									id: 'numrecdoc',
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
					style: 'position:absolute;left:15px;top:35px',
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
									width: 170,
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
					style: 'position:absolute;left:15px;top:60px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									 xtype: 'datefield',
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
					style: 'position:absolute;left:15px;top:85px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									 xtype: 'datefield',
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
					style: 'position:absolute;left:15px;top:110px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									 xtype: 'datefield',
									 fieldLabel:"Fecha de Contabilizacion",
									 name:"Fechaconta",
									 allowBlank:true,
									 width:130,
									 binding:true,
									 defaultvalue:'1900-01-01',
									 hiddenvalue:'',
									 id:"fechaconta",
									 autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}									
									}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:500px;top:160px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 0,
							items: [{
									xtype:'button',
									id:'botbuscarsep',
									text: 'Buscar',
									handler:function(){
										//buscar articulos
										var numrecdoc   = Ext.getCmp('numrecdoc').getValue();
										var destino     = Ext.getCmp('cmbdestino').getValue();
										var cod_pro     = Ext.getCmp('cod_pro').getValue();
										var nompro      = Ext.getCmp('nompro').getValue();
										var fecregdoc   = Ext.getCmp('fecregdoc').getValue();
										var fecaprdoc   = Ext.getCmp('fecaprdoc').getValue();
										var fechaconta  = Ext.getCmp('fechaconta').getValue();
										buscarDataRD(numrecdoc,destino,cod_pro,nompro,fecregdoc,fecaprdoc,fechaconta);
									}
								}]
						}]
					}]
			
	});
/***********************************************************************************
/***********************************************************************************
* @Función para buscar los Proveedores o Beneficiarios segun sea el caso
* @parametros: 
* @retorno:
* @fecha de creación: 04/07/2012.
* @autor: Ing. Luis Anibal Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
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
				formulario:fromBusquedaRD
			});

			
			comcatproveedor.mostrarVentana();
		}
		else
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
				titvencat: 'Catalogo de Beneficiario',
				anchoformbus: 450,
				altoformbus:130,
				anchogrid: 450,
				altogrid: 400,
				anchoven: 500,
				altoven: 400,
				datosgridcat: dsparametro,
				colmodelocat: colmodelcatparametro,
				arrfiltro:[{etiqueta:'Codigo',id:'copro',valor:'cod_pro'},
						   {etiqueta:'Nombre',id:'conom',valor:'nompro'}],
				rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
				parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscarBeneficiarios'}),
				tipbus:'L',
				setdatastyle:'F',
				formulario:fromBusquedaRD
			});

			
			comcatproveedor.mostrarVentana();
		}
	}
	
