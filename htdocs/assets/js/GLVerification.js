var glv_payroll = new GLVerification_payroll_Management();
var glv_MonthlyTrend = new  GLVerification_MonthlyTrend_Report();
var glv_filter = new  GLVerification_filter_management();
//GL Verification
var GLVerificationManagement = function () {
    var self = this;

    //function init
    self.init_glverification = function () {
        //load dashboard table
        self.glverification_dashboard_table();

        //event click submit filter
        $('#btnfilter_glverification').off('click');
        $('#btnfilter_glverification').on('click', function () {
            ShowBusy();
            self.Submit_filterGLV(function(){
                self.glverification_dashboard_table();
                $('.nav-tabs a[href="#hr0"]').tab('show');
		//reload DeptCd
        glv_MonthlyTrend.ReloadDeptId();
        HideBusy();
    });
        });

         
        //load drp reportdate
        var month_to_number = {
            'Jan' : '1',
            'Feb' : '2',
            'Mar' : '3',
            'Apr' : '4',
            'May' : '5',
            'Jun' : '6',
            'Jul' : '7',
            'Aug' : '8',
            'Sep' : '9',
            'Oct' : '10',
            'Nov' : '11',
            'Dec' : '12'
        };
        var month_to_name = {
            '1' : 'Jan',
            '2' : 'Feb',
            '3' : 'Mar',
            '4' : 'Apr',
            '5' : 'May',
            '6' : 'Jun',
            '7' : 'Jul',
            '8' : 'Aug',
            '9' : 'Sep',
            '10' : 'Oct',
            '11' : 'Nov',
            '12' : 'Dec'
        };

        $('.date-picker').datepicker( {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: "M yy",
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                //jQuery(this).datepicker('setDate', new Date(year, month, 1));
                //{dialog.object}.setValue('ReportDate',month_to_name[parseInt(month)+1]+" "+year);
                $('#reportdate').val(month_to_name[parseInt(month)+1]+" "+year);
                if(dateText!=this.value){
                    ShowBusy();
                 //on change report date refresh the dropdown DepCd
                 glv_MonthlyTrend.ReloadDeptIdBaseOnReportDates(year,Number(month)+1);
                //reset all data on GLVerification
                
                self.ResetData();
            }
            

        },
        beforeShow: function() {
            if ((datestr = $(this).val()).length > 0) {
                year = datestr.substring(datestr.length-4, datestr.length);
                month = month_to_number[datestr.substring(0, 3)];
                $(this).datepicker('option', 'defaultDate', new Date(year, month-1, 1));
                $(this).datepicker('setDate', new Date(year, month-1, 1));
            }
        },
        yearRange: ((new Date).getFullYear() - 10).toString()+":"+((new Date).getFullYear()+1).toString()
    });
        $('#btnshowreportdate').click(function() {
            $("#reportdate").focus();
            $(".ui-datepicker-calendar").hide();
        });
        
        
        
        
        //================

        //event click button month trend report on dashboard tabs
        $('#btnmonthtrendreport').click(function() {
            $('.nav-tabs a[href="#hr3"]').tab('show');
            ShowBusy();
            glv_MonthlyTrend.getUrlMonthlyTrend();
        });

        //event drp deptid on header filter change
        $("#drpdeptid").on('change', function() {
             //reset all data on GLVerification
             self.ResetData();
             // reset filter
             glv_filter.get_deptId('drpFilters');
         });
        
        ////event common click on tabs
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            if ((target == '#hr1')) {
                $("#rdoshowdollar").prop("checked", true)
            }
        });

        ////event click on tabs payroll load all payroll
        $('#tabsdashboard').click(function() {
            self.glverification_dashboard_table();
            self.GetAcknowledgerApprove();
        });

        ////event click on tabs transaction load all transaction
        $('#tabstransaction').click(function() {
            self.glverification_transactions_table();
        });

        ////event click on tabs payroll load all payroll
        $('#tabspayroll').click(function() {

            glv_payroll.glverification_payroll_table();
        });
        var previousValue =""; //store previous value of dropdownbox
        $('#monthlyTrendDrop').on('focus',function(){
            previousValue = $("#monthlyTrendDrop").val();
            localStorage.setItem('prevMonthlyTrenDropVal',previousValue);
        }).on("change",function(){
            $('#monthlyTrendDropBtn').prop('disabled', false);
            localStorage.setItem('currentMonthlyTrenDropVal',$("#monthlyTrendDrop").val());
        });
        $('#monthlyTrendDropBtn').on('click',function(){
            ShowBusy();
            glv_MonthlyTrend.onSaveDropdownMonthlyTrend();
            $('#monthlyTrendDropBtn').prop('disabled', true);
        })

        $('#tabsmonthtrend').click(function(){
            if($('#drpdeptid').val()){
                ShowBusy();
                glv_MonthlyTrend.getUrlMonthlyTrend();
            } else{
                document.getElementById('monthlyTrendUrl').src ="";
            }   
            self.checkExistedCommentForMonthlyTrendDataId();      
        });
        
        //event click on button Acknowledgement in tabs dashboard
        $('#btnacknowlege').off('click');
        $("#btnacknowlege").click(function(){
         var r = confirm("Are you sure?");
         if(r == true) {

             var checkValue = 0;
             if($('#btnacknowlege').text()!='Approved'){
                checkValue=1;
            }
            var form_data = {
                reportdate:$('#reportdate').val(),
                deptid:$("#drpdeptid option:selected").val(),
                checked: checkValue
            }
            $.ajax({
                url: base_url + 'glverification/Submit_AcknowledgedData',
                async: false,
                method: 'POST',
                data: form_data,
                success: function (data) {
                    var r1 = $.parseJSON(data);
                    if(r1.Code=="success" ){
                        if($('#btnacknowlege').text()!='Approved'){
                            $("#btnacknowlege").prop("disabled",true);
                            $("#btnacknowlege").html('Approved');
                            $("#btnacknowlege").removeClass();
                            $("#btnacknowlege").addClass("btn btn-ack-disable-blue");
                        } else {
                            $("#btnacknowlege").html('Approve and Submit');
                            $("#btnacknowlege").removeClass();
                            $("#btnacknowlege").addClass("btn btn-ack-enable-grey");
                        }
                        
                    }
                },
                error: function (xhr) {
                    alert("Error");
                    
                }
            });

              //reload DeptCd
              glv_MonthlyTrend.ReloadDeptId();
             // reload monthly trend
             self.getMonthlyTrendPercent();
             
         }
         
     });
	//on change business dropdown
    $("#drpbusunit").on('change',function(){
	    //reset all data on GLVerification
        self.ResetData();
    });
	
    
        ////load my filters
        glv_filter.get_deptId('drpFilters');
    }
    
    //on change my filter dropdown
    $("#drpFilters").on('change',function(){
        //reset all data on GLVerification
        self.ResetData();
    });

    //reset all Data on GLVerification
    self.ResetData = function(){
        $('#dt_glverification_dashboard').DataTable().clear().destroy();
        $('.nav-tabs a[href="#hr0"]').tab('show');
        $('#tabsdashboard').off('click');


        $('#tabsglv_transactions').removeClass('active').addClass('disabled');
        $('#tabsglv_transactions').find('a').removeAttr("data-toggle");
        $('#tabstransaction').off('click');

        $('#tabsglv_payroll').removeClass('active').addClass('disabled');
        $('#tabsglv_payroll').find('a').removeAttr("data-toggle");
        $('#tabspayroll').off('click');

        $('#tabsglv_monthtrendreport').removeClass('active').addClass('disabled');
        $('#tabsglv_monthtrendreport').find('a').removeAttr("data-toggle");
        $('#tabsmonthtrend').off('click');

        $('#btnmonthtrendreport').prop('disabled', true);
        $("#monthly_percentage").val("0%");
        $("#monthly_percentage_actual").css("width","0%");
        $("#monthly_percentage_remain").css("width","100%");
        document.getElementById('monthlyTrendUrl').src ="";   

        // disable acknowledge
        $("#btnacknowlege").prop("disabled",true);
        $("#btnacknowlege").html('Approve and Submit');
        $("#btnacknowlege").removeClass();
        $("#btnacknowlege").addClass("btn btn-ack-disable-grey");     
    }
    //event click submit filter data
    self.Submit_filterGLV =function(callback){
        ShowBusy();
        // turn on tabsdashboard click event
        $('#tabsdashboard').on('click', function () {
          self.glverification_dashboard_table();
          self.GetAcknowledgerApprove();
      });
        
        // turn on tabstransaction click event
        $('#tabstransaction').on('click', function () {
            self.glverification_transactions_table();
        });

        // turn on tabspayroll click event
        $('#tabspayroll').on('click', function () {
           glv_payroll.glverification_payroll_table();
       });

        // turn on tabsmonthtrend click event
        $('#tabsmonthtrend').on('click', function () {
            if($('#drpdeptid').val()){
                ShowBusy();
                glv_MonthlyTrend.getUrlMonthlyTrend();
            } else{
                document.getElementById('monthlyTrendUrl').src ="";
            } 
            self.checkExistedCommentForMonthlyTrendDataId(); 
        });

     
        var form_data = {
            reportdate:$('#reportdate').val(),
            deptid:$("#drpdeptid option:selected").val(),
            busunit:$("#drpbusunit option:selected").val(),
            myfilter: $("#drpFilters option:selected").val()
        }

        $.ajax({
            url: base_url + 'glverification/headerfilterglvdata_submit',
            //async: false,
            method: 'POST',
            data: form_data,
            success: function (data) {
                var r1 = $.parseJSON(data);

                $('#tabsglv_transactions').removeClass('disabled');
                $('#tabsglv_transactions').find('a').attr("data-toggle", "tab")

                $('#tabsglv_payroll').removeClass('disabled');
                $('#tabsglv_payroll').find('a').attr("data-toggle", "tab")

                $('#tabsglv_monthtrendreport').removeClass('disabled');
                $('#tabsglv_monthtrendreport').find('a').attr("data-toggle", "tab")

                $('#btnmonthtrendreport').prop('disabled', false);

                if (r1.approve=="Disable"){
                    $("#btnacknowlege").prop("disabled",true);
                    $("#btnacknowlege").html('Approve and Submit');
                    $("#btnacknowlege").removeClass();
                    $("#btnacknowlege").addClass("btn btn-ack-disable-grey");
                }

                if(r1.approve=="EnableAcknowledged"){
                    $("#btnacknowlege").prop("disabled",true);
                    $("#btnacknowlege").html('Approved');
                    $("#btnacknowlege").removeClass();
                    $("#btnacknowlege").addClass("btn btn-ack-disable-blue");
                }

                if(r1.approve=="EnableNotAcknowledged"){
                    $("#btnacknowlege").prop("disabled",false);
                    $("#btnacknowlege").html('Approve and Submit');
                    $("#btnacknowlege").removeClass();
                    $("#btnacknowlege").addClass("btn btn-ack-enable-grey");
                }
                
                
                HideBusy();
                callback();
                
            },
            error: function (xhr) {
                alert("Error");
                HideBusy();
            }
        });
    }

    //generate dynamically title of column report date
    self.generate_dynamically_titlecolumn_report_date = function(curentreportdate) {
        var arrMonth = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var fy = curentreportdate.match(/\d+/)[0];
        var fp = curentreportdate.match(/\D+/)[0];

        var indexMon = arrMonth.indexOf(fp.trim());
        var rs = new Array();
        for (var i = indexMon; i >= 0; i--) {
            rs.push(arrMonth[i] + " " +  fy);
        }
        for (var j = (arrMonth.length - 1); j > indexMon; j--) {
            rs.push(arrMonth[j]  + " " +  (fy - 1));
        }

        return rs;
    };

    //function for refresh datatable
    self.RefreshTable = function(tableId,keepCurrentPage){
        ShowBusy();
        var oTable = $(tableId).DataTable( );
        if (keepCurrentPage == true){
        oTable.ajax.reload(null, false);

        }else{
            oTable.ajax.reload();
        }

        HideBusy();
    }

    //load table dashbaord
    self.glverification_dashboard_table = function(){
        var responsiveHelper_dt_glverification_dashboard = undefined;
        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };
        $('#dt_glverification_dashboard').DataTable().clear().destroy();
        var oTable =  $('#dt_glverification_dashboard').DataTable({
            "autoWidth": false,
            "bFilter" : false,
            "infoEmpty": "No records available",
            "processing": true,
            "serverSide": true,
            "fixedHeader": true,
            "scrollX":  '100%',
            "scrollY":  '50vh',
            "scrollCollapse": true,
            "paging":         false,
            "ordering": false,
            "bInfo": false,
            language: {
                processing: "<img src='assets/images/loading.gif' alt='loading'> Processing...",
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_dt_glverification_dashboard) {
                    responsiveHelper_dt_glverification_dashboard = new ResponsiveDatatablesHelper($('#dt_glverification_dashboard'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow) {
                responsiveHelper_dt_glverification_dashboard.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_glverification_dashboard.respond();
            },
            "fnInitComplete": function(oSettings, json) {
                //oSettings.fnDrawCallback = alert('redraw');
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": base_url + 'glverification/getverification_dashboard',
                "type": "POST",
                "error": function(err,xhr){
                    alert('There was a problem loading glverification dashboard. Please try reloading the page.');
                }
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            {   "title": "<span><u>Period: "+$('#reportdate').val()+"</u><br>Transaction Type</span>", "targets": [ 0 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if (oData[0]==="Total"){
                    $(nTd).html('<div style="width:100%;padding: 2px 5px;" class="bd-darkgray bgcolor-dark-grey">' +oData[0]+  '</div>');
                }else{
                    $(nTd).html('<div style="width:100%;padding: 2px 5px;" class="bd-darkgray bg-color-white">' +oData[0]+  '</div>');
                }
            }
        },
        {   "targets": 1, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            var amountselected;
            if (parseFloat(oData[3]) === 0 || oData[3] === null ) {
                amountselected = "$"+0;
            } else if (parseFloat(oData[3]) < 0) {
                amountselected="($"+parseFloat(oData[3]).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+")";
            } else {
                amountselected="$"+parseFloat(oData[3]).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
            }
            if (oData[0]==="Total"){
                $(nTd).html('<div style="width: 100%;" class="bd-darkgray text-right bgcolor-dark-grey"><span id="linkamountselected" style="width:60%;color: blue;padding: 2px 5px;cursor:pointer;" class="display-inline"><u>'+ amountselected +'</u></span><span style="width:40%;border-left: 1px solid #999;padding: 2px 5px;" class="display-inline">'+ oData[4]+'</span></div>');
            }else{
                $(nTd).html('<div style="width: 100%;" class="bd-darkgray text-right bg-color-white"><span id="linkamountselected" style="width:60%;color: blue;padding: 2px 5px;cursor:pointer;" class="display-inline"><u>'+ amountselected +'</u></span><span style="width:40%;border-left: 1px solid #999;padding: 2px 5px;" class="display-inline">'+ oData[4]+'</span></div>');
            }

            $(nTd).on('click',"#linkamountselected", function(e) {
                if (oData[0]=="Payroll"){
                    glv_payroll.glverification_payroll_table();
                    $('.nav-tabs a[href="#hr2"]').tab('show');
                }else {
                    self.glverification_transactions_table(oData[0]);
                    $('.nav-tabs a[href="#hr1"]').tab('show');
                }
            });
        }
    },
    {   "targets": 2, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
        var amountactive;
        if (parseFloat(oData[5]) === 0 || oData[5] === null ) {
            amountactive = "$"+0;
        } else if (parseFloat(oData[5]) < 0) {
            amountactive="($"+parseFloat(oData[5]).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+")";
        } else {
            amountactive="$"+parseFloat(oData[5]).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
        }
        if (oData[0]==="Total"){
            $(nTd).html('<div style="width: 100%;" class="bd-darkgray text-right bgcolor-dark-grey"><span style="width:60%;padding: 2px 5px;" class="display-inline">'+ amountactive +'</span><span style="width:40%;border-left: 1px solid #999;padding: 2px 5px;" class="display-inline">'+ oData[6]+'</span></div>');
        }else{
            $(nTd).html('<div style="width: 100%;" class="bd-darkgray text-right bg-color-white"><span style="width:60%;padding: 2px 5px;" class="display-inline">'+ amountactive +'</span><span style="width:40%;border-left: 1px solid #999;padding: 2px 5px;" class="display-inline">'+ oData[6]+'</span></div>');
        }
    }
},
{   "targets": [ 3 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
    /** Old UI
    if (oData[4]===0 || oData[1]===100){
        $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:100%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px;">&nbsp;</span><span style="width:0%;background-color:#f0bcbc;line-height: 20px;display: inline-block;" >&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">100%</div>');
    }else {
        if(oData[1]===0){
            $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:' + oData[1] + '%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px;">&nbsp;</span><span  style="width:' + oData[2] + '%;background-color:#f0bcbc;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">' + oData[1] + '%</div>');

        }else{
            if (oData[10]>0){
                $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:' + oData[1] + '%;background:linear-gradient(to right,  #6ea400 ' + oData[1] + '%,  #FFDD00 ' + (100-oData[1])+ '%);line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px -2px -1px -1px;">&nbsp;</span><span  style="width:' + oData[2] + '%;background-color:#f0bcbc;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 2px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">' + oData[1] + '%</div>');
             } else{
            $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:' + oData[1] + '%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px -2px -1px -1px;">&nbsp;</span><span  style="width:' + oData[2] + '%;background-color:#f0bcbc;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 2px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">' + oData[1] + '%</div>');
            }

        }
    }

    **/
    var percentCompleted = 0;
    var percentNotverified = 0;
    var percentPending = 0;

    if(oData[4] === 0){
        percentCompleted = 100;
        percentNotverified =0;
        percentPending = 0;
    }else {
        percentCompleted = Math.round((oData[2]*100)/(oData[2]+oData[8]+oData[10]));
        percentNotverified = Math.round((oData[8]*100)/(oData[2]+oData[8]+oData[10]));
        percentPending = Math.round((oData[10]*100)/(oData[2]+oData[8]+oData[10]));
    }
    if(percentCompleted+percentNotverified+percentPending>100){
        percentCompleted--;
    }
    if(percentCompleted+percentNotverified+percentPending<100){
        percentNotverified++;
    }
    var totalCompleted = percentPending +percentCompleted;

    if( oData[10]>0 &&percentPending<=5){
        if( percentCompleted>=95 ){
        percentCompleted = (percentCompleted - 5 + percentPending);        
        } else  if(percentNotverified>=95){
            percentNotverified = (percentNotverified - 5 + percentPending);
        }else {
            if(percentCompleted>=5){
                percentCompleted = (percentCompleted - 5 + percentPending);
            }else{
                 percentNotverified = (percentNotverified - 5 + percentPending);
            }
        }
        percentPending = 5;
    }
   
   

     if (percentCompleted===100){
        $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:100%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px;">&nbsp;</span><span style="width:0%;background-color:#f0bcbc;line-height: 20px;display: inline-block;" >&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">100%</div>');
    }else if (percentPending===100){
        $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:100%;background-color:#FFDD00;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px;">&nbsp;</span><span style="width:0%;background-color:#f0bcbc;line-height: 20px;display: inline-block;" >&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">100%</div>');
    }else  if (percentNotverified===100){
    
        $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:0%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px;">&nbsp;</span><span  style="width:100%;background-color:#f0bcbc;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">0%</div>');

    }else if (oData[10]>0)   {
            if (percentCompleted>0){
                    $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:' + percentCompleted + '%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px -1px -1px -1px;">&nbsp;</span><span  style="width:' + percentPending + '%;background-color:#FFDD00;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 1px;">&nbsp;</span><span  style="width:' + percentNotverified+ '%;background-color:#f0bcbc;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 0px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">' + totalCompleted + '%</div>');
                 } else{
                    $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:' + percentCompleted + '%;background-color:#6ea400;line-height: 20px;display: inline-block;border: 0px;margin:-1px -1px -1px -1px;">&nbsp;</span><span  style="width:' + percentPending + '%;background-color:#FFDD00;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 1px;">&nbsp;</span><span  style="width:' + percentNotverified+ '%;background-color:#f0bcbc;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 0px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">' + totalCompleted + '%</div>');
                }

                if(percentNotverified===0){
                    $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:' + percentCompleted + '%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px -1px -1px -1px;">&nbsp;</span><span  style="width:' + percentPending + '%;background-color:#FFDD00;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 1px;">&nbsp;</span><span  style="width:' + percentNotverified+ '%;background-color:#f0bcbc;line-height: 20px;display: inline-block;margin:-1px 0px -1px 0px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">' + totalCompleted + '%</div>');

                }
    }else {
                $(nTd).html('<div style="width:85%;border-right: none;line-height:20px;" class="display-inline bd-darkgray"><span  style="width:' + totalCompleted + '%;background-color:#6ea400;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px -2px -1px -1px;">&nbsp;</span><span  style="width:' + percentNotverified + '%;background-color:#f0bcbc;line-height: 20px;display: inline-block;border: gray 1px solid;margin:-1px 0px -1px 2px;">&nbsp;</span> </div><div style="width:15%;border:1px solid gray;line-height: 20px;border-left: none; padding-right:5px;" class="display-inline text-right bg-color-white">' + totalCompleted + '%</div>');

    }
}
},
{   "targets": [ 4 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
    var amountnotverified;
    if (parseFloat(oData[7]) === 0 || oData[7] === null ) {
        amountnotverified = "$"+0;
    } else if (parseFloat(oData[7]) < 0) {
        amountnotverified="($"+parseFloat(oData[7]).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+")";
    } else {
        amountnotverified="$"+parseFloat(oData[7]).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
    }
    if (oData[0]==="Total"){
        $(nTd).html('<div style="width: 100%;" class="bd-darkgray text-right bgcolor-dark-grey"><span style="color: #686868;width:60%;padding: 2px 5px;" class="display-inline">'+amountnotverified+'</span><span style="color: gray;width:40%;border-left: 1px solid #999;padding: 2px 5px;" class="display-inline">'+ oData[8]+'</span></div>');
    }else{
        $(nTd).html('<div style="width: 100%;" class="bd-darkgray text-right bg-color-white"><span style="color: #686868;width:60%;padding: 2px 5px;" class="display-inline">'+amountnotverified+'</span><span style="color: gray;width:40%;border-left: 1px solid #999;padding: 2px 5px;" class="display-inline">'+ oData[8]+'</span></div>');
    }

}
}
]
});
self.getMonthlyTrendPercent();
}

    // Get Monthly trend percent
    self.getMonthlyTrendPercent = function(){
        $.ajax({ url: base_url + 'glverification/GetMonthlyTrendPercent',
            type: 'GET',
            success: function(data) {
                var realPercent = Number(data.trim())*100;
                localStorage.setItem('MonthlyPercentage',realPercent);
                var percent = Math.round(realPercent);
                $("#monthly_percentage").val( percent.toString()+"%");
                $("#monthly_percentage_actual").css("width", percent.toString()+"%");
                $("#monthly_percentage_remain").css("width",(100-percent).toString()+"%");
                HideBusy();
            },
            error: function (request, status, error) {
                HideBusy();
            }
        });
        
    }

    // Get Acknowleger Approve status
    self.GetAcknowledgerApprove = function(){
        ShowBusy();
        var form_data = {
            reportdate:$('#reportdate').val(),
            deptid:$("#drpdeptid option:selected").val()
        }

        $.ajax({
            url: base_url + 'glverification/getStatusForAcknowlegde',
            //async: false,
            method: 'POST',
            data: form_data,
            success: function (data) {
                var r1 = $.parseJSON(data);

                if (r1.approve=="Disable"){
                    $("#btnacknowlege").prop("disabled",true);
                    $("#btnacknowlege").html('Approve and Submit');
                    $("#btnacknowlege").removeClass();
                    $("#btnacknowlege").addClass("btn btn-ack-disable-grey");
                }

                if(r1.approve=="EnableAcknowledged"){
                    $("#btnacknowlege").prop("disabled",true);
                    $("#btnacknowlege").html('Approved');
                    $("#btnacknowlege").removeClass();
                    $("#btnacknowlege").addClass("btn btn-ack-disable-blue");
                }

                if(r1.approve=="EnableNotAcknowledged"){
                    $("#btnacknowlege").prop("disabled",false);
                    $("#btnacknowlege").html('Approve and Submit');
                    $("#btnacknowlege").removeClass();
                    $("#btnacknowlege").addClass("btn btn-ack-enable-grey");
                }
                
                
                HideBusy();
              //  callback();

          },
          error: function (xhr) {
            alert("Error");
            HideBusy();
        }
    });
        
    }


    //load table transaction
    self.glverification_transactions_table = function ($ReconGroupTitle) {
        var reportdate = $("#reportdate").val();
        $("#rdoshowdollar").prop("checked", true);

        var listHeader = self.generate_dynamically_titlecolumn_report_date(reportdate);
        if (listHeader != null) {
            for (var index = 0; index < listHeader.length; index++) {
                $("#col_" + (index + 1)).text(listHeader[index]);
            }
        }

        var responsiveHelper_dt_transaction = undefined;
        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };

         $('#exportTransaction').click(function() {
           ShowBusy();
            $.ajax({ url: base_url + '/glverification/exportTransaction',
                data: {"recongouptitle" : $ReconGroupTitle,"listHeader" : listHeader},
                type: 'POST',
                dataType: "json",
                success: function(data) {
                    HideBusy();
                },
                error: function (request, status, error) {
                    HideBusy();
                }
            }).done(function(data){
                var $a = $("<a>");
                $a.attr("href",data.file);
                $("body").append($a);
                $a.attr("download","TransactionData"+$.now()+".xlsx");
                $a[0].click();
                $a.remove();
            });
        });


        //Load Transaction table
        $('#dt_transaction').DataTable().clear().destroy();
        var oTable = $('#dt_transaction').DataTable({
            "infoEmpty": "No records available",
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "fixedHeader": true,
            "scrollX":  '100%',
            "autoWidth": false,
            "scrollY":  '60vh',
            "scrollCollapse": true,
            "searching": false,
            "paging": true,
            "oLanguage": {
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ items"
            },
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            "pageLength": 1000,
            "ordering": false,         
            language: {
                processing: "<img src='assets/images/loading.gif' alt='loading'> Processing...",
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": base_url + 'glverification/ajax_listtransactions',
                "type": "POST",
                "data" : {
                    "recongouptitle" : $ReconGroupTitle
                },
                "error": function(err,xhr){
                    alert('There was a problem loading list transaction. Please try reloading the page.')
                }
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_dt_transaction) {
                    responsiveHelper_dt_transaction = new ResponsiveDatatablesHelper($('#dt_transaction'), breakpointDefinition);
                }
            },
            "rowCallback": function( row, sData, index ) {
                responsiveHelper_dt_transaction.createExpandIcon(row);
                for(n=0;n<22;n++){
                    if (sData[0]===null){
                        $('td:eq('+n+')', row).addClass('bgcolor-white bdnonelf dt-link');  
                        $('td:eq('+n+')', row).text('');
                    }
                    if (sData[0]===0){
                        $('td:eq('+n+')', row).addClass('bgcolor-light-grey bdrbw dt-link');
                    }

                    if ((sData[0]!==null) && n===2 ){
                        $('td:eq(2)', row).addClass('bgcolor-light-pink bdrbw dt-link');
                    }
                    if ((sData[0]!==null) && n===3 ){
                        $('td:eq(3)', row).addClass('bgcolor-light-yellow bdrbw dt-link');
                    }
                    if ((sData[0]!==null) && n===4 ){
                        $('td:eq(4)', row).addClass('bgcolor-light-green bdrbw dt-link');
                    }
                    if ((sData[0]!==null) && n===5 ){
                        $('td:eq(5)', row).addClass('bgcolor-dark-grey bdrbw dt-link');
                    }
                    if ((sData[0]!==null) && n===6 ){
                        $('td:eq(6)', row).addClass('bgcolor-dark-grey bdrbw dt-link');
                    }
                    if ((sData[0]!==null) && n===7 ){
                        $('td:eq(7)', row).addClass('bgcolor-dark-grey bdrbw dt-link');
                    }
                }
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_transaction.respond();
                $("#dt_transaction_paginate").hide();
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            {"targets": 0,"visible": false,"searchable": false},
            {   "class": "col90","targets": [1,2] },
            {   "class": "col53 text-right","targets": [ 3 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if ((parseFloat(sData) == 0 && oData[9]==null) ||(parseFloat(sData) == 0 && oData[9]==0) || sData == null ) {
                    $(nTd).text('');
                    $(nTd).css('color', '#0000ff');
                } else if (parseFloat(sData) < 0) {
                    $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('cursor', 'pointer');
                } else {
                    $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('cursor', 'pointer');
                }
                $(nTd).css('text-decoration', 'underline');
                if (oData[0] !=null) {
                    $(nTd).attr('data-reconitemcd', oData[0]);
                    $(nTd).attr('data-reconstatuscd', '0');
                    $(nTd).attr('data-recongrouptitle', oData[1]);
                    $(nTd).attr('data-reconitemtitle', oData[2]);

                    if(oData[1]!="All Types" && parseFloat(oData[9]) > 0){
                        $(nTd).on('click', function (e) {
                            var reconitemcd = $(this).data('reconitemcd');
                            var reconstatuscd = $(this).data('reconstatuscd');
                            var recongrouptitle = $(this).data('recongrouptitle');
                            var priormonth=0;
                            var reconitemtitle = $(this).data('reconitemtitle');
                            $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                            $(".modal-footer #txtreconitemcd").val(reconitemcd);
                            $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                            $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                            self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                            $('#ModalGLVItemDetails').modal('show');
                            $('#ModalGLVItemDetails').removeData();
                        });
                    }
                    else {
                        $(nTd).css("cursor", "default");
                    }
                }
         } },
         {   "class": "col53 text-right","targets": [ 4 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if ((parseFloat(sData) == 0 && oData[10]==null) ||(parseFloat(sData) == 0 && oData[10]==0)  || sData == null ) {
                $(nTd).text('');
                $(nTd).css('color', '#0000ff');
            } else if (parseFloat(sData) < 0) {
                $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        //$(nTd).css('background-color', '#BDE5F8').css('font-weight', 'bold').css('color', '#009');
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    }
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '1000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && parseFloat(oData[10]) > 0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=0;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);
                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                    $(nTd).css('text-decoration', 'underline');

                } },
                {   "class": "col53 text-right","targets": [ 5 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if ((parseFloat(sData) == 0 && oData[11]==null) ||(parseFloat(sData) == 0  && oData[11]==0) || sData == null ) {
                        $(nTd).text('');
                        $(nTd).css('color', '#0000ff');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        //$(nTd).css('background-color', '#BDE5F8').css('font-weight', 'bold').css('color', '#009');
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    }
                    $(nTd).css('text-decoration', 'underline');
                    
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '3000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && parseFloat(oData[11]) > 0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=0;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {  "class": "col53 text-right","targets": [ 6 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if ((parseFloat(sData) == 0 && oData[12]==null) ||(parseFloat(sData) == 0 && oData[12]==0) || sData == null )   {
                        $(nTd).text('');
                        $(nTd).css('color', '#0000ff');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        //$(nTd).css('background-color', '#BDE5F8').css('font-weight', 'bold').css('color', '#009');
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    }
                    $(nTd).css('text-decoration', 'underline');
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '2000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && parseFloat(oData[12]) > 0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=0;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {   "class": "col53 text-right","targets": [ 7 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if ((parseFloat(sData) == 0 && oData[13]==null) ||(parseFloat(sData) == 0 && oData[13]==0) || sData == null )  {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                        //$(nTd).css('background-color', '#BDE5F8').css('font-weight', 'bold').css('color', '#009');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    }
                    $(nTd).css('text-decoration', 'underline');
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '0');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && parseFloat(oData[13]) > 0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=1;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {   "class": "col53 text-right","targets": [ 8 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if ((parseFloat(sData) == 0 && oData[14]==null) ||(parseFloat(sData) == 0 && oData[14]==0) || sData == null )  {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#0000ff');
                        $(nTd).css('cursor', 'pointer');
                    }
                    $(nTd).css('text-decoration', 'underline');
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '1000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && parseFloat(oData[14]) > 0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=1;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },


                {   "visible": false,"class": "col53 text-right","targets": [ 9 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('text-decoration', 'underline');
                    if(parseFloat(sData) == 0|| sData == null){
                        $(nTd).text('');
                    }
                    else {
                        $(nTd).css('cursor', 'pointer');
                    }
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '0');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && sData>0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=0;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {    "visible": false,"class": "col53 text-right","targets": [ 10 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('text-decoration', 'underline');
                    if(parseFloat(sData) == 0|| sData == null){
                        $(nTd).text('');
                    }
                    else {
                        $(nTd).css('cursor', 'pointer');
                    }
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '1000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && sData>0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=0;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {    "visible": false,"class": "col53 text-right","targets": [11 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('text-decoration', 'underline');
                    if(parseFloat(sData) == 0|| sData == null){
                        $(nTd).text('');
                    }
                    else {
                        $(nTd).css('cursor', 'pointer');
                    }
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '3000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && sData>0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=0;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();


                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {    "visible": false,"class": "col53 text-right","targets": [ 12 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('text-decoration', 'underline');
                    if(parseFloat(sData) == 0|| sData == null){
                        $(nTd).text('');
                    }
                    else {
                        $(nTd).css('cursor', 'pointer');
                    }
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '2000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && sData>0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=0;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {    "visible": false,"class": "col53 text-right","targets": [ 13 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('text-decoration', 'underline');
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');                    
                    }
                    else {
                        $(nTd).css('cursor', 'pointer');
                    }
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '0');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && sData>0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=1;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {    "visible": false,"class": "col53 text-right","targets": [ 14 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    $(nTd).css('color', '#0000ff');
                    $(nTd).css('text-decoration', 'underline');
                    if(parseFloat(sData) == 0|| sData == null){
                        $(nTd).text('');
                    }
                    else {
                        $(nTd).css('cursor', 'pointer');
                    }
                    if (oData[0] !=null) {
                        $(nTd).attr('data-reconitemcd', oData[0]);
                        $(nTd).attr('data-reconstatuscd', '1000');
                        $(nTd).attr('data-recongrouptitle', oData[1]);
                        $(nTd).attr('data-reconitemtitle', oData[2]);

                        if(oData[1]!="All Types" && sData>0){
                            $(nTd).on('click', function (e) {
                                var reconitemcd = $(this).data('reconitemcd');
                                var reconstatuscd = $(this).data('reconstatuscd');
                                var recongrouptitle = $(this).data('recongrouptitle');
                                var priormonth=1;
                                var reconitemtitle = $(this).data('reconitemtitle');
                                $("#ModalGLVItemDetails .modal-title-main").html("Verify GLV Items - " + recongrouptitle + " - " + reconitemtitle);

                                $(".modal-footer #txtreconitemcd").val(reconitemcd);
                                $(".modal-footer #txtreconstatuscd").val(reconstatuscd);
                                $(".modal-footer #txtrecongrouptitle").val(recongrouptitle);

                                self.glverification_GLVItemDetails_table(reconitemcd,reconstatuscd,recongrouptitle,priormonth);

                                $('#ModalGLVItemDetails').modal('show');
                                $('#ModalGLVItemDetails').removeData();
                            });
                        }
                        else {
                            $(nTd).css("cursor", "default");
                        }
                    }
                } },
                {   "class": "col55 text-right","targets": [ 15 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [ 16], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [17 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [ 18 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [ 19 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [ 20 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [ 21 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [22 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [23 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [24 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [25 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [26 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                {   "class": "col55 text-right","targets": [27 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if (parseFloat(sData) == 0 || sData == null ) {
                        $(nTd).text('');
                    } else if (parseFloat(sData) < 0) {
                        $(nTd).text('('+parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+')');
                        $(nTd).css('color', '#ea0202');
                    } else {
                        $(nTd).text(parseFloat(sData).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,"));
                        $(nTd).css('color', '#000000');
                    }
                } },
                ],

            });

        //Event Show Hide Checkbox Dollars Count
        $('input[type=radio][name=rdotransactionshowcountdollar]').change(function(e) {
            e.preventDefault();
            if (this.value == 'Dollars') {
                oTable.columns( [ 3,4,5,6,7,8 ] ).visible( true, true );
                oTable.columns( [ 9,10,11,12,13,14 ] ).visible(false, false);
                oTable.draw( false ); // adjust column sizing and redraw
            }
            else if (this.value == 'Count') {
                oTable.columns( [ 3,4,5,6,7,8 ] ).visible( false, false );
                oTable.columns( [ 9,10,11,12,13,14 ] ).visible( true, true );
                oTable.draw( false );
            }
        });

        //Show hide Note for
        $("#flip").click(function(){
            $("#panel").slideToggle();
        });
        $("#panel").click(function(){
            $("#panel").slideToggle();
        });

    }
    //when click close comment Modal
    $("#closeModalCommentBtn").on('click',function(){
        if($("#comment_Type").val() == "Payroll"){
         //   self.glverification_payroll_verify_table();
            $('#dt_payroll_verification').DataTable().ajax.reload(null,false);
        } else if ($("#comment_Type").val() == "Transaction"){
            $('#dt_verifyglvitems').DataTable().ajax.reload(null,false);
        }else{
            self.checkExistedCommentForMonthlyTrendDataId();
        }
    });
    
    $("#cancelCommentBtn").on("click",function(){
        $('#addCommentBtn').removeAttr('disabled');
        $('#addCommentBtn').removeClass('display-none');
        $('#saveCommentBtn').addClass('display-none');
        $('#saveCommentBtn').attr("disabled",'disabled');
        $("#current_comment").val("");
        $("#cancelCommentBtn").addClass('display-none');
        $("#addEditCommentDiv").addClass('display-none');
    });  

    //load comment for monthly trend tab
     self.loadCommentMonthlyTrend = function () {
        var deptId = $("#drpdeptid option:selected").val()
        var bu = $("#drpbusunit").val();
        var site = $("#drpsite").val();
        $.ajax({ url: base_url + 'glverification/getMonthlyTrendDataId',
            data: {deptId: deptId, businessUnit: bu, site:site},
            type: 'POST',
            dataType: "json",
            success: function(data) {
                $("#uniqueId").val(data); 
                self.loadGLVComentTypeIdForMonthlyTrendDataId(data);
            },
            error: function (request, status, error) {
                alert(error);
            }
        });
    }

    //load GLVComentTypeId For MonthlyTrendDataId
     self.loadGLVComentTypeIdForMonthlyTrendDataId= function (monthTrendId) {
        $.ajax({ url: base_url + 'glverification/getGLVComentTypeIdForMonthlyTrendDataId',
            data: {monthlyTrendDataId: monthTrendId},
            type: 'POST',
            dataType: "json",
            success: function(data) {
                $("#comment_glvtype").val(data); 
                $("#ModalGLVComments").modal({backdrop: 'static', keyboard: false});;
                $("#ModalGLVComments").removeData();
                $('#dt_glvcomments').DataTable().clear().destroy();
                $("#current_comment").val('');
                $("#addCommentBtn").removeAttr("disabled");
                glv_payroll.renewComment();
                glv_payroll.loadCommentDataTable();
            },
            error: function (request, status, error) {
                alert(error);
            }
        });
    }

     // check Existed Comment ForMonthly Trend Data Id
     self.checkExistedCommentForMonthlyTrendDataId= function () {
         var deptId = $("#drpdeptid option:selected").val()
         var bu = $("#drpbusunit").val();
         var site = $("#drpsite").val();
         $.ajax({ url: base_url + 'glverification/checkExistedCommentForMonthlyTrendDataId',
            data: {deptId: deptId, businessUnit: bu, site:site},
            type: 'POST',
            dataType: "json",
            success: function(data) {
             if(data>0){
                $( "#commentMonthly" ).removeClass( "glyphicon-plus-sign" ).addClass( "glyphicon-comment" );
                $( "#textCommentMonthly" ).html('Show');
            }else{
                $( "#commentMonthly" ).removeClass( "glyphicon-comment" ).addClass( "glyphicon-plus-sign" );
                $( "#textCommentMonthly" ).html('Add');
            }
        },
        error: function (request, status, error) {
            alert(error);
        }
    });


     }

    //load data for monthly trend table

     $("#commentMonthly").on('click', function (e) {
         $("#comment_Type").val("MonthlyTrend"); 
          self.loadCommentMonthlyTrend();          
                
            });

    //load table Verify GLV Item details
    self.glverification_GLVItemDetails_table = function(reconitemcd,reconstatuscd,recongrouptitle,priormonth){      
        $("#saveGLVItemDetails").prop("disabled",true);
        $("#btnresetall").prop("disabled",true);
        // Set default checked for checkbox of columns filtered
        $(".toggle-vis").prop('checked', true);
        var responsiveHelper_dt_verifyglvitems = undefined;
        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };

        $('#exportGLVItemDetails').click(function() {
           ShowBusy();
            $.ajax({ 
                url: base_url + '/glverification/exportGLVItemDetails',
                data: {
                    "reconitemcd" : reconitemcd,
                    "reconstatuscd": reconstatuscd,
                    "recongrouptitle"  : recongrouptitle,
                    "priormonth":priormonth,
                    "deptid":$("#drpdeptid option:selected").val(),
                    "busunit":$("#drpbusunit option:selected").val(),
                    "myfilter": $("#drpFilters option:selected").val()
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
                var $a = $("<a>");
                $a.attr("href",data.file);
                $("body").append($a);
                $a.attr("download","GLVItemDetailsData"+$.now()+".xlsx");
                $a[0].click();
                $a.remove();
            });
        });

        $('#dt_verifyglvitems').DataTable().clear().destroy();
        var oTable = $('#dt_verifyglvitems').DataTable({
            "bFilter" : false,
            "autoWidth": false,
            "infoEmpty": "No records available",
            "processing": true,
            "serverSide": true,
            "fixedHeader": true,
            "scrollX":  '100%',
            "scrollY":  '63vh',
            "scrollCollapse": true,
            "paging": true,
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            "pageLength": 100,
            "ordering": false,
            language: {
                processing: "<img src='assets/images/loading.gif' alt='loading'> Processing...",
                info: "Showing _START_ to _END_ of _TOTAL_ items"
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": base_url + 'glverification/getverification_verifyglvitemdetail',
                "type": "POST",
                "data" : {
                    "reconitemcd" : reconitemcd,
                    "reconstatuscd": reconstatuscd,
                    "recongrouptitle"  : recongrouptitle,
                    "priormonth":priormonth,
                    "deptid":$("#drpdeptid option:selected").val(),
                    "busunit":$("#drpbusunit option:selected").val(),
                    "myfilter": $("#drpFilters option:selected").val()
                },
                "error": function(){
                    HideBusy();
                }
            },
            "preDrawCallback" : function() {
                ShowBusy();
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_dt_verifyglvitems) {
                    responsiveHelper_dt_verifyglvitems = new ResponsiveDatatablesHelper($('#dt_verifyglvitems'), breakpointDefinition);
                }
            },
            "rowCallback" : function(nRow,oData) {
                responsiveHelper_dt_verifyglvitems.createExpandIcon(nRow);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_verifyglvitems.respond();
                $('select').on('change', function() {
                    $("#saveGLVItemDetails").prop("disabled",false);
                    $("#btnresetall").prop("disabled",false);
                });
                $('.inputcomment').on('input', function() {
                    $("#saveGLVItemDetails").prop("disabled",false);
                    $("#btnresetall").prop("disabled",false);
                });
                HideBusy();
            },
            "fnInitComplete": function(oSettings, json) {
                //oSettings.fnDrawCallback = alert('redraw');
            },

            /* Old order
            //Set column definition initialisation properties.
            "columnDefs": [
            {"targets": 0,"visible": false,"searchable": false},
            {"class": "col40","targets": [0,10,2,3,4,5,6,7,8,9]},
            {"targets": 9,"visible": false,"searchable": false},
            {"class": "col50","targets": [ 10 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                var amount;
                if (parseFloat(sData) === 0 || sData === null ) {
                    amount = 0;
                } else if (parseFloat(sData) < 0) {
                    amount="("+parseFloat(sData).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+")";
                } else {
                    amount=parseFloat(sData).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
                }
                $(nTd).html(amount);
            }
        },
        {"class": "col110","targets": 11, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if(sData !=2000) {

                var ddl = "<select aria-labelledby='lblhidden_status' class='form-control' id='drpreconstatuscd_"+oData[0]+"' >";
                ddl = ddl + "<option value='0' "+(sData==0?'selected=selected':'')+">Not Verified</option>";
                ddl = ddl + "<option value='1000' "+(sData==1000?'selected=selected':'')+">Pending</option>";
                ddl = ddl + "<option value='3000' "+(sData==3000?'selected=selected':'')+">Complete</option>";
                ddl += "</select>";
                $(nTd).empty();
                $(nTd).prepend(ddl);

                $("#btnmoveallto_verified").show();
                $("#btnmoveallto_pending").show();
                $("#btnmoveallto_completed").show();
                $("#btnresetall").show();
                $("#saveGLVItemDetails").show();
            }else{
                $("#btnmoveallto_verified").hide();
                $("#btnmoveallto_pending").hide();
                $("#btnmoveallto_completed").hide();
                $("#btnresetall").hide();
                $("#saveGLVItemDetails").hide();
                $(nTd).text('Auto Complete');
            }
        } },
        {"class": "col40 text-center","targets": 12, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            var IconId = "commentIcon_"+oData[0].toString().trim();
            $(nTd).attr('data-uniqueId', oData[0]);

            $("#uniqueId").val(oData[0]);
            if (sData == null || sData == ""){

                $(nTd).html('<i class=\"glyphicon glyphicon-plus-sign gi-2x cursor-pointer \" id=\"'+IconId+'\" ></i>');
            } else{
                $(nTd).html('<i class=\"glyphicon glyphicon-comment gi-2x cursor-pointer \" id=\"'+IconId+'\" ></i>');
            }
            $(nTd).on('click', function (e) {
                $("#comment_Type").val("Transaction");
                $("#comment_glvtype").val(oData[12]);    
                $("#ModalGLVComments").modal({backdrop: 'static', keyboard: false});;
                $("#ModalGLVComments").removeData();
                $('#dt_glvcomments').DataTable().clear().destroy();
                $("#uniqueIdComment").html("[" + oData[16] + "]");
                $("#uniqueId").val(oData[0]);
                $("#current_comment").val('');
                $("#addCommentBtn").removeAttr("disabled");
                glv_payroll.renewComment();
                glv_payroll.loadCommentDataTable();
            });
            // var glvcomment = $('<input class=\"inputcomment form-control\" type=\"input\" id="txtreconcomment_'+oData[0]+'" value="' + (sData === null ? " " : sData) + '">');
            // $(nTd).empty();
            // $(nTd).prepend(glvcomment);
        } },
        {"class": "col60","targets": [13]},
        {"class": "col130","targets": [14]},
        {"class": "col50","targets": [15]},
        {"class": "col150","targets": [16]},
        {"class": "col400","targets": [17]},             
        {"targets": [18],"visible": false,"searchable": false},   
        {"class": "col130","targets": [19]},   
        {"class": "col180","targets": [20]},   
        {"targets": [21],"visible": false,"searchable": false},   
        {"targets": [22],"visible": false,"searchable": false},
        {"class": "col250","targets": [23]},
        {"class": "col100","targets": [24]},
        {"class": "col100","targets": [25], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if (sData != "" && sData != null) {
                       // var date = new Date(sData).toISOString().split(".")[0].replace("T", " ");                      
                       $(nTd).html(sData.split(".")[0]);
                   }
                   else
                    $(nTd).html("");
            }},
            {"class": "col70","targets": [27,28,29,30,31,32,33]},
            {"class": "col80 text-center","targets": 26,"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).attr('data-document_uniqueid', oData[0]);
                $(nTd).attr('data-document_glvtypeid', sData); 
                $(nTd).attr('data-document_jnrlinedescription',oData[16]);
                $(nTd).on('click', function (e) {
                    var document_uniqueid = $(this).data('document_uniqueid');
                    $("#document_glvtypeid").val($(this).data('document_glvtypeid'));
                    $("#document_uniqueid").val(document_uniqueid);
                    $("#jnrlinedescription").html($(this).data('document_jnrlinedescription'));
                    $("#upload_glvType").val('Transaction');
                //reset current display
                $('#msg_upload').html(""); // display success response from the server
                $('#files').val('');
                $("#ModalUploadDocument").modal({backdrop: 'static', keyboard: false});
                $("#ModalUploadDocument").removeData();
                $("#upload_file").attr('disabled','disabled');
                glv_payroll.loadListFiles( $("#upload_glvType").val(), $("#document_uniqueid").val(), $("#document_glvtypeid").val());

            });
                if (sData == null || sData == ""){
                    $(nTd).empty();
                    var uploadBt = $('<button aria-label=\"btn-upload\" type=\"button\" style=\"border:none;background-color:transparent;\"><i class=\"glyphicon glyphicon-upload gi-2x  \" id="uploadBt_'+oData[0]+'"  ></i></button>');
                    $(nTd).prepend(uploadBt);
                } else{
                    $(nTd).empty();
                    var uploadBt = $('<button aria-label=\"btn-attachment\" type=\"button\" style=\"border:none;background-color:transparent;\"><i class=\"glyphicon glyphicon-paperclip gi-2x  \" id="uploadBt_'+oData[0]+'"  ></i></button>');
                    $(nTd).prepend(uploadBt);
                }


            }}
            ]
            */
            // New order
             "columnDefs": [
             {"targets": 0,"visible": false,"searchable": false},
             {"class": "col110","targets": 1, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if(sData !=2000) {

                var ddl = "<select aria-labelledby='lblhidden_status' class='form-control' id='drpreconstatuscd_"+oData[0]+"' >";
                ddl = ddl + "<option value='0' "+(sData==0?'selected=selected':'')+">Not Verified</option>";
                ddl = ddl + "<option value='1000' "+(sData==1000?'selected=selected':'')+">Pending</option>";
                ddl = ddl + "<option value='3000' "+(sData==3000?'selected=selected':'')+">Complete</option>";
                ddl += "</select>";
                $(nTd).empty();
                $(nTd).prepend(ddl);

                $("#btnmoveallto_verified").show();
                $("#btnmoveallto_pending").show();
                $("#btnmoveallto_completed").show();
                $("#btnresetall").show();
                $("#saveGLVItemDetails").show();
            }else{
                $("#btnmoveallto_verified").hide();
                $("#btnmoveallto_pending").hide();
                $("#btnmoveallto_completed").hide();
                $("#btnresetall").hide();
                $("#saveGLVItemDetails").hide();
                $(nTd).text('Auto Complete');
            }
        } },
         {"class": "col40 text-center","targets": 2, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            var IconId = "commentIcon_"+oData[0].toString().trim();
            $(nTd).attr('data-uniqueId', oData[0]);

            $("#uniqueId").val(oData[0]);
            if (sData == null || sData == ""){

                $(nTd).html('<i class=\"glyphicon glyphicon-plus-sign gi-2x cursor-pointer \" id=\"'+IconId+'\" ></i>');
            } else{
                $(nTd).html('<i class=\"glyphicon glyphicon-comment gi-2x cursor-pointer \" id=\"'+IconId+'\" ></i>');
            }
            $(nTd).on('click', function (e) {
                $("#comment_Type").val("Transaction");
                $("#comment_glvtype").val(oData[2]);    
                $("#ModalGLVComments").modal({backdrop: 'static', keyboard: false});;
                $("#ModalGLVComments").removeData();
                $('#dt_glvcomments').DataTable().clear().destroy();
                $("#uniqueIdComment").html("[" + oData[22] + "]");
                $("#uniqueId").val(oData[0]);
                $("#current_comment").val('');
                $("#addCommentBtn").removeAttr("disabled");
                glv_payroll.renewComment();
                glv_payroll.loadCommentDataTable();
            });
            // var glvcomment = $('<input class=\"inputcomment form-control\" type=\"input\" id="txtreconcomment_'+oData[0]+'" value="' + (sData === null ? " " : sData) + '">');
            // $(nTd).empty();
            // $(nTd).prepend(glvcomment);
        } },
        {"class": "col80 text-center","targets": 3,"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).attr('data-document_uniqueid', oData[0]);
                $(nTd).attr('data-document_glvtypeid', sData); 
                $(nTd).attr('data-document_jnrlinedescription',oData[22]);
                $(nTd).on('click', function (e) {
                    var document_uniqueid = $(this).data('document_uniqueid');
                    $("#document_glvtypeid").val($(this).data('document_glvtypeid'));
                    $("#document_uniqueid").val(document_uniqueid);
                    $("#jnrlinedescription").html($(this).data('document_jnrlinedescription'));
                    $("#upload_glvType").val('Transaction');
                //reset current display
                $('#msg_upload').html(""); // display success response from the server
                $('#files').val('');
                $("#ModalUploadDocument").modal({backdrop: 'static', keyboard: false});
                $("#ModalUploadDocument").removeData();
                $("#upload_file").attr('disabled','disabled');
                glv_payroll.loadListFiles( $("#upload_glvType").val(), $("#document_uniqueid").val(), $("#document_glvtypeid").val());

            });
                if (sData == null || sData == ""){
                    $(nTd).empty();
                    var uploadBt = $('<button aria-label=\"btn-upload\" type=\"button\" style=\"border:none;background-color:transparent;\"><i class=\"glyphicon glyphicon-upload gi-2x  \" id="uploadBt_'+oData[0]+'"  ></i></button>');
                    $(nTd).prepend(uploadBt);
                } else{
                    $(nTd).empty();
                    var uploadBt = $('<button aria-label=\"btn-attachment\" type=\"button\" style=\"border:none;background-color:transparent;\"><i class=\"glyphicon glyphicon-paperclip gi-2x  \" id="uploadBt_'+oData[0]+'"  ></i></button>');
                    $(nTd).prepend(uploadBt);
                }


            }},
            {"class": "col180","targets": [4]}, 
            {"class": "col170","targets": [5]},
            {"class": "col100","targets": [6], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
            if (sData != "" && sData != null) {
                       // var date = new Date(sData).toISOString().split(".")[0].replace("T", " ");                      
                       $(nTd).html(sData.split(".")[0]);
                   }
                   else
                    $(nTd).html("");
            }},
            {"class": "col40","targets": [7,8,10,11,12,13,14,15]},
            {"class": "col250","targets": [9]},
            {"class": "col50","targets": [16]},
            {"class": "col50","targets": [ 17 ], "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    var amount;
                    if (parseFloat(sData) === 0 || sData === null ) {
                        amount = 0;
                    } else if (parseFloat(sData) < 0) {
                        amount="("+parseFloat(sData).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace('-', '')+")";
                    } else {
                        amount=parseFloat(sData).toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
                    }
                    $(nTd).html(amount);
                }
            },
            {"class": "col60","targets": [18]},
            {"class": "col130","targets": [19]},
            {"class": "col130","targets": [20]},
            {"class": "col400","targets": [21]}, 
            {"class": "col150","targets": [22]},
            {"class": "col70","targets": [23,24,25,26,27,28,29]}


            // end New order
            ]

        });
        
        // Show hide column in table
        $(document).ready(function() {
            var table = $('#dt_verifyglvitems').DataTable();
            
             $('.toggle-vis').change(function () {
                   // Get the column API object
                var column = table.column( $(this).attr('data-column') );
         
                // Toggle the visibility
                column.visible( ! column.visible() );
                 });           
        } );
        //Event Button Update status GLV Items
        $('#saveGLVItemDetails').off('click');
        $("#saveGLVItemDetails").click(function(){
            var table = $('#dt_verifyglvitems').DataTable();
            var data = table
            .rows()
            .data();
            var listData = [];
            for (var index = 0; index < data.length; index++) {
                //in case autocomplete we not used dropdownbox but using textbox
                var reconstatus = $("#drpreconstatuscd_" + data[index][0]).val() ? $("#drpreconstatuscd_" + data[index][0]).val().trim(): 2000;
                
           //     var reconcomment = $("#txtreconcomment_" + data[index][0]).val().trim();
           /* Old order
                if(data[index][11]==null) data[index][11]="";
                if (reconstatus != data[index][11] ) {
                    var changeData = {};
                    changeData.uniqueid = data[index][0];
                    changeData.reconstatuscd = reconstatus;
                   // changeData.reconcomment = reconcomment;
                    listData.push(changeData);
                }
            */

            // New order

            if(data[index][1]==null) data[index][1]="";
                if (reconstatus != data[index][1] ) {
                    var changeData = {};
                    changeData.uniqueid = data[index][0];
                    changeData.reconstatuscd = reconstatus;
                   // changeData.reconcomment = reconcomment;
                    listData.push(changeData);
                }
            }
            if (listData.length>0) {
                self.Update_VerifyGLVItems(listData);
            }
        });

        //Event Button click move all to verified
        $("#btnmoveallto_verified").click(function(){
            var table = $('#dt_verifyglvitems').DataTable();
            var data = table
            .rows()
            .data();
            //flag for decide if actually data has changed
            var flag = false;
            for (var index = 0; index < data.length; index++) {
                if($("#drpreconstatuscd_" + data[index][0]).val() != 0){
                    flag = true;
                }
                $("#drpreconstatuscd_" + data[index][0]).val(0);
            }       
            if(flag){
                $("#saveGLVItemDetails").prop("disabled",false);
                $("#btnresetall").prop("disabled",false);
            }
        });

        //Event Button click move all to pending
        $("#btnmoveallto_pending").click(function(){
            var table = $('#dt_verifyglvitems').DataTable();
            var data = table
            .rows()
            .data();
            //flag for decide if actually data has changed
            var flag = false;
            for (var index = 0; index < data.length; index++) {
                if($("#drpreconstatuscd_" + data[index][0]).val() != 1000){
                    flag = true;
                }
                $("#drpreconstatuscd_" + data[index][0]).val(1000);
            }        
            if(flag){
                $("#saveGLVItemDetails").prop("disabled",false);
                $("#btnresetall").prop("disabled",false);
            }
        });

        //Event Button click move all to completed
        $("#btnmoveallto_completed").click(function(){
            var table = $('#dt_verifyglvitems').DataTable();
            var data = table
            .rows()
            .data();
            //flag for decide if actually data has changed
            var flag = false;
            for (var index = 0; index < data.length; index++) {
                if($("#drpreconstatuscd_" + data[index][0]).val() != 3000){
                    flag = true;
                }
                $("#drpreconstatuscd_" + data[index][0]).val(3000);
            }
            if(flag){
                $("#saveGLVItemDetails").prop("disabled",false);
                $("#btnresetall").prop("disabled",false);
            }
        });

        //Event Button click Reset all
        $("#btnresetall").click(function(){
            self.RefreshTable("#dt_verifyglvitems",true);
            $(this).prop("disabled",true);
            $("#saveGLVItemDetails").prop("disabled",true);
        });
     
    
    // Calculator Data table Height
    var calcDataTableHeight = function() {     
        return $(window).height() * 50 / 100;
    };

    // Get recon status
    var get_recon_status = function(callback) {
        $.ajax({ url: base_url + '/glverification/get_recon_status',
            type: 'POST',
            dataType: "json",
            success: function(data) {
                callback(data);
            },
            error: function (request, status, error) {
            }
        });
    }

    //Function Update status GLV Items
    self.Update_VerifyGLVItems = function(data) {
        ShowBusy();
        $.ajax({ url: base_url + '/glverification/update_verifyglvitems',
            data: {data: data},
            type: 'POST',
            dataType: "json",
            success: function(data) {
                HideBusy();
                self.Submit_filterGLV(function(){
                    self.RefreshTable("#dt_transaction",true);
                    $('.nav-tabs a[href="#hr1"]').tab('show');
                });
                
                //$('#ModalGLVItemDetails').modal('hide');
                $(".modal").removeClass("in");
                $(".modal-backdrop").remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
                $(".modal").hide();
            },
            error: function (request, status, error) {
                HideBusy();
            }
        });
    }

   
    }
}

