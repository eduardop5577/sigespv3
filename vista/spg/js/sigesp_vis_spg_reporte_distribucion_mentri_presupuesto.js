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

var fromReporteDisMenTriPre = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//-------------------------------------------------------------------------------------

	fieldSetEstOrigenDesde = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Desde',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsestdesde',
		nofiltroest:'1'
	});
	
	fieldSetEstOrigenHasta = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Hasta',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsesthasta',
		nofiltroest:'1'
	});
	
    //-------------------------------------------------------------------------------------
	
	//Datos para el formato de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------
	fromEstructura = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 265+obtenerPosicion(),
			items: [{	
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [fieldSetEstOrigenDesde.fsEstructura]
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
							items: [fieldSetEstOrigenHasta.fsEstructura]
							}]
					}]
	});
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromNivelReporte = new Ext.form.FieldSet({
			title:'Distribuci&#243;n',
			style: 'position:absolute;left:10px;top:360px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:300px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 40,
							items: [{
									xtype: 'checkbox',
									labelSeparator :'',
									boxLabel:'Mensual',
									fieldLabel: '',
									id: 'estmodapemen',
									/*readOnly:true,*/
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true,
									listeners:{	
										'check': function (checkbox, checked){
											if(checked){
												Ext.getCmp('estmodapetri').setValue(false);
											}
										}
									}
								}]
							},
							{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'checkbox',
									labelSeparator :'',
									boxLabel:'Trimestral',
									fieldLabel: '',
									/*readOnly:true,*/
									id: 'estmodapetri',
									inputValue:1,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'0',
									allowBlank:true,
									listeners:{	
										'check': function (checkbox, checked){
											if(checked){
												Ext.getCmp('estmodapemen').setValue(false);
											}
										}
									}
								}]
							}]
				}]
	})

	//--------------------------------------------------------------------------------------------
	
	var	fromTipoImpresion = new Ext.form.FieldSet({
			title:'Tipo de Impresion',
			style: 'position:absolute;left:10px;top:425px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:270px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'combo',
									fieldLabel: '',
									labelSeparator :'',
									id: 'tipoimp',
									store : stOpcimp,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									typeAhead : true,
									triggerAction : 'all',
									mode : 'local',
									emptyText:'PDF',
									listWidth:150,
									width:150
								}]
							}]
					}]
		})
	
	//--------------------------------------------------------------------------------------------
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteDisMenTriPre = new Ext.FormPanel({
		applyTo: 'formReporteDisMenTriPre',
		width:950, //700
		height: 600,
		title: "<H1 align='center'>Distribuci&#243;n Mensual/Trimestral del Presupuesto</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromNivelReporte,
		        fromEstructura,
		        fromTipoImpresion
		        ]
	});	
	verificarEstatus();
	fromReporteDisMenTriPre.doLayout();
});	

	//-------------------------------------------------------------------------------------------------------------------------	

	function irImprimir(){
		
		var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
		var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
		var opcionimp = 'P';
		var valido = true;
		
		if(arrCodigosDesde[6] > arrCodigosHasta[6]){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por fuente de financiamiento no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if(arrCodigosDesde[7] > arrCodigosHasta[7]){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por cuenta no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(arrCodigosDesde[0]!='0000000000000000000000000') {
			if(!fieldSetEstOrigenDesde.validarEstructuraCompleta()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe seleccionar toda la estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			
			if(!fieldSetEstOrigenHasta.validarEstructuraCompleta()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		
		if(arrCodigosHasta[0]!='0000000000000000000000000') {
			if(!fieldSetEstOrigenHasta.validarEstructuraCompleta()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe seleccionar toda la estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			
			if(!fieldSetEstOrigenDesde.validarEstructuraCompleta()) {
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		
		if((arrCodigosDesde[6]=="" && arrCodigosHasta[6]!="") || (arrCodigosDesde[6]!="" && arrCodigosHasta[6]=="")){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Fuente de Financiamiento !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[7]=="" && arrCodigosHasta[7]!="") || (arrCodigosDesde[7]!="" && arrCodigosHasta[7]=="")){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if(valido){
			if(Ext.getCmp('tipoimp').getValue()!=''){
				opcionimp=Ext.getCmp('tipoimp').getValue();
			}
			if(empresa['estmodest']==1){
				if((arrCodigosDesde[0]!="" && arrCodigosDesde[1]!="" && arrCodigosDesde[2]!="") || (arrCodigosHasta[0]!="" && arrCodigosHasta[1]!="" && arrCodigosHasta[2]!="")){
					var datosReportes = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
					                   +"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
					                   +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
					                   +"&txtcodfuefindes="+arrCodigosDesde[6]
					                   +"&txtcodfuefinhas="+arrCodigosHasta[6]+"&estclades="+arrCodigosDesde[5]
					                   +"&estclahas="+arrCodigosHasta[5]+"&txtcuentades="+arrCodigosDesde[7]
					                   +"&txtcuentahas="+arrCodigosHasta[7];
				}
				else{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe seleccionar un rango de estructuras programatica !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
					valido = false;
				}
			}
			else{
				if((arrCodigosDesde[0]!="" && arrCodigosDesde[1]!="" && arrCodigosDesde[2]!="") || (arrCodigosHasta[0]!="" && arrCodigosHasta[1]!="" && arrCodigosHasta[2]!="")){
					var datosReportes = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
					                   +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
					                   +"&codestpro5="+arrCodigosDesde[4]+"&codestpro1h="+arrCodigosHasta[0]
					                   +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
					                   +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
					                   +"&txtcodfuefindes="+arrCodigosDesde[6]
					                   +"&txtcodfuefinhas="+arrCodigosHasta[6]+"&estclades="+arrCodigosDesde[5]
					                   +"&estclahas="+arrCodigosHasta[5]+"&txtcuentades="+arrCodigosDesde[7]
					                   +"&txtcuentahas="+arrCodigosHasta[7];
				}
				else{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe seleccionar un rango de estructuras programatica !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
					valido = false;
				}
			}
			if(valido){
				imprimir(datosReportes,opcionimp);
			}
		}
	}
	
	function obtenerPosicion(){
		if(empresa['numniv']=='3'){
			return 0;
		}
		else{
			return 80;
		}
	}

	function imprimir(datos,opcion)
	{
		var pagina = '';
		if(Ext.getCmp('estmodapemen').checked){
			if(opcion=='P'){
				pagina ="reportes/sigesp_spg_rpp_distribucion_mensual_presupuesto.php"+datos;
			}
			else{
				pagina ="reportes/sigesp_spg_rpp_distribucion_mensual_presupuesto_excel.php"+datos;
			}
		}
		else if(Ext.getCmp('estmodapetri').checked){
			if(opcion=='P'){
				pagina ="reportes/sigesp_spg_rpp_distribucion_trimestral_presupuesto.php"+datos;
			}
			else{
				pagina ="reportes/sigesp_spg_rpp_distribucion_trimestral_presupuesto_excel.php"+datos;
			}
		}
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
		
	function verificarEstatus()
	{
		if(empresa['estmodape']=="0"){
			Ext.getCmp('estmodapemen').setValue(true);
		}
		else{
			Ext.getCmp('estmodapetri').setValue(true);
		}
	}