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

var fromReporteAperturaCuentas = null; //varibale para almacenar la instacia de objeto de formulario 
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
			arrfiltro:[{etiqueta:'C&#243;digo',id:'codfdesde',valor:'codfuefin',longitud:'2'},
					   {etiqueta:'Denominaci&#243;n',id:'denfdesde',valor:'denfuefin'}],
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
			arrfiltro:[{etiqueta:'C&#243;digo',id:'codfhasta',valor:'codfuefin',longitud:'2'},
					   {etiqueta:'Denominaci&#243;n',id:'denfhasta',valor:'denfuefin'}],
			posicion:'position:absolute;left:280px;top:10px', 
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
	
	//--------------------------------------------------------------------------------------------	
	
	fieldset = new Ext.form.FieldSet({
		width: 925,
		height: 200+obtenerPosicion(),
		title: '',
		style: 'position:absolute;left:10px;top:5px',
		cls :'fondo',
		items: [{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:10px;top:10px',
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
				style: 'position:absolute;left:465px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenHasta.fieldSetEstPre]
					}]
				}]
	})

	//--------------------------------------------------------------------------------------------
	
	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Intervalo de fuente de finaciamiento',
		style: 'position:absolute;left:190px;top:'+(205+obtenerPosicion())+'px',
		cls :'fondo',
		items: [comcampocatfuentefinandesde.fieldsetCatalogo,
    		    comcampocatfuentefinanhasta.fieldsetCatalogo]
  	})

	
	//--------------------------------------------------------------------------------------------

	fieldsettres = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Configurar Reporte',
		style: 'position:absolute;left:190px;top:'+(275+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:100px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								boxLabel: 'Mostrar saldos en dolares',
								id: 'chkSaldosDolares',
								inputValue:1,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'0',
								allowBlank:true,
								width: 500
							}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteAperturaCuentas = new Ext.FormPanel({
		applyTo: 'formReporteAperturaCuentas',
		width:965, //700
		height: 500,
		title: "<H1 align='center'>Listado de Apertura de Cuentas</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;', 
		items: [fieldset,fieldsetdos,fieldsettres]
	});	
	fromReporteAperturaCuentas.doLayout();
});	

	function obtenerPosicion(){
		if(empresa['numniv']=='3'){
			return 0;
		}
		else{
			return 80;
		}
	}

	function irImprimir()
	{
		var valido = true;
		var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructuraFormato();
		var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructuraFormato();
		confuefindes = Ext.getCmp('coddesde').getValue();
		confuefinhas = Ext.getCmp('codhasta').getValue();

		if(confuefindes>confuefinhas){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El rango por Fuente de Financiamiento es incorrecto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[0]!='' && arrCodigosHasta[0]=='') || (arrCodigosDesde[0]=='' && arrCodigosHasta[0]!='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[1]=='' && arrCodigosHasta[1]!='') || (arrCodigosDesde[1]!='' && arrCodigosHasta[1]=='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[2]=='' && arrCodigosHasta[2]!='') || (arrCodigosDesde[2]!='' && arrCodigosHasta[2]=='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if(empresa['estmodest']==2){
			if((arrCodigosDesde[3]=='' && arrCodigosHasta[3]!='') || (arrCodigosDesde[3]!='' && arrCodigosHasta[3]=='')){
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			if((arrCodigosDesde[4]=='' && arrCodigosHasta[4]!='') || (arrCodigosDesde[4]!='' && arrCodigosHasta[4]=='')){
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		if(valido){
			var saldosDolares = false;
			if(Ext.getCmp('chkSaldosDolares').checked){ //Mostrar saldo en Dolares
				saldosDolares = true;
			}
			imprimir('sigesp_spg_rpp_listado_apertura_pdf.php',saldosDolares,arrCodigosDesde,arrCodigosHasta,confuefindes,confuefinhas);
		}
	}

	function imprimir(nombreArchivo,saldol,arrCodigosDesde,arrCodigosHasta,confuefindes,confuefinhas)
	{	 
		//preparando los datos a enviar segun el tipo de estrutura correspondiente a la empresa
		if(empresa['estmodest']==1)
		{
			var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
			+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			+"&txtcodfuefindes="+confuefindes
			+"&txtcodfuefinhas="+confuefinhas+"&estclades="+arrCodigosDesde[5]
			+"&estclahas="+arrCodigosHasta[5];

		}else {
			var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			+"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
			+"&codestpro5="+arrCodigosDesde[4]+"&codestpro1h="+arrCodigosHasta[0]
			+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			+"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
			+"&txtcodfuefindes="+confuefindes
			+"&txtcodfuefinhas="+confuefinhas+"&estclades="+arrCodigosDesde[5]
			+"&estclahas="+arrCodigosHasta[5];

		}
		if(empresa['estmodest']==1){
			pagina="reportes/"+nombreArchivo+datosReporte;

		}
		else{
			pagina="reportes/"+nombreArchivo+datosReporte;

		}
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");

	}

