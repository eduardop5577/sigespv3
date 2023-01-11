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
		style:'position:absolute;left:180px;top:15px',
		listeners:{
            'click' : function(boton)
            {
 				//componente catalogo de proveedores
				var reCatProveedor = Ext.data.Record.create([
					{name: 'cod_pro'}, //campo obligatorio                             
					{name: 'nompro'},  //campo obligatorio
					{name: 'dirpro'},  //campo obligatorio
					{name: 'rifpro'}   //campo obligatorio
				]);
				
				var comcampocatproveedor = new com.sigesp.vista.comCatalogoProveedor({
					reCatalogo: reCatProveedor,
					rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
					parametros: "ObjSon={'operacion': 'buscarProveedores'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'cod_prodesde',valor:'cod_pro'}],
					numFiltroNoVacio: 1,
					idComponente:'Desde'
				});
				//fin componente catalogo de proveedores
            	comcampocatproveedor.mostrarVentana();
            }
        }
	});
	var botbusquedaHasta = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:420px;top:15px',
		listeners:{
            'click' : function(boton)
            {
 				//componente catalogo de proveedores
				var reCatProveedor = Ext.data.Record.create([
					{name: 'cod_pro'}, //campo obligatorio                             
					{name: 'nompro'},  //campo obligatorio
					{name: 'dirpro'},  //campo obligatorio
					{name: 'rifpro'}   //campo obligatorio
				]);
				
				var comcampocatproveedor = new com.sigesp.vista.comCatalogoProveedor({
					reCatalogo: reCatProveedor,
					rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
					parametros: "ObjSon={'operacion': 'buscarProveedores'",
					soloCatalogo: true,
					arrSetCampo:[{campo:'cod_prohasta',valor:'cod_pro'}],
					numFiltroNoVacio: 1,
					idComponente:'Hasta'
				});
				//fin componente catalogo de proveedores
            	comcampocatproveedor.mostrarVentana();
            }
        }
	});
	//creando datastore y columnmodel para especialidad
	var reEspecialidad = Ext.data.Record.create([
						{name: 'codesp'},
						{name: 'denesp'}
	]);
	
	var dsEspecialidad =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reEspecialidad)
	});
	
	function buscarEspecialidades()
	{
		var myJSONObject ={
			"operacion":"buscarEspecialidad" 
		};
			
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request(
		{
			url: '../../controlador/rpc/sigesp_ctr_rpc_especialidad.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request )
			{ 
		    	var datosEspecialidad = eval('(' + result.responseText + ')');
		        dsEspecialidad.loadData(datosEspecialidad);
			},
			failure: function ( result, request){ 
					Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
			}
		});		        
	}	
	buscarEspecialidades();

	var	fromReporteProv1= new Ext.form.FieldSet({
			title:'Categoria',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 465,
			cls :'fondo',
			height: 80,
			items:[{
		    		layout: "column",
		    		defaults: {border: false},
		    		style: 'position:absolute;left:35px;top:10px',
		    		items: [{
			    			layout: "form",
			    			border: false,
			    			labelWidth: 100,
			    			items:[{		    				    	
		    						xtype: "radiogroup",
		    						labelSeparator:'',
		    						binding:true,
		    						hiddenvalue:'',
		    						defaultvalue:'',	
		    						columns: [200,200],
		    						id:'tipo',
		    						items: [
	    								{boxLabel: 'Proveedor', name: 'categoria',inputValue: 'P',checked:true},
	    								{boxLabel: 'Contratista', name: 'categoria', inputValue: 'C'}
	    								]
		        				  	}]
		    				}]
		    		}]  	
  	});  
	
	var	fromproveedores = new Ext.form.FieldSet({
			title:'Rango de Códigos',
			style: 'position:absolute;left:10px;top:100px',
			border:true,
			width: 465,
			cls :'fondo',
			height: 95,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:20px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Desde',
									name: 'proveedesde',
									id: 'cod_prodesde',
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
					style: 'position:absolute;left:260px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Hasta',
									name: 'proveedesde',
									id: 'cod_prohasta',
									width: 100,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
								    autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"}
									}]
							}]
	        		},botbusquedaHasta]  
  	});
	
	var	fromReporteProv2= new Ext.form.FieldSet({
			title:'Ordenado por',
			style: 'position:absolute;left:10px;top:210px',
			border:true,
			width: 465,
			cls :'fondo',
			height: 80,
			items:[{
		    		layout: "column",
		    		defaults: {border: false},
		    		style: 'position:absolute;left:15px;top:10px',
		    		items: [{
			    			layout: "form",
			    			border: false,
			    			labelWidth: 100,
			    			items:[{		    				    	
		    						xtype: "radiogroup",
		    						labelSeparator:'',
		    						binding:true,
		    						hiddenvalue:'',
		    						defaultvalue:'',	
		    						columns: [200,200],
		    						id:'orden',
		    						items: [
		    								{boxLabel: 'C&#243;digo', name: 'ordenado',inputValue: '0',checked:true},
		    								{boxLabel: 'Nombre', name: 'ordenado', inputValue: '1'}
		    								]
	        				  		}]
	    					}]
		    	}]		
  	});
//Creacion del formulario
	
var Xpos = ((screen.width/2)-(250));
plReporteProveedor = new Ext.FormPanel({
	applyTo: 'formulario',
	width:500,
	height: 380,
	title: "<H1 align='center'>Listado de Proveedores</H1>",
	frame:true,
	autoScroll:false,
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;',
	items: [
	        fromReporteProv1,
	        fromproveedores, 
	        {
			layout: "column",
			border: false,
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:160px',
			items: [{
					layout: "form",
					border: false,
					labelWidth: 80,			
					items: [{
							xtype:"combo",
							store: dsEspecialidad,
							displayField:'denesp',
							valueField:'codesp',
							labelSeparator:":",
							id:"codesp",
							typeAhead: true,
							mode: 'local',
							triggerAction: 'all',
							fieldLabel:'Especialidad',
							listWidth:250,
							editable:false,
							width:200,
							binding:true,
							hiddenvalue:'',
							defaultvalue:'---'	
						}]
				}]
	        },
	        fromReporteProv2,
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:10px;top:300px',
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:80,
					items: [cmbtiporeporte]
					}]
			},
			{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:250px;top:300px',
			border:false,
			items: [{
					layout:"form",
					border:false,
					labelWidth:100,
					items: [cmbtipoimpresion]
					}]
			}]
});	
     fromReporteProv1.doLayout();
});

irBuscarFormato();

function irBuscarFormato()
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'RPC',
		'seccion'     : 'REPORTE',
		'variable'    : 'LISTADO_PROVEEDORES',
		'valor'		  : 'sigesp_rpc_rpp_proveedor.php',
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
		'variable'    : 'FICHA_PROVEEDOR',
		'valor'		  : 'sigesp_rpc_rpp_proveedor.php',
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
	})}
	

function irCancelar(){
	limpiarFormulario(plReporteProveedor);
}

function irImprimir()
{
	var cod_prodesde2 = Ext.getCmp('cod_prodesde').getValue();
	var cod_prohasta2 = Ext.getCmp('cod_prohasta').getValue();
	var codesp2 = Ext.getCmp('codesp').getValue(); 
	
	if(Ext.getCmp('orden').items.items[0].checked){
		orden = Ext.getCmp('orden').items.items[0].inputValue;
	}
	else if (Ext.getCmp('orden').items.items[1].checked){
		orden = Ext.getCmp('orden').items.items[1].inputValue;
	}
   
	if(Ext.getCmp('tipo').items.items[0].checked){
		tipo = Ext.getCmp('tipo').items.items[0].inputValue;
	}
	else if (Ext.getCmp('tipo').items.items[1].checked){
		tipo = Ext.getCmp('tipo').items.items[1].inputValue;
	}
	if (Ext.getCmp('tipoRep').getValue()=='L')
	{
		if (Ext.getCmp('tipoimp').getValue()=='P')
		{
			pagina="reportes/"+formato+"?hidorden="+orden+"&hidtipo="+tipo+"&hidcodprov1="+cod_prodesde2+"&hidcodprov2="+cod_prohasta2+"&hidcodesp="+codesp2;
			window.open(pagina,"menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
		else
		{
			pagina="reportes/sigesp_rpc_rpp_proveedor_excel.php?hidorden="+orden+"&hidtipo="+tipo+"&hidcodprov1="+cod_prodesde2+"&hidcodprov2="+cod_prohasta2+"&hidcodesp="+codesp2;
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");	
		}
	}
	else
	{
		pagina="reportes/"+formatoficha+"?tiporeporte=0&hidorden="+orden+"&hidtipo="+tipo+"&hidcodproben1="+cod_prodesde2+"&hidcodproben2="+cod_prohasta2;
		window.open(pagina,"menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
	}
	limpiarFormulario(plReporteProveedor);
}

	var opcimp = [ 
				 [ 'PDF', 'P' ], 
	             [ 'EXCEL', 'E' ] 
				 ];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	}); //Fin de store para el tipo de impresion
	
	//creando objeto combo filtrar
	var cmbtipoimpresion = new Ext.form.ComboBox({
		store : stOpcimp,
		fieldLabel : 'Tipo Impresión',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'tipoimp',
		width : 100,
		typeAhead : true,
		triggerAction : 'all',
		forceselection : true,
		binding : true,
		mode : 'local'
	});
	
	cmbtipoimpresion.setValue('P');

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