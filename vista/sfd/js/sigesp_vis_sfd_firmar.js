/***********************************************************************************
* @Proceso para Firmas Digitales.
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    //Registro para el combo de orientación de la  página
    var reOrientacion = [['Vertical','P'],
                        ['Horizontal','L'],
                    ];
    // Arreglo que contiene los tamaños que se pueden usar

    var seOr = new Ext.data.SimpleStore({
        fields: ['etiqueta', 'cod1'],
        data : reOrientacion // Se asocian la información de la pag
    });

   //Registro para el combo de tamaño de página
    var reTamanno = [ ['Carta','letter'],
                    ['Oficio','legal'],
                    ['A4','A4'],
                    ['A5','A5'],
                    ['A6','A6'],
                ];
    // Arreglo que contiene los tamaños que se pueden usar

    var seTam = new Ext.data.SimpleStore({
        fields: ['den', 'cod'],
        data : reTamanno // Se asocian la información de la pag
    });  

    var FirmaVisible = [['Si','si'],
                    ['No','no'],
    ];

    var SeFir = new Ext.data.SimpleStore({
        fields: ['resp', 'valor'],
        data : FirmaVisible // Se asocian la información de la pag
    });

    var panel      = '';
    var pantalla   = '';
    var actualizar = false;
    var rutaProceso  =  '../../controlador/sfd/sigesp_ctr_sfd_firmar.php';
    barraherramienta    = true;

    Ext.onReady(
    function()
    {
        Ext.QuickTips.init();
        Ext.Ajax.timeout=36000000000;
        Ext.form.Field.prototype.msgTarget = 'side';
                   
        //componentes del formulario
        Xpos = ((screen.width/2)-(450/2));
        Ypos = ((screen.height/2)-(450/2));
      
        panel = new Ext.FormPanel({
            title: 'Firma Digital',
            bodyStyle:'padding:5px 5px 0px',
            width:500,
            height:275,          
            tbar: [],
            style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',          
            url:'save-form.php',
            fileUpload: true,
            items:[{
                xtype:'fieldset',                            
                //autoHeight:true,
                autoWidth:true,
                cls :'fondo',
                width: 488,
                height:234,
                items:[
                {
                    frame:true,
                    xtype:'fieldset',
                    title: 'Firmar Documento',
                    bodyStyle:'padding:5px 5px 0',
                    id:'fsfirmar',
                    width: 430,
                    height: 209,
                    fileUpload: true,            
                    defaults: {width: 430},
                    labelWidth: 75,                  
                    tbar: [],
                    autoWidth:true,
                    items: [{
                        xtype: 'fileuploadfield',
                        fieldLabel: 'Archivo',
                        id: 'archivopdf',
                        labelSeparator:'',
                        allowBlank:false,
                        width: 330,
                        emptyText: 'Seleccione el archivo.pdf',
                        fileUpload: true,
                        buttonCfg:
                        {
                            text: '...'
                        }
                    },{
                        xtype: 'fileuploadfield',
                        fieldLabel: 'Certificado',
                        id: 'archivocrt',
                        labelSeparator:'',
                        allowBlank:false,
                        width: 330,
                        emptyText: 'Seleccione el certificado.crt',
                        fileUpload: true,
                        buttonCfg:
                        {
                            text: '...'
                        }
                    },{
                        xtype: 'fileuploadfield',
                        fieldLabel: 'Clave (.KEY)',
                        id: 'archivokey',
                        labelSeparator:'',
                        allowBlank:false,
                        width: 330,                      
                        emptyText: 'Seleccione la clavePrivada.key',
                        fileUpload: true,
                        buttonCfg:
                        {
                            text: '...'
                        }
                    },{
                        store: seOr,
                        xtype: 'combo',
                        labelSeparator: '',
                        fieldLabel: 'Orientaci&oacuten',
                        id: 'orientacion',                      
                        editable: false,
                        displayField:'etiqueta',
                        valueField:'cod1',                        
                        typeAhead: true,
                        allowBlank:true,
                        value:'Vertical',    
                        triggerAction: 'all',
                        mode: 'local',
                        listWidth: 100,
                        width: 100,
                    },{
                        store: seTam,
                        xtype: 'combo',
                        labelSeparator: '',
                        fieldLabel: 'Tama&ntildeo p&aacutegina',
                        id: 'tamanopagina',                      
                        editable: false,
                        displayField:'den',
                        valueField:'cod',
                        value:'Carta',
                        typeAhead: true,
                        allowBlank:false,
                        triggerAction: 'all',
                        mode: 'local',
                        listWidth: 100,
                        width: 100,
                    },{
                        store: SeFir,
                        xtype: 'combo',
                        labelSeparator: '',
                        fieldLabel: 'Firma Visible',
                        id: 'visibilidad',                      
                        editable: false,
                        displayField:'resp',
                        valueField:'valor',
                        value:'Si',
                        typeAhead: true,
                        allowBlank:false,
                        triggerAction: 'all',
                        mode: 'local',
                        listWidth: 100,
                        width: 100,
                    }                  
                    ]
                }]
            }]
        })
        panel.render(document.body);
    })
      
/***********************************************************************************
* @Función para limpiar los campos.
* @parametros:
* @retorno:
* @fecha de creación: 10/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/    
    function irCancelar()
    {
        panel.getForm().reset();               
    } 
 
/***********************************************************************************
* @Función para procesar el movimiento inicial de existencias de inventario.
* @parametros:
* @retorno:
* @fecha de creación: 01/08/2016
* @autor: Pasantes: Génesis Oropeza y Marco Gutiérrez.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
    function irProcesar()
    {
        var txtarc = Ext.getCmp('archivopdf').getValue();
        var txtcert = Ext.getCmp('archivokey').getValue();
        var txtclave = Ext.getCmp('archivocrt').getValue();
        var cmborientacion = Ext.getCmp('orientacion').getValue();
        var cmbotamano = Ext.getCmp('tamanopagina').getValue();
    
        if((txtarc!="") && (txtclave!="") && (txtcert!="")&&(cmborientacion !="")  )
        {
            panel.getForm().submit({
                url : "../../controlador/sfd/sigesp_ctr_sfd_firmar.php",
              //  waitMsg: 'Cargando el archivo...',
                success: function(fsfirmar, o)
                {
					Ext.Msg.show({
                        title:'Mensaje',
                        msg: 'Archivo cargado y firmado exitosamente',
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.INFO
                    });
                    panel.getForm().reset();                   
                },
                failure: function (fsfirmar, o)
                {
                    var data = Ext.decode(o.response.responseText);
                    Ext.Msg.show({
                        title:'Mensaje',
                        msg: data.errorMsg,
                        buttons: Ext.Msg.OK,
                        icon: Ext.MessageBox.ERROR
                    });                             
                }                               
            });
        }else
        {
            Ext.Msg.show({
                title:'Mensaje',
                msg: 'Debe seleccionar los archivos',
                buttons: Ext.Msg.OK,
                icon: Ext.MessageBox.ERROR
            });
        }                       
    }

/***********************************************************************************
* @Función para Descargar los archivos generados pór el módulo de apertura
* @parametros:
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/  
    function irDescargar()
    {
        objCatDescarga = new catalogoDescarga();
        objCatDescarga.mostrarCatalogo();
    }