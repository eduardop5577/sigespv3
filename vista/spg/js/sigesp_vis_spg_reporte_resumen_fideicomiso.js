/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var fromReporteResFideicomiso = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//--------------------------------------------------------------------------------------------	

	//Creando el campo de fuente de financiamiento
	var reFuenteFinan = Ext.data.Record.create([
		  {name: 'codfuefin'},
		  {name: 'denfuefin'}
	]);
	                                    	
	var dsFuenteFinan =  new Ext.data.Store({
		 reader: new Ext.data.JsonReader({
		 root: 'raiz',             
		 id: "id"},reFuenteFinan)
	});
	                                    						
	var colmodelcatfuentefinan = new Ext.grid.ColumnModel([
		 {header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'codfuefin'},
		 {header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denfuefin'}
	]);
		
	//componente campocatalogo para el campo fuente de financiamiento
	comcampocatfuentefinandesde = new com.sigesp.vista.comCampoCatalogo({
			titvencat: "<H1 align='center'>Catálogo de Fuente de Financiamiento</H1>",
			anchoformbus: 450,
			altoformbus:100,
			anchogrid: 450,
			altogrid: 380,
			anchoven: 500,
			altoven: 400,
			anchofieldset: 850,
			datosgridcat: dsFuenteFinan,
			colmodelocat: colmodelcatfuentefinan, 
			rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
			parametros: "ObjSon={'operacion': 'buscarFuenteFinanciamiento'}",
			arrfiltro:[{etiqueta:'Código',id:'codfdesde',valor:'codfuefin',longitud:'2'},
					   {etiqueta:'Denominación',id:'denfdesde',valor:'denfuefin'}],
			posicion:'position:absolute;left:20px;top:10px', 
			tittxt:'Código',
			idtxt:'codfuefin',
			campovalue:'codfuefin',
			anchoetiquetatext:50,
			anchotext:120,
			anchocoltext:0.22,
			idlabel:'dendesde',
			labelvalue:'denfuefin',
			anchocoletiqueta:0.50,
			anchoetiqueta:250,
			tipbus:'L', 
			binding:'C',
			hiddenvalue:'',
			defaultvalue:'--',
			allowblank:true,
	});
	//fin componente para el campo fuente de financiamiento/
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromFuenteFinanciamiento = new Ext.form.FieldSet({
			xtype:"fieldset", 
    		title:'Fuente de Finaciamiento',
    		style: 'position:absolute;left:10px;top:10px',
    		border:true,
    		width: 570,
    		cls :'fondo',
    		height: 80,
    		items:[comcampocatfuentefinandesde.fieldsetCatalogo]
  	})
	
	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteResFideicomiso = new Ext.FormPanel({
		applyTo: 'formReporteResFideicomiso',
		width:600, //700
		height: 150,
		title: "<H1 align='center'>RESUMEN FIDEICOMISO</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromFuenteFinanciamiento]
	});	
	fromReporteResFideicomiso.doLayout();
});

//--------------------------------------------------------------------------------------------	

function irImprimir()
{
	var codfuefin = Ext.getCmp('codfuefin').getValue();
	if(codfuefin!=''){
		var pagina="reportes/sigesp_spg_rpp_resumen_fideicomiso.php?txtcodfuefindes="+codfuefin;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
	else{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Por Favor Seleccionar todos los parametros de busqueda!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
}

