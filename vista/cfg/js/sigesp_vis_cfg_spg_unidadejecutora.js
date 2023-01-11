/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* a la definicion de unidad ejecutora 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true;
var cantnivel           = parseInt(empresa['numniv']);  //cantidad de niveles del presupuesto
var formunidadejecutora = ''; //instacia del formulario principal
var comliscatestructura = ''; //instacia del componente lista catalogo
var comcampocatalogo    = ''; //instacia del componente campo catalogo
var selmodestructura = new Ext.grid.CheckboxSelectionModel({});
var selmodestructuracat = new Ext.grid.CheckboxSelectionModel({});
var dataestructuraeliminada='';
var Actualizar=false;

var Campos =new Array(	['coduniadm','novacio|'],
						['denuniadm','novacio|'],
						['estemireq','novacio|'],
						['coduniadmsig','novacio|'],
						['denuac','novacio|'],
						['resuniadm','novacio|']);

function mostrarEstatus(est)
{
	if (est=='P')
	{
		return 'Proyecto';
	}
	else if (est=='A')
	{
		return 'Acci&#243;n Centralizada';	
	}
	else if (est=='-')
	{
		return 'POR DEFECTO';	
	}
}

function mostrarIvaCent(iva)
{
	if (iva=='1')
	{
		return 'Si';
	}
	else
	{
		return 'No';	
	}
}

//combo iva centralizado
var estatusceniva = [['1', 'Si'], ['0', 'No']]
var stEstCenIva = new Ext.data.SimpleStore({
    fields: ['estatus', 'labelEstatus'],
    data: estatusceniva // from states.js
});

var cmbEstCenIva = new Ext.form.ComboBox({
    store: stEstCenIva,
    editable: false,
    forceSelection: true,
    displayField: 'labelEstatus',
    valueField: 'estatus',
    id: 'estceniva',
    typeAhead: true,
    triggerAction: 'all',
    mode: 'local',
    listeners:{
    	'change':function(cmb){
    		var estatus = cmb.getValue();
    		if(estatus== '1'){
    			var encontro = false;
    			var duplicado = false;
    			var centraItera  = '' 
    			comliscatestructura.dataGridEditable.store.each(function (registroGrid){
    				centraItera = registroGrid.get('central');
    				if(centraItera == '1' && !encontro)
					{
    					encontro = true;
    				}
    				
    				if(centraItera == '1' && encontro)
					{
    					duplicado = true;
    				}
    				
    			});
    			
    			if(duplicado){
    				Ext.MessageBox.show({
		    			title:'Advertencia',
						msg: 'Ya existe una estructura configurada para el iva centralizado',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.WARNING
		    		});
    				cmb.setValue('0'); 
    			}
    		}
    	}
    }
})
//fin combo iva centralizado

Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	
		
	//creando datastore de la grid de datos
	switch(cantnivel) {
		case 1:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},    
							{name: 'denestpro1'},
							{name: 'estcla'},
							{name: 'central'},
							{registrocat:''}
						]);
	
			objetoestnivelN={"raiz":[{"codestpro1":'',"denestpro1":'',"estcla":'',"registrocat":''}]};
			break;
		case 2:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},    
							{name: 'denestpro2'},
							{name: 'estcla'},
							{name: 'central'},
							{registrocat:''}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"denestpro2":'',"registrocat":''}]};
			break;
		case 3:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},
							{name: 'denestpro2'},
							{name: 'codestpro3'},    
							{name: 'denestpro3'},
							{name: 'estcla'},
							{name: 'central'},
							{registrocat:''}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"denestpro3":'',"registrocat":''}]};
			break;
		case 4:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},
							{name: 'denestpro2'},
							{name: 'codestpro3'},
							{name: 'denestpro3'},
							{name: 'codestpro4'},    
							{name: 'denestpro4'},
							{name: 'estcla'},
							{name: 'central'},
							{registrocat:''}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"denestpro4":'',"registrocat":''}]};
			break;
		case 5:
	    	registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},
							{name: 'denestpro1'},
							{name: 'codestpro2'},
							{name: 'denestpro2'},
							{name: 'codestpro3'},
							{name: 'denestpro3'},
							{name: 'codestpro4'},
							{name: 'denestpro4'},
							{name: 'codestpro5'},    
							{name: 'denestpro5'},
							{name: 'estcla'},
							{name: 'central'},
							{registrocat:''}
						]);
	
			objetoestnivelN = {"raiz":[{"codestpro1":'',"codestpro2":'',"codestpro3":'',"codestpro4":'',"codestpro5":'',"denestpro5":'',"registrocat":''}]};
			break;
	}
	
	var dataestrucutura =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivelN),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivelN)
							})
							
	dataestructuraeliminada =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivelN),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivelN)
							})
	//creando el column y sel model de la grid de datos
	var modelogridN="[selmodestructura,";
	for(var x=1;x<=cantnivel;x++)
	{
		if(x==cantnivel)
		{
			modelogridN = modelogridN + "{header: '"+empresa['nomestpro'+x]+"', width: 50, sortable: true,   dataIndex: 'codestpro"+x+"'},"+
										"{header: 'Denominaci&#243;n', width: 45, sortable: true,   dataIndex: 'denestpro"+x+"'},"+
										"{header: 'Tipo', width: 25, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatus}";
		}
		else
		{
			modelogridN = modelogridN + "{header: '"+empresa['nomestpro'+x]+"', width: 50, sortable: true,   dataIndex: 'codestpro"+x+"'},";
		}	
	}
	
	if(empresa['estceniva']=='1')
	{
		modelogridN = modelogridN + ",{header: 'Iva', " +
									"  width: 25, sortable: true, " +
									"  dataIndex: 'central'," +
									"  editor:cmbEstCenIva," +
									"  renderer:mostrarIvaCent}]";
	}
	else
	{
		modelogridN = modelogridN + "]";
	}
	var objetomodelo = Ext.util.JSON.decode(modelogridN);
	var colmodestructura = new Ext.grid.ColumnModel(objetomodelo);
	
	//creando datasotre para el catalogo
	dataestrucuturacat =  new Ext.data.Store({
								proxy: new Ext.data.MemoryProxy(objetoestnivelN),
								reader: new Ext.data.JsonReader({
												root: 'raiz',             
												id: "id"   
											},registroestnivelN),
								data: objetoestnivelN
	  						})
	
	//creando arreglo de validacion para la grid del catalogo
	var modelovalgrid="[";
			for(var x=1;x<=cantnivel;x++)
			{
				if(x==cantnivel)
				{
					modelovalgrid = modelovalgrid + "'codestpro"+x+"','estcla'";
				}
				else
				{
					modelovalgrid = modelovalgrid + "'codestpro"+x+"',";
				}	
			}
	modelovalgrid = modelovalgrid + "]";
	var arrcampoval = Ext.util.JSON.decode(modelovalgrid);
	
	//creando el column y sel model del catalogo
	var modelocatN="[selmodestructuracat,";
			for(var x=1;x<=cantnivel;x++)
			{
				if(x==cantnivel)
				{
					modelocatN = modelocatN + "{header: '"+empresa['nomestpro'+x]+"', width: 50, sortable: true,   dataIndex: 'codestpro"+x+"'},"+
												"{header: 'Denominaci&#243;n', width: 45, sortable: true,   dataIndex: 'denestpro"+x+"'},"+
												"{header: 'Tipo', width: 25, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatus}";
				}
				else
				{
					modelocatN = modelocatN + "{header: '"+empresa['nomestpro'+x]+"', width: 50, sortable: true,   dataIndex: 'codestpro"+x+"'},";
				}	
			}
	modelocatN = modelocatN + "]";
	var objetomodelocat = Ext.util.JSON.decode(modelocatN);
	var colmodestructuracat = new Ext.grid.ColumnModel(objetomodelocat);
	
	comliscatestructura = new com.sigesp.vista.comListaEditableCatalogo({
		titvencat: 'Catalogo de Estructuras Presupuestarias',
		idgrid: 'gridestpre',
		anchoformbus: 450,
		altoformbus:100,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 500,
		ancho: 850,
		alto: 250,
		datosgridcat: dataestrucuturacat,
		colmodelocat: colmodestructuracat,
		selmodelocat: selmodestructuracat,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_catestpresupuestaria.php',
		parametros: "ObjSon={'operacion':'nivelN','cantnivel':'" + cantnivel + "'}",
		tipbus:'L',
		arrfiltro:[{etiqueta:'Codigo',id:'codigo',valor:'codestpro'+cantnivel,ancho:150,longitud:25},
				   {etiqueta:'Descripcion',id:'descripcion',valor:'denestpro'+cantnivel,ancho:300,longitud:254}],
		posicion: 'position:absolute;left:5px;top:150px',
		titgrid: 'Estructura Presupuestaria',
		datosgrid: dataestrucutura,
		colmodelo: colmodestructura,
		selmodelo: selmodestructura,
		arrcampovalidaori:arrcampoval,
		arrcampovalidades:arrcampoval,
		guardarEliminados: true,
		rgeliminar: registroestnivelN
	});
	
	// creando datastore del campo catalogo unidad administradora
	var registro_unidadadministradora = Ext.data.Record.create([
		{name: 'coduac'},    
		{name: 'denuac'},
		{name: 'resuac'},
		{name: 'tipuac'}
	]);
	
	var objeto_unidadadministradora={"raiz":[{"coduac":'',"denuac":'',"resuac":'',"tipuac":''}]};
		
	var dsunidadadministradora =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto_unidadadministradora),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
			registro_unidadadministradora
			),
			data: objeto_unidadadministradora
	  	})					
						
	comcampocatalogo = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Catalogo de Unidades Administradoras',
							anchoformbus: 450,
							altoformbus:100,
							anchogrid: 450,
							altogrid: 400,
							anchoven: 500,
							altoven: 400,
							anchofieldset:650,
							datosgridcat: dsunidadadministradora,
							colmodelocat: new Ext.grid.ColumnModel([
          									{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'coduac'},
          									{header: "Descripcion", width: 40, sortable: true, dataIndex: 'denuac'}
        					]),
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_spg_unidadadministradora.php',
							parametros: "ObjSon={'oper': 'catalogo'}",
							arrfiltro:[{etiqueta:'Codigo',id:'codigouac',valor:'coduac'},
									   {etiqueta:'Descripcion',id:'descripcionuac',valor:'denuac'}],
							posicion:'position:absolute;left:5px;top:110px',
							tittxt:'Unidad Administradora',
							idtxt:'coduniadmsig',
							campovalue:'coduac',
							anchoetiquetatext:130,
							anchotext:50,
							anchocoltext:0.30,
							idlabel:'denuac',
							labelvalue:'denuac',
							anchocoletiqueta:0.63,
							anchoetiqueta:150,
							tipbus:'L'
						});
	
	
	
	
	function getFormUnidad()
	{
		Ext.QuickTips.init();
		Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';	
		var Xpos = ((screen.width/2)-(450));
		var Ypos = 65;
		formunidadejecutora = new Ext.FormPanel({
						width: 900,
						height: 450,
						applyTo: 'formulario_unidadejecutora',
						title: 'Definici&#243;n de unidades ejecutoras',
						frame:true,
						labelWidth:200,
						style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
						items:[{
								layout : "column",
					        	defaults : {border : false},
								style:'position:absolute;left:17px;top:5px',
								items : [{
												layout : "form",
					        					border : false,
												labelWidth: 130,
					        					columnWidth : 1,
					        					items : [{
														  xtype: 'textfield',
														  fieldLabel: 'C&#243;digo',
														  labelSeparator:'',
														  id: 'coduniadm',
														  autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10'},
                                                          width: 80,
														  listeners: {
																		'blur': function()
																		{
																					var valorcampo = this.getValue();
																					valorcampo = ue_rellenarcampo(valorcampo, 10);
																					this.setValue(valorcampo);
																		}
																	 }
														}]
						   				}]
								},{
								layout : "column",
					        	defaults : {border : false},
								style:'position:absolute;left:17px;top:35px',
								items : [{
												layout : "form",
					        					border : false,
												labelWidth: 130,
					        					columnWidth : 1,
					        					items : [{
														  fieldLabel: 'Denominaci&#243;n',
														  xtype: 'textfield',
														  labelSeparator:'',
														  id: 'denuniadm',
														  autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '255', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"},
														  width: 370	
														}]
						   				}]
								},{
								layout : "column",
					        	defaults : {border : false},
								style:'position:absolute;left:17px;top:65px',
								items : [{
												layout : "form",
					        					border : false,
												labelWidth: 130,
					        					columnWidth : 1,
					        					items : [{
														  fieldLabel: 'Responsable ',
														  xtype: 'textfield',
														  labelSeparator:'',
														  id: 'resuniadm',
														  autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '255', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=¡;:[]{}áéíóúÁÉÍÓÚ ');"},
														  width: 370	
														}]
						   				}]
								},{
								layout : "column",
					        	defaults : {border : false},
								style:'position:absolute;left:17px;top:90px',
								items : [{
												layout : "form",
					        					border : false,
												labelWidth: 130,
					        					columnWidth : 1,
					        					items : [{
            												xtype: "checkbox",
            												fieldLabel: "Emite requisici&#243;n",
            												labelSeparator:'',
															inputValue:1,
            												id: 'estemireq'
        												}]
						   				}]
								},
								comcampocatalogo.fieldsetCatalogo,
								comliscatestructura.dataGridEditable]
						
		});
			
	}
	getFormUnidad();
	
});

function validarDatosGrabar()
{
	var valido = true;
	var codigo       = Ext.getCmp('coduniadm').getValue();
	var denominacion = Ext.getCmp('denuniadm').getValue();
	var coduac       = comcampocatalogo.campo.getValue();
	var detalles = comliscatestructura.dataGridEditable.getStore();
	var responsable = Ext.getCmp('resuniadm').getValue();
	
	if(codigo=='')
	{
		Ext.MessageBox.show({
	    	title:'Advertencia',
			msg: 'Debe indicar el c&#243;digo de la unidad ejecutora',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
	    });	
	    valido = false
	}
	else if(denominacion=='')
	{
		Ext.MessageBox.show({
	    	title:'Advertencia',
			msg: 'Debe indicar la Denominaci&#243;n de la unidad ejecutora',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
	    });	
	    valido = false
	}
	else if(responsable==''){
		Ext.MessageBox.show({
	    	title:'Advertencia',
			msg: 'Debe indicar el responsable de la unidad ejecutora',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
	    });	
	    valido = false
	}
	else if(coduac==''){
		Ext.MessageBox.show({
	    	title:'Advertencia',
			msg: 'Debe indicar la Unidad Administradora asociada la unidad ejecutora',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
	    });	
	    valido = false
	}
	else if(detalles.getCount() == 0){
    	Ext.MessageBox.show({
	    	title:'Advertencia',
			msg: 'Debe agregar al menos una Estructura Presupuestaria',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
	    });	
	    valido = false
    }
    
    return valido;
}



function irGuardar()
{
	if(validarDatosGrabar())
	{
		var estemireq = 0;
		if(Ext.getCmp('estemireq').checked){
			estemireq = 1;
		}
		var cadenajson = "{'operacion':'incluir',"+
						 "'datoscabecera':[{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
						 					"'coduac':'"+comcampocatalogo.campo.getValue()+"',"+
											"'denuniadm':'"+Ext.getCmp('denuniadm').getValue()+"',"+
											"'coduniadmsig':'"+comcampocatalogo.campo.getValue()+"',"+
											"'codcencos':'---',"+
											"'resuniadm':'"+Ext.getCmp('resuniadm').getValue()+"',"+
											"'estemireq':"+estemireq+"}],";
						 
		var detalles = comliscatestructura.dataGridEditable.getStore();
		var dataestructuraeliminada =comliscatestructura.dataStoreEliminados;
		cadenajson = cadenajson +"'imo_spg_dt_unidadadministrativa':[";
		for (var i = 0; i <= detalles.getCount() - 1; i++) {
			if (i == 0) {
				switch(cantnivel) {
					case 1:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'0000000000000000000000000',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro5'),25)+"',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
				}
			}
			else{
				switch(cantnivel) {
					case 1:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'0000000000000000000000000',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro5'),25)+"',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'," +
											"'central':'"+detalles.getAt(i).get('central')+"'}";
						break;
				}
			}
		}
		cadenajson = cadenajson + "],";
		
		cadenajson = cadenajson +"'pel_spg_dt_unidadadministrativa':[";
		for (var i = 0; i <= dataestructuraeliminada.getCount() - 1; i++) {
			if (i == 0) {
				switch(cantnivel) {
					case 1:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'0000000000000000000000000',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro5'),25)+"',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
				}
			}
			else{
				switch(cantnivel) {
					case 1:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'0000000000000000000000000',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'"+ue_rellenarcampo(dataestructuraeliminada.getAt(i).get('codestpro5'),25)+"',"+
											"'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
				}
			}
		}
		cadenajson = cadenajson + "]}";
		
		
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_spg_unidadejecutora.php',
			params : parametros,
			method: 'POST',
			success: function ( resultad, request ){ 
		        datos = resultad.responseText;
		       	resultado = datos.split("|");
				if(resultado[2]=="1"){
					switch(resultado[1]) {
						case "0":
		    				Ext.MessageBox.show({
		    					title:'Error',
								msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.ERROR
		    				});
							break;
						case "1":
		    				Ext.MessageBox.show({
		    					title:'Mensaje',
								msg: 'El registro fue actualizado',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
		    				});
							break;
						case "2":
		    				Ext.MessageBox.show({
		    					title:'Mensaje',
								msg: 'El registro fue incluido',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
		    				});
							break;
					}
				}
				else{
					Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
				}
				limpiarCampos();
				comliscatestructura.dataGridEditable.store.removeAll();
				dataestructuraeliminada.removeAll();
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	}
}

function irEliminar()
{
	var estemireq = 0;
	if(Ext.getCmp('estemireq').checked)
	{
		estemireq = 1;
	}
	var Result;
	Ext.MessageBox.confirm('Confirmar', '¿Desea eliminar este registro?', Result);
    function Result(btn)
	{
		if (btn == 'yes')
		{
			if(validarDatosGrabar())
			{
				var cadenajson = "{'operacion':'eliminar'," +
								"'datoscabecera':[{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
						 							"'coduac':'"+comcampocatalogo.campo.getValue()+"',"+
													"'denuniadm':'"+Ext.getCmp('denuniadm').getValue()+"',"+
													"'coduniadmsig':'"+comcampocatalogo.campo.getValue()+"',"+
												    "'estemireq':"+estemireq+"}],";
				var detalles = comliscatestructura.dataGridEditable.getStore();				 
				cadenajson = cadenajson +"'pel_spg_dt_unidadadministrativa':[";
				for (var i = 0; i <= detalles.getCount() - 1; i++)
				{
					if (i == 0)
					{
						switch(cantnivel)
						{
							case 1:
		    					cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
															"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
															"'codestpro2':'0000000000000000000000000',"+
															"'codestpro3':'0000000000000000000000000',"+
															"'codestpro4':'0000000000000000000000000',"+
															"'codestpro5':'0000000000000000000000000',"+
															"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 2:
		    					cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
															"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
															"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
															"'codestpro3':'0000000000000000000000000',"+
															"'codestpro4':'0000000000000000000000000',"+
															"'codestpro5':'0000000000000000000000000',"+
															"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 3:
		    					cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
															"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
															"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
															"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
															"'codestpro4':'0000000000000000000000000',"+
															"'codestpro5':'0000000000000000000000000',"+
															"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 4:
		    					cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
															"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
															"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
															"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
															"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
															"'codestpro5':'0000000000000000000000000',"+
															"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 5:
		    					cadenajson = cadenajson + "{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
															"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
															"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
															"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
															"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
															"'codestpro5':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro5'),25)+"',"+
															"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
						}
					}
					else
					{
						switch(cantnivel)
						{
							case 1:
		    					cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'0000000000000000000000000',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 2:
		    					cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'0000000000000000000000000',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 3:
		    					cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'0000000000000000000000000',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 4:
		    					cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'0000000000000000000000000',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
							case 5:
		    					cadenajson = cadenajson + ",{'coduniadm':'"+Ext.getCmp('coduniadm').getValue()+"',"+
											"'codestpro1':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro1'),25)+"',"+
											"'codestpro2':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro2'),25)+"',"+
											"'codestpro3':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro3'),25)+"',"+
											"'codestpro4':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro4'),25)+"',"+
											"'codestpro5':'"+ue_rellenarcampo(detalles.getAt(i).get('codestpro5'),25)+"',"+
											"'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
								break;
						}
					}
				}
				cadenajson = cadenajson + "],'ins_spg_dt_unidadadministrativa':[]}";
				parametros = 'ObjSon=' + cadenajson;
				Ext.Ajax.request({
					url: '../../controlador/cfg/sigesp_ctr_cfg_spg_unidadejecutora.php',
					params: parametros,
					method: 'POST',
					success: function(resultad, request){
							datos = resultad.responseText;
							resultado = datos.split("|");
							switch(resultado[1]) {
								case "-1":
		    						Ext.MessageBox.alert('Mensaje', 'El registro no puede ser eliminado, ya que ha sido referenciado en otros procesos');
									break;
								case "1":
		    						Ext.MessageBox.alert('Mensaje', 'El registro fue eliminado');
									break;
								case "0":
		    						Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
									break;
								case "-8":
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
									break;
							}
							limpiarCampos();
							comliscatestructura.dataGridEditable.store.removeAll();
							dataestructuraeliminada.removeAll();
					},
					failure: function(result, request){
						Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
					}
				});
			}
		}
	}
}

function irBuscar()
{
	mostrarCatalogoUnidadEjecutora();
	Ext.getCmp('coduniadm').setDisabled(true);
}

function irCancelar()
{
	 irNuevo();
}

function irNuevo()
{
	var myJSONObject ={
		"operacion":"buscarcodigo" 
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/cfg/sigesp_ctr_cfg_spg_unidadejecutora.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request )
		{ 
            var datos = result.responseText;
			var	resultado = datos.split("|");
			var codigo = resultado[1];
			if (codigo != "")
			{
				limpiarCampos();
				comliscatestructura.dataGridEditable.store.removeAll();
				Ext.getCmp('coduniadm').setValue(codigo);
				Ext.getCmp('coduniadm').setDisabled(false);
			}
		},
		failure: function ( result, request)
		{ 
			Ext.MessageBox.alert('Error', 'Error de comunicacion'); 
		}
	});		       
}