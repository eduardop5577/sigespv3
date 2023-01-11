/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta = true;
formato ='';
formatoficha ='';
Ext.onReady(function()
{
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	

	
	var botbusquedaDesde = new Ext.Button({
		id: 'botbusquedaDesde',
		iconCls: 'menubuscar',
		style:'position:absolute;left:185px;top:20px',
		listeners:{
            'click' : function(boton)
            {
				//componente catalogo de proveedores
				var reCatBeneficiario = Ext.data.Record.create([
					{name: 'ced_bene'}, //campo obligatorio                             
					{name: 'nombene'},  //campo obligatorio
					{name: 'apebene'},  //campo obligatorio
					{name: 'dirbene'}   //campo obligatorio
				]);
				
				var comcampocatbeneficario = new com.sigesp.vista.comCatalogoBeneficiario({
					reCatalogo: reCatBeneficiario,
					rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
					parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'ced_benedesde',valor:'ced_bene'}],
					numFiltroNoVacio: 1,
					idComponente:'Desde'
				});
				//fin componente catalogo de proveedores
            	comcampocatbeneficario.mostrarVentana();
            }
        }
	});
	var botbusquedaHasta = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:425px;top:20px',
		listeners:{
            'click' : function(boton)
            {
				//componente catalogo de proveedores
				var reCatBeneficiario = Ext.data.Record.create([
					{name: 'ced_bene'}, //campo obligatorio                             
					{name: 'nombene'},  //campo obligatorio
					{name: 'apebene'},  //campo obligatorio
					{name: 'dirbene'}   //campo obligatorio
				]);
				
				var comcampocatbeneficario = new com.sigesp.vista.comCatalogoBeneficiario({
					reCatalogo: reCatBeneficiario,
					rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
					parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'ced_benehasta',valor:'ced_bene'}],
					numFiltroNoVacio: 1,
					idComponente:'Hasta'
				});
				//fin componente catalogo de proveedores
            	comcampocatbeneficario.mostrarVentana();
            }
        }
	});

	var reSolicitud = Ext.data.Record.create([
    	    {name: 'cedper'}, 
    	    {name: 'nomper'}
            	]);
            	
	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
    	});
    						
	var cmSolicitud = new Ext.grid.ColumnModel([
		new Ext.grid.CheckboxSelectionModel(),
        {header: "Cedula/Codigo", width: 40, sortable: true, dataIndex: 'cedper'},
        {header: "Nombre del Personal", width: 50, sortable: true, dataIndex: 'nomper'}          
	]);
    	
    	//creando datastore y columnmodel para la grid de reporte beneficiario
	var gridSolicitud = new Ext.grid.EditorGridPanel({
	 		width:650,
	 		height:100,
			frame:true,
			title:'',
			style: 'position:absolute;left:0px;top:300px',
			autoScroll:true,
     		border:true,
     		ds: dsSolicitud,
       		cm: cmSolicitud,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
       		stripeRows: true,
      		viewConfig: {forceFit:true}
	});
	//--------------------------------------------------------------------------------------------
	
	var	fromBeneficiarios = new Ext.form.FieldSet({
			title:'Intervalo de Beneficiarios',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 465,
			cls :'fondo',
			height: 80,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Desde',
									id: 'ced_benedesde',
									width: 100,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]
						}]
					},botbusquedaDesde,
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:265px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Hasta',
									name: 'proveedesde',
									id: 'ced_benehasta',
									width: 100,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]
							}]
					},botbusquedaHasta]
  	})
	
	var	fromTipoReporte= new Ext.form.FieldSet({
			title:'Ordenado por',
			style: 'position:absolute;left:10px;top:100px',
			border:true,
			width: 465,
			cls :'fondo',
			height: 120,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:60px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items:[{		    				    	
									xtype: "radiogroup",
									labelSeparator:'',
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',	
									columns: [200,200,200],
									id:'orden',
									items: [
									        {boxLabel: 'C&#233;dula', name: 'ordenbene',inputValue: 'ced_bene',checked:true},
									        {boxLabel: 'Nombre', name: 'ordenbene', inputValue: 'nombene'},
									        {boxLabel: 'Apellido', name: 'ordenbene', inputValue: 'apebene'}
									        ]
								}]
						}]
				}]  	
  	})
	
//Creacion del formulario
	var Xpos = ((screen.width/2)-(250));
	plReporteBeneficiario = new Ext.FormPanel({
		applyTo: 'formulario',
		width:500,
		height: 300,
		title: "<H1 align='center'>Listado de Beneficiarios</H1>",
		frame:true,
		autoScroll:false,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;',
		items: [fromBeneficiarios,
		        fromTipoReporte,
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:10px;top:230px',
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:80,
						items: [cmbtiporeporte]
						}]
				},
		        {
				xtype: 'hidden',
				id: 'codsis',
				binding:true,
				defaultvalue:'RPC'
		        },
		        {
				xtype: 'hidden',
				id: 'nomven',
				binding:true,
				defaultvalue:'sigesp_vis_rpc_reporteBeneficiarios.html'
		        }]
	});	
	fromBeneficiarios.doLayout();
});
irBuscarFormato();

function irBuscarFormato()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'RPC',
		'seccion'     : 'REPORTE',
		'variable'    : 'LISTADO_BENEFICIARIOS',
		'valor'		  : 'sigesp_rpc_rpp_beneficiario.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: '../../controlador/rpc/sigesp_ctr_rpc_reportes.php',
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formato = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})

	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'RPC',
		'seccion'     : 'REPORTE',
		'variable'    : 'FICHA_BENEFICIARIO',
		'valor'		  : 'sigesp_rpc_rpp_beneficiario.php',
		'tipo'		  : 'C'
	};	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request(
	{
		url: '../../controlador/rpc/sigesp_ctr_rpc_reportes.php',
		params: parametros,
		method: 'POST',
		success: function (result, request)
		{ 
			formatoficha = result.responseText;			
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})

}

function irCancelar()
{
	limpiarFormulario(plReporteBeneficiario);
}

function irImprimir()
{
	ced_benedesde = Ext.getCmp('ced_benedesde').getValue();
	ced_benehasta = Ext.getCmp('ced_benehasta').getValue();
	
	if(Ext.getCmp('orden').items.items[0].checked)
	{
		orden = Ext.getCmp('orden').items.items[0].inputValue;
	}
	else if (Ext.getCmp('orden').items.items[1].checked)
  	{
  		orden = Ext.getCmp('orden').items.items[1].inputValue;
  	}
 	else if(Ext.getCmp('orden').items.items[2].checked)	
    {
   		orden = Ext.getCmp('orden').items.items[2].inputValue;
    }
	if (Ext.getCmp('tipoRep').getValue()=='L')
	{
		pagina="reportes/"+formato+"?hidorden="+orden+"&hidcedula1="+ced_benedesde+"&hidcedula2="+ced_benehasta;
		window.open(pagina,"menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
	}
	else
	{
		pagina="reportes/"+formatoficha+"?hidorden="+orden+"&hidcodproben1="+ced_benedesde+"&hidcodproben2="+ced_benehasta;
		window.open(pagina,"menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
	}
	limpiarFormulario(plReporteBeneficiario);
}


	var opcRep = [ 
				 [ 'LISTADO', 'L' ], 
	             [ 'FICHA', 'F' ] 
				 ];
	
	var stOpcRep = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcRep
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtiporeporte = new Ext.form.ComboBox({
		store : stOpcRep,
		fieldLabel : 'Tipo Reporte',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipoRep',
		width : 130,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbtiporeporte.setValue('L');