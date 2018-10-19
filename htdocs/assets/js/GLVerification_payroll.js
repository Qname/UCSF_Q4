var GLVerification_payroll_Management = function () {
    var self = this;
    var isAjaxing = false;

    $(document).ready(function(){
        /***** DEPTID: finishes typing instead of on key up *****/
        var typingTimer = 0;                
        var doneTypingInterval = 1500;
        var $input = $('#txtPayrollFilter');

        /***** CONTROL POINT: will change Rollup *****/
        $('select#drPayrollFilter').on('change', function() {   
             $('#inputFilter').show();      
                $input.val("");           
            if($("#drPayrollFilter").val() == "EmpChanged"){
                  $('#inputFilter').hide();  
                self.glverification_payroll_expense_table("",$("#drPayrollFilter").val(),"Chg");
            }
        });

        /***** CONTROL POINT: will change Rollup *****/ 
        //on keydown, prevent enter and space
        $input.on('keydown', function (event) {
            if(event.keyCode == 13 ||event.keyCode == 32) {
                event.preventDefault();
                return false;
            }
        });
        
        //on paste, start countdown
        $input.on('paste', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(self.doneTyping, doneTypingInterval);
        }); 
        
        //on keyup, start the countdown
        $input.on('keyup', function () {       
            clearTimeout(typingTimer);
            typingTimer = setTimeout(self.doneTyping, doneTypingInterval);
        });

        self.doneTyping = function () {
               
                self.glverification_payroll_expense_table("",$("#drPayrollFilter").val(),$("#txtPayrollFilter").val());
            }

        $("#btnSubmitVerifyPayroll").click(function(){
            // $("#loadingModal").modal('toggle');
            var table = $('#dt_payroll_verification').DataTable();
            var data = table
            .rows()
            .data();
            
            var listData = [];
            for (var index = 0; index < data.length; index++) {
                var sta = $("#status-" + data[index][0]).val().trim();
        //        var cmt = $("#cmt-" + data[index][0]).val().trim();
                if(data[index][6]==null) data[index][6]="";
                if (sta != data[index][5] ) {
                    var changeData = {};
                    changeData.Id = data[index][0];
                    changeData.Status = sta;
       //             changeData.Comment = cmt;
                    listData.push(changeData);
                }
            }
            self.submit_glv_payroll(listData);
        });
    });

    self.submit_glv_payroll = function(data) {
        $.ajax({ url: base_url + '/glverification/submit_data_glv_payroll',
            data: {data: data},
            type: 'POST',
            dataType: "json",
            success: function(data) {
                if (data != "Data no change.") {
                    var glv_management = new GLVerificationManagement();
                    glv_management.Submit_filterGLV(function(){
                        glv_management.RefreshTable("#dt_payroll_verification");
                    });
                }
                alert(data);
                    // $("#loadingModal").modal('toggle');
                },
                error: function (request, status, error) {
                    alert("Error!");
                    // $("#loadingModal").modal('toggle');
                }
            });
    }

    self.month_to_number = [
    {'name' : 'Jan', 'value' : '1'},
    {'name' : 'Feb', 'value' : '2'},
    {'name' : 'Mar', 'value' : '3'},
    {'name' : 'Apr', 'value' : '4'},
    {'name' : 'May', 'value' : '5'},
    {'name' : 'Jun', 'value' : '6'},
    {'name' : 'Jul', 'value' : '7'},
    {'name' : 'Aug', 'value' : '8'},
    {'name' : 'Sep', 'value' : '9'},
    {'name' : 'Oct', 'value' : '10'},
    {'name' : 'Nov', 'value' : '11'},
    {'name' : 'Dec', 'value' : '12'},
    ];

    self.glverification_payroll_table = function(){
        $("#drPayrollFilter").val("PositionTitleCategory");
         $('#inputFilter').show();      
        $('#txtPayrollFilter').val("");   
        self.glverification_payroll_verify_table();
        self.glverification_payroll_FTE_table();
        self.glverification_payroll_expense_table("",$("#drPayrollFilter").val(),$("#txtPayrollFilter").val());
    };

    self.parseToCurrency  = function(num, decNum) {
        return "$"+parseFloat(num).toFixed(decNum).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
    };
    self.formatNumer  = function(num, decNum) {
        return parseFloat(num).toFixed(decNum).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
    };

    self.parseToPercent  = function(num) {
        if(num == 1) 
            return parseFloat(num * 100) + "%";
        return parseFloat(num * 100).toFixed(2) + "%";
    };

    self.addSpaceEmpName = function(name) {
        return name? name.toString().replace(",", ", "):"";
    }
    //renew comment in comment pop-up
    self.renewComment = function(){
        $("#addCommentBtn").removeClass("display-none");
        $("#saveCommentBtn").attr('disabled','disabled');
        $("#saveCommentBtn").addClass("display-none");
        $("#cancelCommentBtn").addClass('display-none');
        $("#addEditCommentDiv").addClass('display-none');
    }
    //load table payroll verifycation
    self.glverification_payroll_verify_table = function(){
        var regYe = /\d+/;
        var regMo = /[a-zA-Z]+/g;
        var deptId = $("#drpdeptid").val();
        var bu = $("#drpbusunit").val();
        var ye = $("#reportdate").val().match(regYe);
        var moText = $("#reportdate").val().match(regMo);
        var mo = "";
        var myfilter = $("#drpFilters option:selected").val();
        self.month_to_number.forEach(function(element) {
          if(element.name == moText[0]) {mo  = element.value;}
      }, this);

        var listReconStatus;
        $.ajax({ url: base_url + '/glverification/get_recon_status',
            type: 'POST',
            dataType: "json",
            success: function(data) {
                listReconStatus = data;
            },
            error: function (request, status, error) {
            }
        });

       // $('#dt_payroll_verification').DataTable().clear().destroy();
        $('#dt_payroll_verification').DataTable().destroy();
        $('#dt_payroll_verification').DataTable({
           
            "infoEmpty": "No records available",
            "fixedHeader": true,
            "scrollX": '100%',
            "scrollY": '50vh',
            "scrollCollapse": true,
            "processing": true,
            "serverSide": true,
            "paging": false,
            "oLanguage": {
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ items",
                "sZeroRecords" : "No data available in table"
            },
             "ajax": {
                "url": base_url + '/glverification/verify_payroll',
                "data": {deptId: deptId, businessUnit: bu, year: ye[0], month: mo,myfilter: myfilter },
                "type": "POST",
                "error":function(err,xhr){
                    alert('There was a problem loading payroll GL Verification items. Please try reloading the page.');
                }            
            },
            //"pageLength": 10,
            //"sPaginationType": "full_numbers",
            "ordering": false,
            "searching": false,
            "bInfo" : false,
            "columnDefs": [
            { "targets": [ 0 ], "visible": false, "searchable": false},
            { "targets": [ 1 ], "class": "col110" },
            { "targets": [ 2 ], "class": "dt-link text-center col70", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).on('click', function(e) {
                    $("#txtPayrollFilter").val('');
                    self.glverification_payroll_expense_table(oData[3],$("#drPayrollFilter").val(),$("#txtPayrollFilter").val());
                    $('html, body').animate({
                        scrollTop: $("#dt_payroll_expense_detail").offset().top
                    }, 500);
                });
            } },
            { "targets": [ 3 ], "class": "dt-link  col130", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html(self.addSpaceEmpName(sData));
                $(nTd).on('click', function(e) {
                    $("#txtPayrollFilter").val('');                    
                    self.glverification_payroll_expense_table(sData,$("#drPayrollFilter").val(),$("#txtPayrollFilter").val());
                    $('html, body').animate({
                        scrollTop: $("#dt_payroll_expense_detail").offset().top
                    }, 500);
                });
            } },
            { "targets": [ 4 ], "class": "col50", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html('<div class=" text-right">' +self.parseToCurrency(sData,2)+  '</div>');
            } },            
            { "targets": [ 5], "class": "col50", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
              var ddl = "<select aria-labelledby='lblhidden_status' id='status-"+oData[0]+"' >";
              ddl = ddl + "<option value='0' "+(sData==0?'selected=selected':'')+">Not Verified</option>";
              ddl = ddl + "<option value='1000' "+(sData==1000?'selected=selected':'')+">Pending</option>";
              ddl = ddl + "<option value='3000' "+(sData==3000?'selected=selected':'')+">Complete</option>";
              ddl += "</select>";
              $(nTd).empty();
              $(nTd).prepend(ddl);
          } },
          
          { "targets": [ 6 ], "class": "col40 text-center", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            var IconId = "commentIcon_"+oData[0].toString().trim();
            $(nTd).attr('data-uniqueId', oData[0]);
          
            $("#uniqueId").val(oData[0]);
            if (sData == null || sData == ""){
               
                $(nTd).html('<i class=\"glyphicon glyphicon-plus-sign gi-2x cursor-pointer \" id=\"'+IconId+'\" ></i>');
            } else{
                $(nTd).html('<i class=\"glyphicon glyphicon-comment gi-2x cursor-pointer \" id=\"'+IconId+'\" ></i>');
            }
            $(nTd).on('click', function (e) {
                $("#comment_Type").val("Payroll");
                $("#comment_glvtype").val(oData[6]);    
                $("#ModalGLVComments").modal({backdrop: 'static', keyboard: false});
                $("#ModalGLVComments").removeData();
                $('#dt_glvcomments').DataTable().clear().destroy();
                $("#addCommentBtn").html("Add Comment");
                $("#uniqueIdComment").html("[" + oData[3] + "]");
                $("#uniqueId").val(oData[0]);
                $("#current_comment").val('');
                self.renewComment();
                self.loadCommentDataTable();
            });
        } },
        { "targets": [ 7 ], "class": "col80 text-center", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            //column of document
            $(nTd).attr('data-document_uniqueid', oData[0]);
            $(nTd).attr('data-document_glvtypeid', sData); 
            $(nTd).attr('data-document_jnrlinedescription',oData[3]);// save employee name here
            $(nTd).on('click', function (e) {
                var document_uniqueid = $(this).data('document_uniqueid');
                $("#document_glvtypeid").val($(this).data('document_glvtypeid'));
                $("#document_uniqueid").val(document_uniqueid);
                $("#jnrlinedescription").html($(this).data('document_jnrlinedescription'));
                $("#upload_glvType").val('Payroll');
                //reset current display
                $('#msg_upload').html(""); // display success response from the server
                $('#files').val('');
                $("#ModalUploadDocument").modal({backdrop: 'static', keyboard: false});
                $("#ModalUploadDocument").removeData();
                $("#upload_file").attr('disabled','disabled');
                self.loadListFiles( $("#upload_glvType").val(), $("#document_uniqueid").val(), $("#document_glvtypeid").val());
              
            });
            if (sData == null || sData == ""){
                $(nTd).empty();
                var uploadBt = $('<button aria-label=\"btn-upload\" type=\"button\" style=\"border:none;background-color:transparent;\"><i class=\"glyphicon glyphicon-upload gi-2x  \" id="uploadBt_'+oData[0]+'"  ></i></button>');
                $(nTd).prepend(uploadBt);
            } else{
                $(nTd).empty();
                var uploadBt = $('<button aria-label=\"btn-attachment\"  type=\"button\" style=\"border:none;background-color:transparent;\"><i class=\"glyphicon glyphicon-paperclip gi-2x  \" id="uploadBt_'+oData[0]+'"  ></i></button>');
                $(nTd).prepend(uploadBt);
            }
            } 
        },
        { "targets": [ 8, 9, 10, 15 ], "class": "", },
        { "targets": [ 11] , "class": "col50 text-center " },
        { "targets": [ 12 ], "class": "col40 text-center " },
        { "targets": [ 13 ], "class": "col50 text-center "},
        { "targets": [ 14 ], "class": "col300 " },
        { "targets": [ 16 ], "class": "col150 ", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if (sData != "" && sData != null) {
                       // var date = new Date(sData).toISOString().split(".")[0].replace("T", " ");
                       
                       $(nTd).html(sData.split(".")[0]);
                       
                       // $(nTd).html(date.getFullYear()+'-'+(date.getMonth() + 1) + '-' + date.getDate() +' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds());
                   }
                   else
                    $(nTd).html("");
            } }
            ],
            "order": [[1, 'asc']],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                    i : 0;
                };
                // Total over this page
                pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );

                // Update footer
                $( api.column( 6 ).footer() ).html(
                    '<div class="text-left"> ' +self.parseToCurrency(pageTotal,2)+  '</div>'
                    );
            }
        });
	$('#dt_payroll_verification_wrapper thead th').removeClass('dt-link');
};
	
 //load comment table
     //load table payroll verifycation
     self.loadCommentDataTable = function(){
        var comment_glvtype = $("#comment_glvtype").val();

        $('#dt_glvcomments').DataTable().clear().destroy();
        $('#dt_glvcomments').DataTable({
            "infoEmpty": "No comments available.",
            "fixedHeader": true,
            "scrollX": '100%',
            "scrollY": '50vh',
            "scrollCollapse": true,
            "processing": true,
            "serverSide": true,
            "paging": false,
            "oLanguage": {
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ items",
                "sZeroRecords" : "No comments available."
            },
            
            //"pageLength": 10,
            //"sPaginationType": "full_numbers",
            "ordering": false,
            "searching": false,
            "bInfo" : false,
            "ajax": {
                "url": base_url + '/glverification/getComments',
                "data": {comment_glvtype: comment_glvtype},
                "type": "POST",
                "error":function(err,xhr){
                    alert('There was a problem loading comments. Please try reloading the page.');
                }
            },
            "columnDefs": [  
                { "targets": [ 0 ], "class":"col50 size-12", "visible": false},
                { "targets": [ 1 ], "class": "col50 size-12", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) 
                    {
                        if(oData[4] !=null && oData[4] == $("#hidden_userid").html().toLowerCase()){
                            
                             if($("#comment_Type").val() != "MonthlyTrend"){
                                $(nTd).empty();
                                $(nTd).closest("td").addClass("cursor-pointer");
                                $(nTd).html("<span class='cursor-pointer'>"+sData+"</span>");  
                                $(nTd).on('click', function (e) {
                                    $("#addEditCommentDiv").removeClass('display-none');
                                    $("#currentCommentDiv").html("Edit Comment");
                                    $("#addCommentBtn").addClass("display-none");
                                    $("#saveCommentBtn").removeClass("display-none");
                                    $("#saveCommentBtn").html("Save");
                                    $("#saveCommentBtn").attr("disabled", "disabled");
                                    $("#cancelCommentBtn").removeClass("display-none");
                                    $("#current_comment").val(sData);
                                    $("#current_comment").focus();
                                    $("#currentCommentId").val(oData[0]);
                                });
                            }
                        }
                    } 
                },
                { "targets": [ 2 ], "class": "col50 size-12", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) 
                    {
                        $(nTd).html(sData.split(".")[0]);
                    }
                },
                { "targets": [ 3 ], "class":"col50 size-12"},                    
                { "targets": [ 4 ], "class":"col50", "visible": false},
            ]
        });    
        //}
      
    }
    //enable button save on change comment input
    $("#current_comment").on("input", function(e) {
        if( $("#current_comment").val().trim()!= ''){
            $("#saveCommentBtn").removeAttr("disabled");
        } else{
            $("#saveCommentBtn").attr("disabled",'disabled');
        }
      });
     //load table payroll verifycation
     self.loadListFiles = function(glvType, document_uniqueid,document_glvtypeid){
        $('#dt_attchments').DataTable().clear().destroy();
        $('#dt_attchments').DataTable({
                "infoEmpty": "No attachments available.",
                "fixedHeader": true,
                "scrollX": '100%',
                "scrollY": '50vh',
                "scrollCollapse": true,
                "processing": true,
                "serverSide": true,
                "paging": false,
                "oLanguage": {
                    "sInfo": "Showing _START_ to _END_ of _TOTAL_ items",
                    "sZeroRecords" : "No attachments available."
                },
                
                //"pageLength": 10,
                //"sPaginationType": "full_numbers",
                "ordering": false,
                "searching": false,
                "bInfo" : false,
                "ajax": {
                    "url": base_url + 'glverification/getListDocuments',
                    "data": { uniqueid: document_uniqueid, glvType: glvType,document_glvtypeid: document_glvtypeid },
                    "type": "POST",
                    "error":function(err,xhr){
                        alert('There was a problem loading files. Please try reloading the page.');
                    }
                },
                "drawCallback" : function(oSettings) {
                    if(oSettings.json.data.length ==  0){
                        $("#document_glvtypeid").val('');
                    }
                },
                "columnDefs": [  
                    { "targets": [ 0 ], "class": "col120", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                            var url = './uploaddocuments/'+  $('#upload_glvType').val()+"/"+$("#document_uniqueid").val()+"/"+encodeURI(sData);
                            $(nTd).html("<a href='"+url+"' target='_blank'>"+decodeURI(sData)+"</a>");
                        } 
                    },
                    { "targets": [ 1 ], "class": "col50 text-center", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                       
                        $(nTd).attr('data-documentid', sData); 
                        $(nTd).attr('data-documentname', oData[0]); 
                        $(nTd).html("<button aria-label=\"btn-rm-attachment\" style=\"border:none;background-color:transparent;\" ><span class='glyphicon glyphicon-trash gi-2x' ></span></button>");     
                        $(nTd).on('click', function (e) {
                            $("#delete_documentid").val($(this).data('documentid'));
                            $("#delete_documentname").html(decodeURI($(this).data('documentname'))); 
                            $("#delete_document_glvtypeid").val($("#document_glvtypeid").val());
                            $("#modal_confirm_delete_file").modal('show');
                            $("#btn-yes-delete-file").removeAttr('disabled');
                        });
                         
                        }
                    }    
                ]
            });    
      
    }
     //on click confirm delete current document
    $("#btn-yes-delete-file").on('click',function(){
        $("#btn-yes-delete-file").attr('disabled','disabled');
        self.deleteDocument($("#delete_documentid").val(), $("#delete_document_glvtypeid").val(), $("#upload_glvType").val(),$("#document_uniqueid").val(),$("#delete_documentname").html());
    }) ; 
    self.deleteDocument = function(documentid,document_glvtypeid, glvtype, document_uniqueid,documentname){
        $.ajax({ url: base_url + '/glverification/deleteDocument',
        data: {documentid: documentid, document_glvtypeid: document_glvtypeid, glvtype: glvtype, uniqueid: document_uniqueid, documentname: documentname},
        type: 'POST',
        dataType: "json",
        success: function(data) {
            HideBusy();
            $("#modal_confirm_delete_file").modal('hide');
            $("#msg_upload").removeClass("errText");
            $("#msg_upload").removeClass("display-none");
            $("#msg_upload").html('File "'+ $("#delete_documentname").html() +'" has been deleted.');
            $('#dt_attchments').DataTable().ajax.reload();
            setTimeout(function(){
                $("#msg_upload").addClass("display-none");
            },6000);
        },
        error: function (request, status, error) {
            $("#modal_confirm_delete_file").modal('hide');
            $("#msg_upload").removeClass("display-none");
            $("#msg_upload").html('Failed to delete file "'+ $("#delete_documentname").html()+'"');
            $("#msg_upload").addClass("errText");
            setTimeout(function(){
                $("#msg_upload").addClass("display-none");
            },6000);
            HideBusy();
        }
    });
    };

     //enable or disble upload button
     $('#files').on('change',function(){
        if($('#files').val().toString().trim()!=''){
            $("#upload_file").removeAttr('disabled');
        } else{
            $("#upload_file").att('disabled','disabled');
        } 
    });         
    
    //event on close button upload popup
    $("#closeUploadBtn").on('click',function(){
        if($("#upload_glvType").val() == "Transaction"){
            $('#dt_verifyglvitems').DataTable().ajax.reload(null, false);
        } else{//payroll upload
            $('#dt_payroll_verification').DataTable().ajax.reload(null, false);
        }
       
   });
    //Upload ajax
    $('#upload_file').click(function() {
        $('#upload_file').attr('disabled','disabled');
        var files = $('#files').prop('files');
        if(files.length >= 1){
            var form_data = new FormData();
            form_data.append('uniqueid',  $("#document_uniqueid").val());
            form_data.append('glvType',  $("#upload_glvType").val());
            form_data.append('document_glvtypeid', $("#document_glvtypeid").val());
            for(var count = 0; count<files.length; count++)
            {
             var name = files[count].name;
             var extension = name.split('.').pop().toLowerCase();
             var error = '';
             var currentSizeupload = 0;
              $.ajax({
                  url: base_url + 'glvsetting/getSizeUpload',
                  async: false,
                  method: 'POST',
                  success: function (data) {
                    currentSizeupload = data;
                     form_data.append('currentSizeupload',currentSizeupload);
                  },
                  error: function (xhr) {
                    alert("Error");
                  }
                });
             if(jQuery.inArray(extension, ['xlsx','xls','doc','docx','pdf']) == -1)
             {
                error += "Please choose only xls, xlsx, doc, docx, pdf files.";
                $('#msg_upload').addClass('errText');
                $('#msg_upload').removeClass('display-none');
                $('#msg_upload').html('Please choose only xls, xlsx, doc, docx, pdf files.');
                $('#files').val('');
                setTimeout(function(){
                    $('#msg_upload').removeClass('errText');
                    $('#msg_upload').addClass('display-none');
                },6000);
                return;
             }
             else if(files[count].size > currentSizeupload*1048576){
                $('#msg_upload').addClass('errText');
                $('#msg_upload').removeClass('display-none');
                $('#msg_upload').html('File size must not exceed ' +currentSizeupload+'Mb.' );
              
                $('#files').val('');
                setTimeout(function(){
                    $('#msg_upload').removeClass('errText');
                    $('#msg_upload').addClass('display-none');
                },6000);
                return;
            }
             else
             {
                form_data.append("files[]", files[count]);
             }
            }
            if(error == '')
            {
                $.ajax({
                    url: base_url + 'glverification/uploadFiles', // point to server-side controller method
                    dataType: 'text', // what to expect back from the server
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if(IsJsonString(response)){
                            var res = JSON.parse(response);
                            if(res.status){ // if upload success
                                $('#msg_upload').removeClass('display-none errText');                     
                                $('#msg_upload').html(res.msg);
                                setTimeout(function(){
                                    $('#msg_upload').addClass('display-none ');
                                },5000);
                                $("#document_glvtypeid").val(res.document_glvtypeid);
                                glv_payroll.loadListFiles( $("#upload_glvType").val(),$("#document_uniqueid").val(),$("#document_glvtypeid").val());
                      
                            } else {
                                $('#msg_upload').removeClass('display-none');       
                                $('#msg_upload').addClass('errText');                
                                $('#msg_upload').html(res.msg);
                                setTimeout(function(){
                                    $('#msg_upload').addClass('display-none');
                                    $('#msg_upload').removeClass('errText');
                                },5000);
                            }
                        } else{ //internal error
                            $('#msg_upload').removeClass('display-none');       
                                $('#msg_upload').addClass('errText');                
                                $('#msg_upload').html(res.msg);
                                setTimeout(function(){
                                    $('#msg_upload').addClass('display-none');
                                    $('#msg_upload').removeClass('errText');
                            },5000);
                        }
                        $("#ModalUploadDocument").removeData();
                        $('#files').val('');
                        $('#upload_file').attr('disabled','disabled');
                       
                    },
                    error: function (response) {
                        var res = JSON.parse(response);
                        $('#msg_upload').addClass('errText');
                        $('#msg_upload').html(res.msg); 
                        setTimeout(function(){
                            $('#msg_upload').addClass('display-none');
                        },5000);// display error response from the server
                        $("#document_glvtypeid").val(res.document_glvtypeid);
                        $('#files').val('');
                    }
                });
            }
            else
            {
             alert(error);
            }
        }
       
    });
   

    $('#addCommentBtn').on('click', function(){
        $("#saveCommentBtn").attr("disabled", "disabled");
        $("#saveCommentBtn").removeClass('display-none');

        if($("#comment_Type").val() == "MonthlyTrend"){
            $("#saveCommentBtn").html('Submit');
        } else {
            $("#saveCommentBtn").html('Add Comment');  
        }
        $("#addCommentBtn").addClass('display-none');
        $("#cancelCommentBtn").removeClass('display-none');
        $("#addEditCommentDiv").removeClass('display-none');
        $("#currentCommentDiv").html("Add Comment");
        $("#current_comment").val("");
        $("#current_comment").focus();
    });

    $("#saveCommentBtn").on("click",function(){
        if(  $("#saveCommentBtn").html()=="Add Comment" || $("#saveCommentBtn").html()=="Submit")
        {
            var comment_glvtype_Id =  $("#comment_glvtype").val();
            if(comment_glvtype_Id !="" && comment_glvtype_Id != null){ // add to current list comment
                var comment =  $("#current_comment").val();
                ShowBusy();
                $.ajax({ url: base_url + '/glverification/addAdditionalComments',
                    data: {comment: comment, comment_glvType_Id: comment_glvtype_Id},
                    type: 'POST',
                    dataType: "json",
                    success: function(data) {
                        HideBusy();
                        if (data) {
                            self.renewComment();
                            self.loadCommentDataTable();
                            $("#comment_result").html("<span style='color:#000;'>Comment has been added successfully.</span>");
                            $("#comment_result").show('fast');
                            setTimeout( function() {
                                $("#comment_result").hide('slow');
                            }, 6000);
                        }
                        else {
                            self.renewComment();
                            $("#comment_result").html("<span style='color:red;'>Failed to add comment.</span>");
                            $("#comment_result").show('fast');
                            setTimeout( function() {
                                $("#comment_result").hide('slow');
                            }, 6000);
                        }
                    },
                    error: function (request, status, error) {
                        HideBusy();
                        self.renewComment();
                        $("#comment_result").html("<span style='color:red;'>Failed to add comment.</span>");
                        $("#comment_result").show('fast');
                        setTimeout( function() {
                            $("#comment_result").hide('slow');
                        }, 6000);
                    }
                });
              
            } else {// add new list comment 
                var comment =  $("#current_comment").val();
                var uniqueId =$("#uniqueId").val() ;
                ShowBusy();
                $.ajax({ url: base_url + '/glverification/addNewComment',
                    data: {comment: comment, uniqueId:uniqueId, commentType: $("#comment_Type").val() },
                    type: 'POST',
                    dataType: "json",
                    success: function(data) {
                        HideBusy();     
                        if(data != null){
                            $("#comment_glvtype").val(data);
                            self.renewComment();
                            self.loadCommentDataTable();
                            $("#comment_result").html("<span style='color:#000;'>Comment has been added successfully.</span>");
                            $("#comment_result").show('fast');
                            setTimeout( function() {
                                $("#comment_result").hide('slow');
                            }, 6000);
                        }  else{
                            self.renewComment();
                            $("#comment_result").html("<span style='color:red;'>Failed to add comment.</span>");
                            $("#comment_result").show('fast');
                            setTimeout( function() {
                                $("#comment_result").hide('slow');
                            }, 6000);
                        }                
                       
                    },
                    error: function (request, status, error) {
                        self.renewComment();
                        HideBusy();
                        $("#comment_result").html("<span style='color:red;'>Failed to add comment.</span>");
                        $("#comment_result").show('fast');
                        setTimeout( function() {
                            $("#comment_result").hide('slow');
                        }, 6000);
                    }
                });
            }
          
        }
        else if(  $("#saveCommentBtn").html()=="Save"){ //saved editted comment
            var currentCommentId =  $("#currentCommentId").val();
            var comment =  $("#current_comment").val();
            ShowBusy();
            $.ajax({ url: base_url + '/glverification/updateComments',
                data: {comment: comment, commentId: currentCommentId},
                type: 'POST',
                dataType: "json",
                success: function(data) {
                    HideBusy();
                    if (data) {

                        if($("#comment_Type").val() == "MonthlyTrend"){
                            $("#saveCommentBtn").html('Submit');
                        } else {
                            $("#saveCommentBtn").html('Add Comment');  
                        }
                       // $("#saveCommentBtn").html("Add Comment");
                        self.renewComment();
                        self.loadCommentDataTable();
                        $("#comment_result").html("<span style='color:#000;'>Comment has been saved.</span>");
                        $("#comment_result").show('fast');
                        setTimeout( function() {
                            $("#comment_result").hide('slow');
                        }, 6000);   
                    }
                    else {
                        self.renewComment();
                        $("#comment_result").html("<span style='color:red;'>Failed to save comment.</span>");
                        $("#comment_result").show('fast');
                        setTimeout( function() {
                            $("#comment_result").hide('slow');
                        }, 6000);
                    }                
                },
                error: function (request, status, error) {

                    if($("#comment_Type").val() == "MonthlyTrend"){
                        $("#saveCommentBtn").html('Submit');
                    } else {
                        $("#saveCommentBtn").html('Add Comment');  
                    }
                 //   $("#saveCommentBtn").html("Add Comment");
                    $("#saveCommentBtn").removeAttr("disabled");
                    $("#cancelCommentBtn").addClass('display-none');
                    $("#addEditCommentDiv").addClass('display-none');
                    HideBusy();
                    $("#comment_result").html("<span style='color:red;'>Failed to save comment.</span>");
                    $("#comment_result").show('fast');
                    setTimeout( function() {
                        $("#comment_result").hide('slow');
                    }, 6000);                    
                }
            });
        }
       
    });
	
	
    //generate list name header follow report date
    self.generate_name_follow_report_date = function(time) {
        var arrMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var ye = time.match(/\d+/)[0];
        var mon = time.match(/\D+/)[0];
        var mon1 = "Nov";

        var indexMon = arrMonth.indexOf(mon.trim());
        var rs = new Array();
        for (var i = indexMon; i >= 0; i--) {
            rs.push(arrMonth[i] + " " +  ye);
        }
        for (var j = (arrMonth.length - 1); j > indexMon; j--) {
         rs.push(arrMonth[j]  + " " +  (ye - 1));
     }

     return rs;
 };

    //load table payroll FTE & Salary
    self.glverification_payroll_FTE_table = function(){
        var time = $("#reportdate").val();
        var ye = time.match(/\d+/)[0];

        var listHeader = self.generate_name_follow_report_date(time);
        if (listHeader != null) {
            for (var index = 0; index < listHeader.length; index++) {
                $("#fte_" + (index + 1)).text(listHeader[index] + " FTE");
                $("#sal_" + (index + 1)).text(listHeader[index] + " SAL");
            }
        }

        $('#dt_payroll_fte').DataTable().clear().destroy();
        table = $('#dt_payroll_fte').DataTable({
            "infoEmpty": "No records available",
            "processing": true,
            "serverSide": true,
            "fixedHeader": true,
            "scrollX": '100%',
            "scrollY": '50vh',
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "searching": false,
            "bInfo" : false,
            "oLanguage": {
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ items",
                "sZeroRecords" : "No data available in table"
            },
            "ajax": {
                "url": base_url + '/glverification/payroll_fte',
                "data": {year: ye },
                "type": "POST",
                "error":function(err,xhr){
                    alert('There was a problem loading payroll FTE & Salary items. Please try reloading the page.');
                }
            },
            "columnDefs": [
            { "targets": [ 0 ], "class": "col110 ", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
              if (sData == "Total") 
                  $(nTd).parent().css('font-weight', 'bold');
              
          } },
          { "targets": [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 ], 
          "class": "col53 text-right ", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
              if (sData == 0) 
                  $(nTd).html("");
              else 
                  $(nTd).html(self.formatNumer(sData,0));
          } },
          { "targets": [ 12 ], 
          "class": "col53 text-right  dt-border-right", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
              if (sData == 0) 
                  $(nTd).html("");
              else 
                  $(nTd).html(self.formatNumer(sData,0));
          } },
          { "targets": [ 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 22, 23, 24 ], 
          "class": "col53 text-right ", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
              if (sData == 0) 
                  $(nTd).html("");
              else 
                  $(nTd).html(self.parseToCurrency(sData,0));
          } }
          ]
      });
    };

    //load table payroll Expense Detail and FTE Report
    self.glverification_payroll_expense_table = function(empName,search_col,search_val){
        var time = $("#reportdate").val();

        var listHeader = self.generate_name_follow_report_date(time);
        if (listHeader != null) {
            for (var index = 0; index < listHeader.length; index++) {
                $("#exp_" + (index + 1)).text(listHeader[index]);
            }
        }
         $('#exportPayroll').click(function() {
            if(isAjaxing) return;
            isAjaxing = true;
           ShowBusy();

            $.ajax({ url: base_url + '/glverification/export_payroll_expense',
                data: {
                    "emp_name": empName, 
                    "changedEmp":"false",
                    "listHeader" : listHeader
                     },
                type: 'POST',
                dataType: "json",
                success: function(data) {
                    HideBusy();
                },
                error: function (request, status, error) {
                    HideBusy();

                }
            }).done(function(data){
                isAjaxing = false;
                var $a = $("<a>");
                $a.attr("href",data.file);
                $("body").append($a);
                $a.attr("download","PayrollData"+$.now()+".xlsx");
                $a[0].click();
                $a.remove();
            });
        });

         $('#exportPayrollChanged').click(function() {
            if(isAjaxing) return;
            isAjaxing = true;
           ShowBusy();
            $.ajax({ url: base_url + '/glverification/export_payroll_expense',
                data: {
                    "emp_name": empName, 
                    "changedEmp":"true",
                    "listHeader" : listHeader
                },
                type: 'POST',
                dataType: "json",
                success: function(data) {
                    HideBusy();
                },
                error: function (request, status, error) {
                    HideBusy();
                }
            }).done(function(data){
                isAjaxing = false;                
                var $a = $("<a>");
                $a.attr("href",data.file);
                $("body").append($a);
                $a.attr("download","PayrollChangedData"+$.now()+".xlsx");
                $a[0].click();
                $a.remove();
            });
        });


        $('#dt_payroll_expense_detail').DataTable().clear().destroy();
        //datatables
        table = $('#dt_payroll_expense_detail').DataTable({
            "infoEmpty": "No records available",
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "fixedHeader": true,
            "scrollX":  '100%',
            "scrollY":  '100vh',
            "scrollCollapse": true,
            "searching": false,
            "paging": true,
            "oLanguage": {
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ items",
                "sZeroRecords" : "No data available in table"
            },
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            //"lengthMenu": [ [50, 100, 200], [50, 100, 200] ],
            "pageLength": 50,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": base_url + '/glverification/payroll_expense',
                "data": {emp_name: empName,search_col:search_col, search_val: search_val },
                "type": "POST",
                "error":function(err,xhr){
                    alert('There was a problem loading payroll Expense Detail and FTE Report items. Please try reloading the page.');
                    HideBusy();
                }
            },
            "createdRow": function( row, data, dataIndex ) {
                if ( data[3] == "Total" ) {
                    $(row).addClass('bg-grey');
                }
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            { "targets": [ 0 ], "class": "col120 ", "visible": false, "searchable": false },
            { "targets": [ 1 ], "class": "col40", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if(oData['3'] === 'zSpace') {
                    $(nTd).html('');
                }
            } },
            { "targets": [ 2 ], "class": "col100 ", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if(oData['3'] === 'zSpace') {
                    $(nTd).html('');
                }
                else {
                    $(nTd).html(self.addSpaceEmpName(sData));
                }
            } },
            { "targets": [ 3 ], "class": " text-center", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if(sData === 'zSpace') {
                    $(nTd).html('');
                }
            } },
            { "targets": [ 4 ], "class": "col50 text-center " },
            { "targets": [ 5 ], "class": "col50 text-center " },
            { "targets": [ 6, 7], "class": "" },
            { "targets": [ 8 ], "class": "text-center " },
            { "targets": [ 9 ], "class": "col40 text-center " },
            { "targets": [ 10 ], "class": "col50 text-center " },
            { "targets": [ 11 ], "class": "", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if (sData != "" && sData != null) 
                  $(nTd).addClass('red-color');
          } },
          { "targets": [ 12 ], "class": " col50 text-right", "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if(sData != 0 && sData != null)
                if (oData[4] == "FTE") 
                    $(nTd).html(self.parseToPercent(sData));
                else 
                    $(nTd).html(self.parseToCurrency(sData,2));
                else
                    $(nTd).html("");

                if (oData[11] != "" && oData[11] != null && sData != "" && sData != null) {
                  $(nTd).addClass('bg-red');
              }
          } },
          { "targets": [ 13, 14 ], "class": "col50  text-right", 
          "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if(sData != 0 && sData != null)
                if (oData[4] == "FTE") 
                    $(nTd).html(self.parseToPercent(sData));
                else 
                    $(nTd).html(self.parseToCurrency(sData,2));
                else
                    $(nTd).html("");
            } }
            ]
        });
    };
}