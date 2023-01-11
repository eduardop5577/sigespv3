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

var fromReporteUnidadesEjecutoras = null; //variable para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//-------------------------------------------------------------------------------------------------------------------------

	fieldSetEstOrigenDesde = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:false,
		idtxt:'comfsestdesde'
	});
	
	fieldSetEstOrigenHasta = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:false,
		idtxt:'comfsesthasta'
	});
	
	//-------------------------------------------------------------------------------------------------------------------------
	
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
			titvencat: "<H1 align='center'>Cat&#225;logo de Fuente de Financiamiento</H1>",
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
			arrfiltro:[{etiqueta:'C�digo',id:'codfdesde',valor:'codfuefin',longitud:'2'},
					   {etiqueta:'Denominaci�n',id:'denfdesde',valor:'denfuefin'}],
			posicion:'position:absolute;left:25px;top:10px', 
			tittxt:'Desde',
			idtxt:'coddesde',
			campovalue:'codfuefin',
			anchoetiquetatext:50,
			anchotext:120,
			anchocoltext:0.22,
			idlabel:'dendesde',
			labelvalue:'',
			anchocoletiqueta:0.50,
			anchoetiqueta:0,
			tipbus:'L', 
			binding:'C',
			hiddenvalue:'',
			defaultvalue:'--',
			allowblank:true,
	});
	//fin componente para el campo fuente de financiamiento/
	
	//componente campocatalogo para el campo fuente de financiamiento
	comcampocatfuentefinanhasta = new com.sigesp.vista.comCampoCatalogo({
			titvencat: "<H1 align='center'>Cat�logo de Fuente de Financiamiento</H1>",
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
			arrfiltro:[{etiqueta:'C&#243;digo',id:'codfhasta',valor:'codfuefin',longitud:'2'},
					   {etiqueta:'Denominaci&#243;n',id:'denfhasta',valor:'denfuefin'}],
			posicion:'position:absolute;left:650px;top:10px', 
			tittxt:'Hasta',
			idtxt:'codhasta',
			campovalue:'codfuefin',
			anchoetiquetatext:50,
			anchotext:120,
			anchocoltext:0.22,
			idlabel:'denhasta',
			labelvalue:'',
			anchocoletiqueta:0.50,
			anchoetiqueta:0,
			tipbus:'L', 
			binding:'C',
			hiddenvalue:'',
			defaultvalue:'--',
			allowblank:true,
	});
	//fin componente para el campo fuente de financiamiento/
	
	//-------------------------------------------------------------------------------------------------------------------------

	var unidad_ejecutora = Ext.data.Record.create([
  		{name: 'coduniadm'},
  		{name: 'denuniadm'}
  	]);
  	
  	var dsUnidadEjecutora =  new Ext.data.Store({
  	    reader: new Ext.data.JsonReader({
  		root: 'raiz',             
  		id: "id"},unidad_ejecutora)
  	});
  						
  	var cmcatUnidadEjecutora = new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'coduniadm'},
          {header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'denuniadm'}
  	]);
  	//componente campocatalogo para el campo unidad administradora
  	
  	comcampocatUnidadEjecutoradesde = new com.sigesp.vista.comCampoCatalogo({
  			titvencat: "<H1 align='center'>Cat&#225;logo de Unidades Ejecutoras</H1>",
  			anchoformbus: 450,
  			altoformbus:100,
  			anchogrid: 450,
  			altogrid: 400,
  			anchoven: 500,
  			altoven: 400,
  			anchofieldset: 850,
  			datosgridcat: dsUnidadEjecutora,
  			colmodelocat: cmcatUnidadEjecutora,
  			rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
  			parametros: "ObjSon={'operacion': 'buscarUnidadesEjecutoras'}",
  			arrfiltro:[{etiqueta:'Codigo',id:'codidesde',valor:'coduniadm',longitud:'10'},
  					   {etiqueta:'Denominaci&#243;n',id:'desdesde',valor:'denuniadm'}],
  			posicion:'position:absolute;left:20px;top:10px',
  			tittxt:'Desde',
  			idtxt:'coddes',
  			campovalue:'coduniadm',
  			anchoetiquetatext:50,
  			anchotext:120,
  			anchocoltext:0.22,
  			idlabel:'dendes',
  			labelvalue:'',
  			anchocoletiqueta:0.50,
  			anchoetiqueta:0,
  			tipbus:'L',
  			binding:'C',
  			hiddenvalue:'',
  			defaultvalue:'',
  			allowblank:false
  	});
  	
  	comcampocatUnidadEjecutorahasta = new com.sigesp.vista.comCampoCatalogo({
			titvencat: "<H1 align='center'>Cat&#225;logo de Unidades Ejecutoras</H1>",
			anchoformbus: 450,
			altoformbus:100,
			anchogrid: 450,
			altogrid: 400,
			anchoven: 500,
			altoven: 400,
			anchofieldset: 850,
			datosgridcat: dsUnidadEjecutora,
			colmodelocat: cmcatUnidadEjecutora,
			rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			parametros: "ObjSon={'operacion': 'buscarUnidadesEjecutoras'}",
			arrfiltro:[{etiqueta:'Codigo',id:'codihasta',valor:'coduniadm',longitud:'10'},
					   {etiqueta:'Denominaci&#243;n',id:'deshasta',valor:'denuniadm'}],
			posicion:'position:absolute;left:650px;top:10px',
			tittxt:'Hasta',
			idtxt:'codhas',
			campovalue:'coduniadm',
			anchoetiquetatext:50,
			anchotext:120,
			anchocoltext:0.22,
			idlabel:'denhas',
			labelvalue:'',
			anchocoletiqueta:0.50,
			anchoetiqueta:0,
			tipbus:'L',
			binding:'C',
			hiddenvalue:'',
			defaultvalue:'',
			allowblank:false
	});
	
	//--------------------------------------------------------------------------------------------	
	
  	var	fromEstructura = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 190+obtenerPosicion(),
			items: [{	
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [fieldSetEstOrigenDesde.fieldSetEstPre]
							}]
					},
					{	
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:465px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [fieldSetEstOrigenHasta.fieldSetEstPre]
						}]
					}]

	})

	//--------------------------------------------------------------------------------------------
	
	var	fromFuenteFinanciamiento = new Ext.form.FieldSet({
			xtype:"fieldset", 
    		title:'Intervalo de fuente de financiamiento',
    		style: 'position:absolute;left:10px;top:370px',
    		border:true,
    		width: 925,
    		cls :'fondo',
    		height: 70,
    		items:[comcampocatfuentefinandesde.fieldsetCatalogo,
    		       comcampocatfuentefinanhasta.fieldsetCatalogo]

  	})
	
	//--------------------------------------------------------------------------------------------
	
	var	fromUniEje = new Ext.form.FieldSet({
    		title:'Intervalo de C&#243;digo',
    		style: 'position:absolute;left:10px;top:290px',
    		border:true,
    		width: 925,
    		cls :'fondo',
    		height: 70,
    		items:[comcampocatUnidadEjecutoradesde.fieldsetCatalogo,
    		       comcampocatUnidadEjecutorahasta.fieldsetCatalogo]
  	})

	
	//--------------------------------------------------------------------------------------------

	var	fromConfigurarReporte = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:450px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 65,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 10,
							items: [{
									xtype: 'checkbox',
									labelSeparator :'',
									boxLabel: 'Imprimir todas las Unidades Administrativas',
									id: 'chkimpuniadm',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true,
									width: 200
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:650px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 10,
							items: [{
									xtype: 'checkbox',
									labelSeparator :'',
									boxLabel: 'Imprimir solo los que emiten requisici&#243;n',
									id: 'chkimpemi',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true,
									width: 200
								}]
							}]
					}]

	})
	
	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteUnidadesEjecutoras = new Ext.FormPanel({
		applyTo: 'formReporteUnidadesEjecutoras',
		width:970, //700
		height: 500,
		title: "<H1 align='center'>Unidades Ejecutoras</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;', 
		items: [fromEstructura,
		        fromUniEje,
		        fromFuenteFinanciamiento,
		        fromConfigurarReporte
		        ]
	});	
	fromReporteUnidadesEjecutoras.doLayout();
});	

//--------------------------------------------------------------------------------------------

function obtenerPosicion(){
	if(empresa['numniv']=='3'){
		return 0;
	}
	else{
		return 80;
	}
}

function irImprimir(){

	var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructuraFormato();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructuraFormato();
	var valido = true;
	var codfuefindes = Ext.getCmp('coddesde').getValue();
	var codfuefinhas = Ext.getCmp('codhasta').getValue();
	var coduniejedes = Ext.getCmp('coddes').getValue();
	var coduniejehas = Ext.getCmp('codhas').getValue();

	if(arrCodigosDesde[0] != '0000000000000000000000000') {
		if (!fieldSetEstOrigenDesde.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar la estructura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if (!fieldSetEstOrigenHasta.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if(arrCodigosHasta[0] != '0000000000000000000000000') {
		if (!fieldSetEstOrigenHasta.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar la estructura  !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if (!fieldSetEstOrigenDesde.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if((codfuefindes=="" && codfuefinhas!="") || (codfuefinhas=="" && codfuefindes!="")){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Fuente de Financiamiento !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(codfuefindes>codfuefinhas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Fuente de Financiamiento no es correcto!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((coduniejedes=="" && coduniejehas!="") || (coduniejehas=="" && coduniejedes!="")){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Unidad Ejecutora !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(codfuefindes>codfuefinhas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Unidad Ejecutora no es correcto!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		var chkunidad = 0;
		var chkemireq = 0;
		if(Ext.getCmp('chkimpuniadm').checked){ //Mostrar saldo en Dolares
			chkunidad = 1;
		}
		if(Ext.getCmp('chkimpemi').checked){ //Mostrar saldo en Dolares
			chkemireq = 1;
		}
		for ( var i = 0; i < arrCodigosDesde.length; i++) {
			if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
				arrCodigosDesde[i]="";
			}

			if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--"){
				arrCodigosHasta[i]="";
			}		
		}
		if(empresa['estmodest']==1){
			var pagina = "reportes/sigesp_spg_rpp_unidad_ejecutora.php?codestpro1="+arrCodigosDesde[0]
						+"&codestpro2="+arrCodigosDesde[1]+"&codestpro3="+arrCodigosDesde[2]
						+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
						+"&codestpro3h="+arrCodigosHasta[2]+"&chkemireq="+chkemireq
						+"&chkunidad="+chkunidad+"&txtcoduniadmdes="+coduniejedes
						+"&txtcoduniadmhas="+coduniejehas+"&txtcodfuefindes="+codfuefindes
						+"&txtcodfuefinhas="+codfuefinhas+"&estclades="+arrCodigosDesde[5]
						+"&estclahas="+arrCodigosHasta[5]+"&tipoformato="+"0";
		}
		else{
			var pagina = "reportes/sigesp_spg_rpp_unidad_ejecutora.php?codestpro1="+arrCodigosDesde[0]
						+"&codestpro2="+arrCodigosDesde[1]+"&codestpro3="+arrCodigosDesde[2]
						+"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
						+"&codestpro1h="+arrCodigosHasta[0]+"&codestpro2h="+arrCodigosHasta[1]
						+"&codestpro3h="+arrCodigosHasta[2]+"&codestpro4h="+arrCodigosHasta[3]
						+"&codestpro5h="+arrCodigosHasta[4]+"&chkemireq="+chkemireq
						+"&chkunidad="+chkunidad+"&txtcoduniadmdes="+coduniejedes
						+"&txtcoduniadmhas="+coduniejehas+"&txtcodfuefindes="+codfuefindes
						+"&txtcodfuefinhas="+codfuefinhas+"&estclades="+arrCodigosDesde[5]
						+"&estclahas="+arrCodigosHasta[5]+"&tipoformato="+"0";
		}
	}
	window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
}

