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

barraherramienta    = true;
var cantnivel           = parseInt(empresa['numniv']);  //cantidad de niveles del presupuesto
var comliscatestructura = ''; //instacia del componente lista catalogo
var selmodestructura = new Ext.grid.CheckboxSelectionModel({});
var selmodestructuracat = new Ext.grid.CheckboxSelectionModel({});
var dataestructuraeliminada='';


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

Ext.onReady(function(){
	
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
		
	//creando datastore de la grid de datos
	switch(cantnivel)
	{
		case 1:
			registroestnivelN = Ext.data.Record.create([
							{name: 'codestpro1'},    
							{name: 'denestpro1'},
							{name: 'estcla'},
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
	modelogridN = modelogridN + "]";
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
	
	comliscatestructura = new com.sigesp.vista.comListaCatalogo({
							titvencat: 'Catalogo de Estructuras Presupuestarias',
							idgrid: 'gridestpre',
							anchoformbus: 600,
							altoformbus:100,
							anchogrid: 600,
							altogrid: 400,
							anchoven: 650,
							altoven: 500,
							ancho: 830,
							alto: 200,
							datosgridcat: dataestrucuturacat,
							colmodelocat: colmodestructuracat,
							selmodelocat: selmodestructuracat,
							rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_catestpresupuestaria.php',
							parametros: "ObjSon={'operacion':'nivelN','cantnivel':'" + cantnivel + "'}",
							tipbus:'L',
							arrfiltro:[{etiqueta:'Codigo',id:'codigo',valor:'codestpro'+cantnivel,ancho:150,longitud:25},
									   {etiqueta:'Descripcion',id:'descripcion',valor:'denestpro'+cantnivel,ancho:350,longitud:254}],
							posicion: 'position:absolute;left:5px;top:80px',
							titgrid: 'Estructura Presupuestaria',
							datosgrid: dataestrucutura,
							colmodelo: colmodestructura,
							selmodelo: selmodestructura,
							arrcampovalidaori:arrcampoval,
							arrcampovalidades:arrcampoval,
							guardarEliminados: true,
							rgeliminar: registroestnivelN
						});
	
	
	var myJSONObject ={
		"operacion":"obtenerNivel" 
	};
	
	var nivelValidacion = '';
	var ObjSon = Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/cfg/sigesp_ctr_cfg_spg_validacionestructura.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request )
		{ 
            var respuesta = result.responseText;
            if(respuesta==-1)
			{
            	Ext.MessageBox.show({
					title:'Mensaje',
					msg:'Debe configurar la opci&#243;n validar estructura presupuestaria en empresa',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO,
					fn:function(){
						location.href = 'sigesp_vis_cfg_inicio.html';
					}
				});
            }
            else
			{
            	switch(respuesta)
				{
					case 'N1':
		    			Ext.getCmp('nivval').setValue('Nivel 1');
						break;
					case 'N2':
		    			Ext.getCmp('nivval').setValue('Nivel 2');
						break;
					case 'N3':
		    			Ext.getCmp('nivval').setValue('Nivel 3');
						break;
					case 'N4':
		    			Ext.getCmp('nivval').setValue('Nivel 4');
						break;
					case 'N5':
		    			Ext.getCmp('nivval').setValue('Nivel 5');
						break;
            	}
            	
            }
        },
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});
	
	
	var myJsObject ={
		"operacion":"cargarEstructuras" 
	};
	var objSon = Ext.util.JSON.encode(myJsObject);
	var paraData ='ObjSon='+objSon;
	Ext.Ajax.request({
		url: '../../controlador/cfg/sigesp_ctr_cfg_spg_validacionestructura.php',
		params: paraData,
		method: 'POST',
		success: function ( result, request ) { 
            var respuesta = result.responseText;
            var datosJson = eval('(' + respuesta + ')');
			if(datosJson!=''){
				comliscatestructura.dataGrid.store.loadData(datosJson);
			}
        },
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'Ocurrio un error de conexion, contacte al administrador del sistema'); 
		}
	});
	
	var Xpos = ((screen.width/2)-(425));
	var plValidacionEstructura = new Ext.FormPanel({
		width: 850,
		height: 350,
		applyTo: 'formulario_valestructura',
		title: 'Validaci&#243;n de estructura presupuestaria',
		frame:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:50px;',
		items:[{
				layout : "column",
	        	defaults : {border : false},
				style:'position:absolute;left:17px;top:15px',
				items : [{
								layout : "form",
	        					border : false,
								labelWidth: 100,
	        					columnWidth : 1,
	        					items : [{
										  xtype: 'textfield',
										  fieldLabel: 'Nivel de Validaci&#243;n',
										  id: 'nivval',
										  style:'font-weight: bold;border:none;background:#f1f1f1',
										  readOnly:true,
										  labelSeparator:'',
                                          width: 80,
                                          value: nivelValidacion
										}]
		   				}]
				},
				comliscatestructura.dataGrid]
	});
	
});

function validarDatosGrabar()
{
	var valido   = true;
	var detalles = comliscatestructura.dataGrid.getStore();
	
	if(detalles.getCount() == 0){
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
		var cadenajson = "{'operacion':'grabar','codmenu':'"+codmenu+"','inc_estructuras':[";
		var detalles = comliscatestructura.dataGrid.getStore();
		var dataestructuraeliminada =comliscatestructura.dataStoreEliminados;
		var numReg = detalles.getCount()
		for (var i = 0; i <= numReg - 1; i++)
		{
			if (i == 0)
			{
				switch(cantnivel)
				{
					case 1:
		    			cadenajson = cadenajson + "{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'0000000000000000000000000',"+
											  	  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + "{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + "{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+detalles.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + "{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+detalles.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+detalles.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + "{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+detalles.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+detalles.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'"+detalles.getAt(i).get('codestpro5')+"',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
				}
			}
			else
			{
				switch(cantnivel)
				{
					case 1:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'0000000000000000000000000',"+
											  	  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+detalles.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+detalles.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+detalles.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+detalles.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+detalles.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+detalles.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+detalles.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'"+detalles.getAt(i).get('codestpro5')+"',"+
												  "'estcla':'"+detalles.getAt(i).get('estcla')+"'}";
						break;
				}
			}
		}
		cadenajson = cadenajson +"],'eli_estructuras':[";
		for (var i = 0; i <= dataestructuraeliminada.getCount() - 1; i++)
		{
			if (i == 0)
			{
				switch(cantnivel)
				{
					case 1:
		    			cadenajson = cadenajson + "{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'0000000000000000000000000',"+
												  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + "{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + "{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+dataestructuraeliminada.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + "{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+dataestructuraeliminada.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+dataestructuraeliminada.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + "{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+dataestructuraeliminada.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+dataestructuraeliminada.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'"+dataestructuraeliminada.getAt(i).get('codestpro5')+"',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
				}
			}
			else
			{
				switch(cantnivel)
				{
					case 1:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'0000000000000000000000000',"+
												  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 2:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'0000000000000000000000000',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 3:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+dataestructuraeliminada.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'0000000000000000000000000',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 4:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+dataestructuraeliminada.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+dataestructuraeliminada.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'0000000000000000000000000',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
					case 5:
		    			cadenajson = cadenajson + ",{'codestpro1':'"+dataestructuraeliminada.getAt(i).get('codestpro1')+"',"+
												  "'codestpro2':'"+dataestructuraeliminada.getAt(i).get('codestpro2')+"',"+
												  "'codestpro3':'"+dataestructuraeliminada.getAt(i).get('codestpro3')+"',"+
												  "'codestpro4':'"+dataestructuraeliminada.getAt(i).get('codestpro4')+"',"+
												  "'codestpro5':'"+dataestructuraeliminada.getAt(i).get('codestpro5')+"',"+
												  "'estcla':'"+dataestructuraeliminada.getAt(i).get('estcla')+"'}";
						break;
				}
			}
		}
		cadenajson = cadenajson + "]}";
		
		
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_spg_validacionestructura.php',
			params : parametros,
			method: 'POST',
			success: function ( resultad, request ){ 
		        var respuesta = resultad.responseText;
		       	if(respuesta=="1"){
					Ext.MessageBox.show({
		    			title:'Mensaje',
						msg: 'El proceso se ejecuto satisfactoriamente',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
		    		});
				}
				else{
					Ext.MessageBox.show({
		    			title:'Error',
						msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
		    		});
				}
				comliscatestructura.dataStoreEliminados.removeAll();
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
			} 
		});
	}
}

function irNuevo()
{
	comliscatestructura.dataStoreEliminados.removeAll();	
}