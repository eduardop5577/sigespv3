/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var gridComprCauParcialmente  = null //varibale para almacenar la instacia de objeto de grid de los movimientos 
var fromContabilzarSPG = null //varibale para almacenar la instacia de objeto de formulario 
var comcampocatcomprobante = null;
var fecha = new Date(); 
barraherramienta    = true;
Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.Ajax.timeout=36000000000;
					 
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	
	var Xpos = ((screen.width/2)-(920/2));
	var	plContabilzarSPG = new Ext.FormPanel({
		applyTo: 'formularioSPG',
		width: 950,
		height: 500,
		style: 'position:absolute;left:200px;top:80px',
		title: "<H1 align='center'>Cierre/Disminucion de Compromisos</H1>",
		frame: true,
		autoScroll:true,
		items: [fromContabilzarSPG,gridComprCauParcialmente]
	});
	plContabilzarSPG.doLayout();
});
	
	var tipocompromiso = 	[
                     	['Solicitud de ejecucion Presupuestaria','SEPSPC'],
 			['Orden de Compra','SOCCOC'],
                        ['Orden de Servicio','SOCCOS']]; 
 	
 	var sttipocompromiso = new Ext.data.SimpleStore({
 		fields : [ 'etiqueta', 'valor' ],
 		data : tipocompromiso
 	});

 	var cmbtipocompromiso = new Ext.form.ComboBox({
 		store : sttipocompromiso,
 		fieldLabel : 'Tipo Compromiso ',
 		labelSeparator : '',
 		editable : false,
 		displayField : 'etiqueta',
 		valueField : 'valor',
 		id : 'sistema',
 		width:200,
 		typeAhead: true,
 		triggerAction:'all',
 		forceselection:true,
 		binding:true,
 		mode:'local',
                value:'SEPSPC',
                listeners: {'select':irLimpiar}
 	});

var reg_comprobante = Ext.data.Record.create([
    {name: 'comprobante'},
    {name: 'cod_pro'},
    {name: 'codigo'},
    {name: 'fecha'},
    {name: 'monto'},
    {name: 'montocierre'}
]);

var dscomprobante =  new Ext.data.Store({
	reader: new Ext.data.JsonReader({
		root: 'raiz',             
		id: "id"},reg_comprobante)
});

var colmodelcomprobante = new Ext.grid.ColumnModel([
    {header: "<CENTER>Comprobante<CENTER>", width: 50, sortable: true,   dataIndex: 'comprobante'},
    {header: "<CENTER>Proveedor/Beneficiario</CENTER>", width: 150, sortable: true,   dataIndex: 'codigo'},
    {header: "<CENTER>fecha</CENTER>", width: 50, sortable: true,   dataIndex: 'fecha'},
    {header: "<CENTER>Monto Compromiso</CENTER>", width: 50, sortable: true, dataIndex: 'monto'},
    {header: "<CENTER>Monto Disponible</CENTER>", width: 50, sortable: true, dataIndex: 'montocierre'}
]);
//fin del campo de proveedores

//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
comcampocatcomprobante = new com.sigesp.vista.comCampoCatalogo({
	titvencat: "<H1 align='center'>Catalogo de Compromisos</H1>",
	anchoformbus: 770,
	altoformbus:180,
	anchogrid: 770,
	altogrid: 520,
	anchoven: 800,
	altoven: 600,
	datosgridcat: dscomprobante,
	colmodelocat: colmodelcomprobante,
	rutacontrolador:'../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
	parametros: "ObjSon={'operacion': 'catalogo_compromisos', 'sistema': '"+Ext.getCmp('sistema').getValue()+"'}",
	arrfiltro:[{etiqueta:'Comprobante',id:'mcomprobante',valor:'comprobante',longitud:'15'},
                   {etiqueta:'Fecha Desde',id:'mfecdes',valor:'fecha',tipo:'datefield',defecto:fecha.getFullYear()+'-01-01'},
                   {etiqueta:'Fecha Hasta',id:'mfechas',valor:'fecha',tipo:'datefield',defecto:obtenerFechaActual()},
                   {etiqueta:'Proveedor',id:'mcodigo',valor:'codigo'}],
	posicion:'position:absolute;left:5px;top:40px',
	tittxt:'Compromiso',
	idtxt:'comprobante',
	campovalue:'comprobante',
	anchoetiquetatext:150,
	anchotext:135,
	anchocoltext:0.50,
	anchocoletiqueta:0.45,
	anchoetiqueta:400,
	anchofieldset: 700,
	tipbus:'P',
	hiddenvalue:'',
	defaultvalue:'',
        arrtxtfiltro:['sistema'],
	allowblank:false,
        onAceptar:true,
        fnOnAceptar:irBuscar
});


	var reMovPresupuestario = Ext.data.Record.create([
                                {name: 'comprobante'}, 
                                {name: 'procede'}, 
                                {name: 'codban'}, 
                                {name: 'ctaban'}, 
                                {name: 'estructura'}, 
                                {name: 'codestpro1'},
                                {name: 'codestpro2'},
                                {name: 'codestpro3'},
                                {name: 'codestpro4'},
                                {name: 'codestpro5'},
                                {name: 'estcla'},
                                {name: 'spg_cuenta'},
                                {name: 'codfuefin'},
                                {name: 'codcencos'},
                                {name: 'procede_doc'},
                                {name: 'documento'},
                                {name: 'operacion'},
                                {name: 'descripcion'},
                                {name: 'montocompromiso'},
                                {name: 'montocausado'},
                                {name: 'disponible'},
                                {name: 'montocierre'}
                            ]);

	var dsMovPresupuestario =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovPresupuestario)
	});

	var cmMovPresupuestario = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Estructura</CENTER>", width: 100, sortable: true, dataIndex: 'estructura'},
	    {header: "<CENTER>Cuenta</CENTER>", width: 50, sortable: true, dataIndex: 'spg_cuenta'},
            {header: "<CENTER>Descripci&#243;n</CENTER>", id:'denominacion',width: 180, setEditable: true,sortable: true, dataIndex: 'descripcion', 
             editor: new Ext.form.TextField({allowBlank: false,
                                             autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=ρ;:[]{}αινσϊΡ ');"}
                                             })},
	    {header: "<CENTER>Monto Compromiso</CENTER>", width: 70, sortable: true, dataIndex: 'montocompromiso'},
	    {header: "<CENTER>Monto Causado</CENTER>", width: 70, sortable: true, dataIndex: 'montocausado'},
            {header: '<CENTER>Monto Cierre/disminucion</CENTER>', width: 70, sortable: true, dataIndex: 'montocierre', align: 'right', 
             editor: new Ext.form.TextField({
					maxLength:15,
					minLength:1,
					style: 'text-align:right',
					moneda:true,
					precision:2,
					autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
					listeners:{
								'blur':function(objeto)
														{
															var numero = objeto.getValue();
																valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
																objeto.setValue(valor);
															
														},
								'focus':function(objeto)
								{
									var numero = formatoNumericoEdicion(objeto.getValue());
										objeto.setValue(numero);
									
								}
							}
				})},
	]);

	gridComprCauParcialmente = new Ext.grid.EditorGridPanel({
		width:900,
		height:300,
		frame:true,
		title:'',
		style: 'position:absolute;left:15px;top:130px',
		autoScroll:true,
		border:true,
		ds: dsMovPresupuestario,
		cm: cmMovPresupuestario,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});

	var Xpos = ((screen.width/2)-(300));
	fromContabilzarSPG = new Ext.form.FieldSet({
		    title:'',
		    style: 'position:absolute;left:15px;top:15px',
			border:true,
			width: 900,
			cls: 'fondo',
			height: 110,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:20px',
					items: [{
						layout: "column",
						defaults: {border: false},
						items: [{
								layout: "form",
								border: false,
								labelWidth: 150,
								items: [cmbtipocompromiso]
								}]
                                            }]
					},comcampocatcomprobante.fieldsetCatalogo,
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:80px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype:"datefield",
									fieldLabel:"Fecha de Cierre",
									allowBlank:true,
									labelSeparator :'',
									width:100,
									id:"feccie",
									format: 'd/m/Y',
									value : obtenerFechaActual(),
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					}]
	});

function irBuscar( )
{
    var numcom   = Ext.getCmp('comprobante').getValue();
    var sistema  = Ext.getCmp('sistema').getValue();

    if ((numcom!=='') && ((sistema!=='')))
    {
        gridComprCauParcialmente.store.removeAll();
            obtenerMensaje('procesar','','Buscando Datos');
            //buscar modificaciones a aprobar

            var JSONObject = {
                            'operacion' : 'buscar_cierre_compromiso',
                            'numcom'    : numcom,
                            'sistema'   : sistema
            }

            var ObjSon = JSON.stringify(JSONObject);
            var parametros = 'ObjSon='+ObjSon; 
            Ext.Ajax.request({
                    url : '../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
                    params : parametros,
                    method: 'POST',
                    success: function ( resultado, request)
                    {
                            Ext.Msg.hide();
                            var datos = resultado.responseText;
                            var objetoMov = eval('(' + datos + ')');
                            if(objetoMov!='')
                            {
                                    if(objetoMov!='0')
                                    {
                                            if(objetoMov.raiz == null || objetoMov.raiz =='')
                                            {
                                                    Ext.MessageBox.show({
                                                            title:'Advertencia',
                                                            msg:'No existen datos para mostrar',
                                                            buttons: Ext.Msg.OK,
                                                            icon: Ext.MessageBox.WARNING
                                                    });
                                            }
                                            else
                                            {
                                                    gridComprCauParcialmente.store.loadData(objetoMov);
                                            }
                                    }
                                    else
                                    {
                                            Ext.MessageBox.show({
                                                    title:'Advertencia',
                                                    msg:'Error al buscar datos',
                                                    buttons: Ext.Msg.OK,
                                                    icon: Ext.MessageBox.WARNING
                                            });
                                    }
                            }
                    },
                    failure: function (result,request) 
                    { 
                            Ext.MessageBox.alert('Error', 'Error de comunicacion con el Servidor'); 
                    }	
            });
    }
    else
    {
        Ext.MessageBox.alert('Error', 'Debe Seleccionar Tipo de Compromiso y Compromiso'); 
    }
}

function irCancelar()
{
	limpiarFormulario(fromContabilzarSPG);
	gridComprCauParcialmente.store.removeAll();
}

function irLimpiar()
{
	Ext.getCmp('comprobante').setValue('');
	gridComprCauParcialmente.store.removeAll();
}
function irProcesar()
{
	valido=true;
	var feccie = Ext.getCmp('feccie').getValue().format('Y/m/d');
	var cadenaJson = "{'operacion': 'contabilizar_cierre_compromisos', 'codsis':'"+sistema+"','nomven':'"+vista+"', 'feccie': '"+feccie+"', 'arrDetalle':[";				
	var arrComprobantes = gridComprCauParcialmente.getSelectionModel().getSelections();
	var total = arrComprobantes.length;
	if (total>0)
	{		
            dataComprobantes = gridComprCauParcialmente.store.getModifiedRecords();
            var numComprobante = dataComprobantes.length;
            cuentaserr='';
            for(i=0;((i<=numComprobante-1));i++)
            {
                cuentamodificado = dataComprobantes[i].get('spg_cuenta');
                existe=false;
                for (j=0; ((j < total)&&(!existe)); j++)
                {
                    cuenta=arrComprobantes[j].get('spg_cuenta');
                    if(cuentamodificado==cuenta)
                    {
                        existe=true;
                    }
                }
                if(!existe)
                {
                    cuentaserr = cuentaserr + ', '+cuentamodificado;
                }
            }
            if (cuentaserr!='')
            {
                function respuesta(btn)
                {
                    if(btn=='yes')
                    {
                         continuar();
                         valido=true;
                    }
                    else
                    {
                        valido=false;
                    }
                }
                Ext.MessageBox.confirm('Confirmar', 'La(s) cuenta(s) '+cuentaserr+' esta(n) modificada(s), pero no tildada(s). Desea continuar', respuesta);          
            }
            else
            {
                continuar();
                valido=true;
            }
            function continuar()
            {
                for (i=0; ((i < total)&&(valido)); i++)
                {
                    var montodisponible = parseFloat(ue_formato_operaciones(arrComprobantes[i].get('disponible')));
                    var montocierre = parseFloat(ue_formato_operaciones(arrComprobantes[i].get('montocierre')));
                    if (montodisponible>=montocierre)
                    {
                        if(montocierre!=0)
                        {
                            if(i>0)
                            {
                                    cadenaJson +=",";
                            }
                            cadenaJson +="{'comprobante':'"+arrComprobantes[i].get('comprobante')+"','codban':'"+arrComprobantes[i].get('codban')+"'," +
                                         " 'ctaban':'"+arrComprobantes[i].get('ctaban')+"','procede':'"+arrComprobantes[i].get('procede')+"'," +
                                         " 'spg_cuenta':'"+arrComprobantes[i].get('spg_cuenta')+"','procede_doc':'"+arrComprobantes[i].get('procede_doc')+"'," +
                                         " 'documento':'"+arrComprobantes[i].get('documento')+"','operacion':'"+arrComprobantes[i].get('operacion')+"'," +
                                         " 'codfuefin':'"+arrComprobantes[i].get('codfuefin')+"','codestpro1':'"+arrComprobantes[i].get('codestpro1')+"'," +
                                         " 'codestpro2':'"+arrComprobantes[i].get('codestpro2')+"','codestpro3':'"+arrComprobantes[i].get('codestpro3')+"'," +
                                         " 'codestpro4':'"+arrComprobantes[i].get('codestpro4')+"','codestpro5':'"+arrComprobantes[i].get('codestpro5')+"'," +
                                         " 'estcla':'"+arrComprobantes[i].get('estcla')+"','descripcion':'"+arrComprobantes[i].get('descripcion')+"'," +
                                         " 'monto':'"+montocierre+"','codcencos':'"+arrComprobantes[i].get('codcencos')+"'}";
                        }
                    }
                    else
                    {
                        Ext.MessageBox.alert('Error', 'Favor verifique el monto '+montocierre+' es mayor al disponible por causar.'); 
                        valido=false;
                    }
                }
                if(valido)
                {
                    obtenerMensaje('procesar','','Procesando Datos');
                    cadenaJson = cadenaJson + ']}';
                    var objdata= eval('(' + cadenaJson + ')');	
                    objdata=JSON.stringify(objdata);
                    var parametros = 'ObjSon='+objdata; 
                    Ext.Ajax.request({
                            url : '../../controlador/mis/sigesp_ctr_mis_integracionspg.php',
                            params : parametros,
                            method: 'POST',
                            success: function (resultado, request)
                            { 
                                    var resultado = resultado.responseText;
                                    var arrResultado = resultado.split("|");
                                    Ext.Msg.hide();
                                    //creando componente detalle comprobante
                                    var comResultado = new com.sigesp.vista.comResultadoIntegrador({
                                            tituloVentana: 'Resultado Contabilizaci&#243;n de Cierre de Compromisos',
                                            anchoLabel: 200,
                                            labelTotal:'Total Cierres procesados',
                                            valorTotal: arrResultado[0],
                                            labelProcesada:'Total Cierres contabilizados',
                                            valorProcesada:arrResultado[1],
                                            labelError:'Total Cierres con error',
                                            valorError:arrResultado[2],
                                            tituloGrid:'Detalle de Resultados',
                                            dataDetalle:arrResultado[3]
                                    });
                                    //fin creando componente detalle comprobante

                                    comResultado.mostrarVentana();
                                    irCancelar();
                            },
                            failure: function (resultado,request) 
                            { 
                                    Ext.Msg.hide();
                                    Ext.MessageBox.alert('Error', 'Error al procesar la Informacion'); 
                            }					
                    });
                }
            }
	}
	else
	{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe seleccionar al menos una cuenta a disminuir.',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}