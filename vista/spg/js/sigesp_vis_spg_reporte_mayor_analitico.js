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

var fromReporteMayorAnalitico = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

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

	//Datos del tipo de formato a imprimir
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ],
				   [ 'EXCEL SIN ENCABEZADOS', 'ESC' ]];

	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});

	//--------------------------------------------------------------------------------------------

	fieldset = new Ext.form.FieldSet({
		width: 930,
		height: 275+obtenerPosicion(),
		title: '',
		style: 'position:absolute;left:5px;top:5px',
		cls :'fondo',
		items: [{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:5px;top:10px',
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
				style: 'position:absolute;left:470px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenHasta.fsEstructura]
					}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Configurar Reporte',
		style: 'position:absolute;left:190px;top:'+(400+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:5px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 100,
						items: [{
								xtype: 'checkbox',
								labelSeparator :'',
								boxLabel: 'Ocultar Campo Detalle de los Comprobantes Presupuestarios',
								id: 'chkCampoOculto',
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

	fieldsettres = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Ordenado por',
		style: 'position:absolute;left:190px;top:'+(340+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:30px;top:10px',
				items: [{
						layout: "form",
						border: false,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,200],
								id:'rdOrden',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [{
										boxLabel: 'Fecha',
										name: 'orden',
										inputValue: '1',
										checked:true
										},
										{
										boxLabel: 'Nro. Documento',
										name: 'orden',
										inputValue: '0'
										}]
								}]
					}]
			}]
	})

	//--------------------------------------------------------------------------------------------

	fieldsetcuatro = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Intervalo de Fechas',
		style: 'position:absolute;left:190px;top:'+(280+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:80px;top:10px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype:"datefield",
								labelSeparator :'',
								fieldLabel:"Desde",
								name:'Desde',
								id:'dtFechaDesde',
								allowBlank:true,
								width:100,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								allowBlank:false,
								value: '01/01/'+fecha.getFullYear(),
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				},
				{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:300px;top:10px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype:"datefield",
								labelSeparator :'',
								fieldLabel:"Hasta",
								name:'Hasta',
								id:'dtFechaHasta',
								allowBlank:true,
								width:100,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								allowBlank:false,
								value:  new Date().format('d-m-Y'),
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	fieldsetcinco = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Tipo de Impresion',
		style: 'position:absolute;left:190px;top:'+(460+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
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
								width:150,
							}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteMayorAnalitico = new Ext.FormPanel({
		applyTo: 'formReporteMayorAnalitico',
		width:965, //700
		height: 500,
		title: "<H1 align='center'>Mayor Anal&#225;tico</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fieldset,fieldsetdos,fieldsettres,fieldsetcuatro,fieldsetcinco]
	});	
	fromReporteMayorAnalitico.doLayout();
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
	var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
	var opcionimp = 'P';
	var orden = 'F';
	var valido = true;
	var fechaReporteDesde = Ext.getCmp('dtFechaDesde').getValue().format('d/m/Y');
	var fechaReporteHasta = Ext.getCmp('dtFechaHasta').getValue().format('d/m/Y');
	
	if(arrCodigosDesde[6] > arrCodigosHasta[6])
	{
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por fuente de financiamiento no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(arrCodigosDesde[7] > arrCodigosHasta[7])
	{
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por cuenta no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}	
	if(arrCodigosDesde[0]!='0000000000000000000000000')
	{
		if(!fieldSetEstOrigenDesde.validarEstructuraCompleta())
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe seleccionar toda la estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(!fieldSetEstOrigenHasta.validarEstructuraCompleta())
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if(arrCodigosHasta[0]!='0000000000000000000000000')
	{
		if(!fieldSetEstOrigenHasta.validarEstructuraCompleta())
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe seleccionar toda la estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(!fieldSetEstOrigenDesde.validarEstructuraCompleta())
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if((arrCodigosDesde[6]=="" && arrCodigosHasta[6]!="") || (arrCodigosDesde[6]!="" && arrCodigosHasta[6]==""))
	{
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Fuente de Financiamiento!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((arrCodigosDesde[7]=="" && arrCodigosHasta[7]!="") || (arrCodigosDesde[7]!="" && arrCodigosHasta[7]==""))
	{
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido)
	{
		for ( var i = 0; i < arrCodigosDesde.length; i++)
		{
			if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--")
			{
				arrCodigosDesde[i]="";
			}
			if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--")
			{
				arrCodigosHasta[i]="";
			}		
		}
		if(Ext.getCmp('tipoimp').getValue()!=''){
			opcionimp=Ext.getCmp('tipoimp').getValue();
		}
		var campoOculto = '0';
		if(Ext.getCmp('chkCampoOculto').checked){ //Ocultar campo detalle
			campoOculto = '1';
		}

		if (Ext.getCmp('rdOrden').items.items[0].checked)
		{
			orden = 'F';
		}
		else if(Ext.getCmp('rdOrden').items.items[1].checked)
		{
			orden = 'D';
		}

		if (Ext.getCmp('dtFechaDesde').getValue() > Ext.getCmp('dtFechaHasta').getValue())
		{
			alert('Por favor verifique el intervalo de fechas');
		}
		
		if(arrCodigosDesde[7] > arrCodigosHasta[7])
		{
			alert('Verifique el intervalo de cuentas seleccionado');

		}else
		{
			if(opcionimp=='P')
			{
				imprimir('MAYOR ANALITICO','sigesp_spg_rpp_mayor_analitico_pdf.php',fechaReporteDesde,orden,fechaReporteHasta,arrCodigosDesde,arrCodigosHasta,campoOculto,'');
		
			}
			else 
			{
				if(opcionimp=='E')
				{
					imprimir('','',fechaReporteDesde,orden,fechaReporteHasta,arrCodigosDesde,arrCodigosHasta,campoOculto,'sigesp_spg_rpp_mayor_analitico_excel.php');
				}
				else
				{
					imprimir('','',fechaReporteDesde,orden,fechaReporteHasta,arrCodigosDesde,arrCodigosHasta,campoOculto,'sigesp_spg_rpp_mayor_analitico_sinencabezado_excel.php');
				}
			}
		}
	}
}

function imprimir(variable,nombreArchivo,fechaReporteDesde,orden,fechaReporteHasta,arrCodigosDesde,arrCodigosHasta,campoOculto,ruta)
{	
	//preparando los datos a enviar segun el tipo de estrutura correspondiente a la empresa
	if(empresa['estmodest']==1)
	{
		var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
		+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
		+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
		+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
		+"&txtfecdes="+fechaReporteDesde+"&rborden="+orden+"&txtfechas="+fechaReporteHasta
		+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
		+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]+"&mostrar="+campoOculto;
	}
	else
	{
		var datosReporte ="?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
		+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
		+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
		+"&codestpro4="+arrCodigosDesde[3]+"&codestpro4h="+arrCodigosHasta[3]
		+"&codestpro5h="+arrCodigosHasta[4]+"&codestpro5h="+arrCodigosHasta[4]
		+"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
		+"&txtfecdes="+fechaReporteDesde+"&rborden="+orden+"&txtfechas="+fechaReporteHasta
		+"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
		+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]+"&mostrar="+campoOculto;
	}
	
	if(ruta=='')
	{
		var myJSONObject =
		{
				'operacion'   : 'buscarFormato',
				'sistema'	  : 'SPG',
				'seccion'     : 'REPORTE',
				'variable'    : variable,
				'valor'		  : nombreArchivo,
				'tipo'		  : 'C'
		};	
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function (result, request)
			{ 
				formato = result.responseText;	
				if(empresa['estmodest']==1)
				{
					pagina="reportes/"+formato+datosReporte;
				}
				else
				{
					pagina="reportes/"+formato+datosReporte;
					
				}
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			},
			failure: function (result, request){ 
				Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
			}
		})
	}
	else
	{
		if(empresa['estmodest']==1)
		{
			pagina="reportes/"+ruta+datosReporte;
			
		}
		else
		{
			pagina="reportes/"+ruta+datosReporte;
		}
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}