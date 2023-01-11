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
var banderaEliminar		= false;												// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaImprimir 	= false;
var cantnivel           = parseInt(empresa['numniv']);	//variable que contiene la cantidad de niveles del presupuesto
var loncodestpro1      	= parseInt(empresa['loncodestpro1']);	//variable que contiene la longitud del nivel 1
var loncodestpro2		= parseInt(empresa['loncodestpro2']);	//variable que contiene la longitud del nivel 2
var loncodestpro3		= parseInt(empresa['loncodestpro3']);	//variable que contiene la longitud del nivel 3
var loncodestpro4		= parseInt(empresa['loncodestpro4']);	//variable que contiene la longitud del nivel 4
var loncodestpro5		= parseInt(empresa['loncodestpro5']);	//variable que contiene la longitud del nivel 5
var comListaCatFuente   = null;
var fieldSetEstructura  = null;
var plCasamientoEstructuraFuente = null;


Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//TODO funcion limpiar grid para agregarle al onAceptar del comoponente fieldsetEstructura
	function limpiarGridFuente()
	{
		comListaCatFuente.dataGrid.getStore().removeAll();
		comListaCatFuente.dataStoreEliminados.removeAll();
		irBuscar();
	} 
	
	//COMPONENTE FIELDSET ESTRUCTURA
	fieldSetEstructura = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		mostrarDenominacion:true,
		idtxt:'1',
		onAceptar:true,
		fnOnAceptar:limpiarGridFuente
	});
	
	//STORE Y COLUMMODEL CATALOGO FUENTE FINACIAMIENTO
	var reFuenteFinCat = Ext.data.Record.create([
		{name: 'codfuefin'},
		{name: 'denfuefin'}
	]);
	
	var dsFuenteFinCat =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reFuenteFinCat)
	});
						
	var cmFuenteFinCat = new Ext.grid.ColumnModel([
        {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codfuefin'},
        {header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denfuefin'}
    ]);
    
    //STORE Y COLUMMODEL GRID FUENTE FINACIAMIENTO
	var reFuenteFin = Ext.data.Record.create([
		{name: 'codfuefin'},
		{name: 'denfuefin'},
		{name: 'registrocat'}
	]);
	
	var dsFuenteFin =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reFuenteFin)
	});
						
	var cmFuenteFin = new Ext.grid.ColumnModel([
        {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codfuefin'},
        {header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denfuefin'}
    ]);
	
	//COMPONENTE LISTA CATALOGO (FUENTE FINANCIAMIENTO)
    var xTop = '180';
    if(cantnivel == 5)
	{
    	xTop = '290';
    }
	comListaCatFuente = new com.sigesp.vista.comListaCatalogo({
		titvencat: 'Cat&#225;logo de Fuentes de Financiamiento',
		idgrid: 'gridfuefin',
		anchoformbus: 450,
		altoformbus:100,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		ancho: 700,
		alto: 250,
		datosgridcat: dsFuenteFinCat,
		colmodelocat: cmFuenteFinCat,
		selmodelocat: new Ext.grid.CheckboxSelectionModel({}),
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_spg_estructurafuente.php',
		parametros: "ObjSon={'operacion':'catalogo_fuente'}",
		tipbus:'L',
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codigo',valor:'codfuefin'},
				   {etiqueta:'Denominaci&#243;n',id:'descripcion',valor:'denfuefin'}],
		posicion: 'position:absolute;left:0px;top:'+xTop+'px',
		titgrid: 'Fuente de Financiamiento',
		datosgrid: dsFuenteFin,
		colmodelo: cmFuenteFin,
		selmodelo: new Ext.grid.CheckboxSelectionModel({}),
		arrcampovalidaori:['codfuefin'],
		arrcampovalidades:['codfuefin'],
		guardarEliminados: true,
		rgeliminar: reFuenteFin
	});
	
	var Xpos = ((screen.width/2)-450);
	plCasamientoEstructuraFuente = new Ext.FormPanel({
		applyTo: 'formulario_casamientoestfuen',
		width: 900,
		height: 600,
		title: 'Casamiento Estructura Fuente Financiamiento',
		frame:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:50px;',
		items: [fieldSetEstructura.fieldSetEstPre,
				comListaCatFuente.dataGrid]
	});
	
});

function irGuardar()
{
	var arrEstructura = fieldSetEstructura.obtenerArrayEstructura();
	var dsFuentes    = comListaCatFuente.dataGrid.getStore();
	var dsFuenteEli  = comListaCatFuente.dataStoreEliminados;
   	var first = true;
	var cadenaJson = "{'operacion':'grabar', 'codmenu':'"+codmenu+"', 'arrFuentesIncluir':[";
	dsFuentes.each(function (registroGrid)
	{
		if(first)
		{
			cadenaJson = cadenaJson + "{'codfuefin':'"+registroGrid.get('codfuefin')+"'," +
									  " 'codestpro1':'"+arrEstructura[0]+"'," +
									  " 'codestpro2':'"+arrEstructura[1]+"'," +
									  " 'codestpro3':'"+arrEstructura[2]+"'," +
									  " 'codestpro4':'"+arrEstructura[3]+"'," +
									  " 'codestpro5':'"+arrEstructura[4]+"'," +
									  " 'estcla':'"+arrEstructura[5]+"'}";
			first = false;
		}
		else
		{
			cadenaJson = cadenaJson + ",{'codfuefin':'"+registroGrid.get('codfuefin')+"'," +
									  " 'codestpro1':'"+arrEstructura[0]+"'," +
									  " 'codestpro2':'"+arrEstructura[1]+"'," +
									  " 'codestpro3':'"+arrEstructura[2]+"'," +
									  " 'codestpro4':'"+arrEstructura[3]+"'," +
									  " 'codestpro5':'"+arrEstructura[4]+"'," +
									  " 'estcla':'"+arrEstructura[5]+"'}";
		}
	});
	cadenaJson = cadenaJson + "],'arrFuentesEliminar':[";
	
	var firstEli = true;
	dsFuenteEli.each(function (registroGridEli)
	{
		if(firstEli)
		{
			cadenaJson = cadenaJson + "{'codfuefin':'"+registroGridEli.get('codfuefin')+"'," +
									  " 'codestpro1':'"+arrEstructura[0]+"'," +
									  " 'codestpro2':'"+arrEstructura[1]+"'," +
									  " 'codestpro3':'"+arrEstructura[2]+"'," +
									  " 'codestpro4':'"+arrEstructura[3]+"'," +
									  " 'codestpro5':'"+arrEstructura[4]+"'," +
									  " 'estcla':'"+arrEstructura[5]+"'}";
			firstEli = false;
		}
		else
		{
			cadenaJson = cadenaJson + ",{'codfuefin':'"+registroGridEli.get('codfuefin')+"'," +
									  " 'codestpro1':'"+arrEstructura[0]+"'," +
									  " 'codestpro2':'"+arrEstructura[1]+"'," +
									  " 'codestpro3':'"+arrEstructura[2]+"'," +
									  " 'codestpro4':'"+arrEstructura[3]+"'," +
									  " 'codestpro5':'"+arrEstructura[4]+"'," +
									  " 'estcla':'"+arrEstructura[5]+"'}";
		}
	});
	cadenaJson = cadenaJson + "]}";

	var parametros = 'ObjSon='+cadenaJson;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_estructurafuente.php',
		params : parametros,
		method: 'POST',
		success: function ( resultad, request )
		{ 
	        var respuesta = resultad.responseText;
	        respuesta = respuesta.split("|");
	        var arrFuenteError = respuesta[1].split(",");
        	var msjError = '';
        	for (var i = 0; i < arrFuenteError.length; i++)
			{
        		if(arrFuenteError[i]!='')
				{
        			msjError = msjError + "<br> La Fuente Finaciamiento "+arrFuenteError[i]+", tiene movimientos no puede ser eliminada.";
        		}
        		else
				{
        			msjError = '';
        		}
        	}
	        if(respuesta[0]=='1')
			{
	        	Ext.MessageBox.show({
	    			title:'Mensaje',
					msg: 'La informaci&#243;n fue procesada exitosamente'+msjError,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
	    		});
	        }
	        else
			{
	        	Ext.MessageBox.show({
	    			title:'Error',
					msg: 'Ha ocurrido un error procesando la informaci&#243;n '+msjError,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
	    		});
	        }
	        fieldSetEstructura.limpiarEstructuras(-1);
	       	comListaCatFuente.dataGrid.getStore().removeAll();
			comListaCatFuente.dataStoreEliminados.removeAll();
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
		} 
	});
}

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	fieldSetEstructura.limpiarEstructuras(-1);
	comListaCatFuente.dataGrid.getStore().removeAll();
	comListaCatFuente.dataStoreEliminados.removeAll();
}

function irBuscar()
{
	var arrEstructura = fieldSetEstructura.obtenerArrayEstructura();
	var cadenaJson = "{'operacion':'buscar_fuentes'," +
					 " 'codestpro1':'"+arrEstructura[0]+"'," +
					 " 'codestpro2':'"+arrEstructura[1]+"'," +
					 " 'codestpro3':'"+arrEstructura[2]+"'," +
					 " 'codestpro4':'"+arrEstructura[3]+"'," +
					 " 'codestpro5':'"+arrEstructura[4]+"'," +
					 " 'estcla':'"+arrEstructura[5]+"'}";
	var parametros = 'ObjSon='+cadenaJson;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_estructurafuente.php',
		params : parametros,
		method: 'POST',
		success: function ( resultad, request ){ 
	        var dataFuente    = resultad.responseText;
	        var objDataFuente = eval('(' + dataFuente + ')');
	        if(objDataFuente.raiz != ''){
	        	comListaCatFuente.dataGrid.getStore().loadData(objDataFuente);
	        }
			else {
				Ext.MessageBox.show({
	    			title:'Mensaje',
					msg: 'La estructura no tiene fuentes de financiamiento asociadas',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
	    		});
			}
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
		} 
	});
}