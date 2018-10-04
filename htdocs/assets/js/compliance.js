var ComplianceManagement = function () {
    var self = this;

    //function init
    self.init_Compliance = function () {

        self.compliance_dashboard_table();
        self.compliance_statusreport_table();
        //event click submit filter
        $('#btnfilter_compliance').off('click');
        $('#btnfilter_compliance').on('click', function () {
            ShowBusy();
            $('#dt_compliance_dashboard tfoot > tr > td').remove();
            self.compliance_dashboard_table();
            self.compliance_statusreport_table();
            $('.nav-tabs a[href="#hr1"]').tab('show');

            $('#tabscompliance_statusreport').removeClass('disabled');
            $('#tabscompliance_statusreport').find('a').attr("data-toggle", "tab");

            $('#tabscompliance_detailsreport').removeClass('disabled');
            $('#tabscompliance_detailsreport').find('a').attr("data-toggle", "tab");
            HideBusy();
        });
        
        //load drp reportdate
        var month_to_number = {
            'Jan': '1',
            'Feb': '2',
            'Mar': '3',
            'Apr': '4',
            'May': '5',
            'Jun': '6',
            'Jul': '7',
            'Aug': '8',
            'Sep': '9',
            'Oct': '10',
            'Nov': '11',
            'Dec': '12'
        };
        var month_to_name = {
            '1': 'Jan',
            '2': 'Feb',
            '3': 'Mar',
            '4': 'Apr',
            '5': 'May',
            '6': 'Jun',
            '7': 'Jul',
            '8': 'Aug',
            '9': 'Sep',
            '10': 'Oct',
            '11': 'Nov',
            '12': 'Dec'
        };
        $('.date-picker').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: "M yy",
            onClose: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                //jQuery(this).datepicker('setDate', new Date(year, month, 1));
                //{dialog.object}.setValue('ReportDate',month_to_name[parseInt(month)+1]+" "+year);
                $('#reportdate').val(month_to_name[parseInt(month) + 1] + " " + year);
                if(dateText!=this.value){
                //reset all data on Compliance
                self.ResetData();
            }
        },
        beforeShow: function () {
            if ((datestr = $(this).val()).length > 0) {
                year = datestr.substring(datestr.length - 4, datestr.length);
                month = month_to_number[datestr.substring(0, 3)];
                $(this).datepicker('option', 'defaultDate', new Date(year, month - 1, 1));
                $(this).datepicker('setDate', new Date(year, month - 1, 1));
            }
        },
        yearRange: ((new Date).getFullYear() - 10).toString()+":"+((new Date).getFullYear()+1).toString()            
    });
        $('#btnshowreportdate').click(function () {
            $("#reportdate").focus();
            $(".ui-datepicker-calendar").hide();
        });
        //================

        //on change control point dropdown
        $("#drpdeptid").on('change',function(){
        //reset all data on Compliance
        self.ResetData();
    });

        //on change bussiness unit dropdown
        $("#drpbusunit").on('change',function(){
        //reset all data on Compliance
        self.ResetData();
    });

        ////event click on tabs dashboard
        $('#tabscompliance_dashboard_a').click(function () {
            //self.compliance_dashboard_table();
        });

        ////event click on tabs status report
        $('#tabscompliance_statusreport_a').click(function () {
           // self.compliance_statusreport_table();
       });

        ////event click on tabs details report
        $('#tabscompliance_detailsreport_a').click(function () {
            self.compliance_detailreport_table($("#drpdeptid option:selected").val());
        });
    }
     //reset all Data on Compliance
     self.ResetData = function(){
        $('#dt_compliance_dashboard').DataTable().clear().destroy();

        $('#dt_compliance_dashboard tfoot > tr > td').hide();
        $('#dt_compliance_statusreport').DataTable().clear().destroy();
        $('.nav-tabs a[href="#hr1"]').tab('show');

        

        $('#tabscompliance_statusreport').removeClass('active').addClass('disabled');
        $('#tabscompliance_statusreport').find('a').removeAttr("data-toggle");

        $('#tabscompliance_detailsreport').removeClass('active').addClass('disabled');
        $('#tabscompliance_detailsreport').find('a').removeAttr("data-toggle");
        
    }
    //load table compliance dashboard
    self.compliance_dashboard_table = function () {
        var DeptTitle_Name="";
        if ($("#drpdeptid option:selected").val() == '999999'){
            DeptTitle_Name="All Control Points";
        }else{
            DeptTitle_Name=$("#drpdeptid option:selected").text().substring($("#drpdeptid option:selected").text().indexOf("-")+1)
        }
        $("#titletblcolumn").text(DeptTitle_Name);
        $("#comp_reportdate").text($('#reportdate').val());

        var responsiveHelper_dt_compliance_dashboard = undefined;
        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };
        
        //Load compliance dashboard table
        $('#dt_compliance_dashboard').DataTable().clear().destroy();
        var oTable = $('#dt_compliance_dashboard').DataTable({
            "autoWidth": false,
            "bFilter" : false,
            "infoEmpty": "No records available",
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "paging": false,
            "ordering": false,
            "bInfo": false,
            language: {
                processing: "<img src='assets/images/loading.gif' alt='loading'> Processing...",
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": base_url + 'compliance/ajax_listcompliance_dashboard',
                "type": "POST",
                "data" : {
                    "reportdate" :$('#reportdate').val(),
                    "deptid" : $("#drpdeptid option:selected").val(),
                    "busunit" :$("#drpbusunit option:selected").val()
                },
                "error":function(err,xhr){
                    alert('There was a problem loading compliance dashboard items. Please try reloading the page.');
                }
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_dt_compliance_dashboard) {
                    responsiveHelper_dt_compliance_dashboard = new ResponsiveDatatablesHelper($('#dt_compliance_dashboard'), breakpointDefinition);
                }
            },
            "rowCallback": function( row, sData, index ) {
                responsiveHelper_dt_compliance_dashboard.createExpandIcon(row);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_compliance_dashboard.respond();
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            {   "class": "","targets": 0, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {

                var complete = parseFloat(((oData[1]+oData[3])/oData[4]*100));
                if (complete!== 0 && complete !=100) {
                    complete=complete.toFixed(2);
                }

                var notcomplete = parseFloat((oData[2]/oData[4] * 100));                    
                if (notcomplete !== 0 && notcomplete !=100) {
                    notcomplete=notcomplete.toFixed(2);
                }
                if(oData[4]==0){
                    complete=100;
                    notcomplete=0;
                }
                var appendToResult = "<div class=\"row\">" +
                "<div class=\"col-sm-4\" style=\"padding: 4px;\"><span id=\"linkdashboard\" style=\"color: blue;text-decoration: underline;cursor: pointer;\">"+ sData.substring(sData.indexOf("_")+1) +"</span></div>" +
                "<div class=\"col-sm-8\" style=\"padding: 2px;\">" +
                "<div style=\"BORDER-TOP: black 1px solid;BORDER-RIGHT: 0;WIDTH: 85%;BORDER-BOTTOM: black 1px solid;POSITION: relative;BORDER-LEFT: black 1px solid;line-height: 23px;float:left\">" +
                "<span style=\"BACKGROUND-COLOR: #008000;width: "+complete+"%;display: inline-block;text-align: center;\">&nbsp;</span>" +
                "<span style=\"BACKGROUND-COLOR: #f0bcbc;width: "+notcomplete+"%;display: inline-block;text-align: center;\">&nbsp;</span>" +
                "</div>" +
                "<div style=\"BORDER-TOP: black 1px solid;BORDER-RIGHT: black 1px solid;WIDTH: 15%;BORDER-BOTTOM: black 1px solid;POSITION: relative;BORDER-LEFT: 0;line-height: 23px;display:inline-block;text-align: right;padding-right:5px;\">"+complete+"%</div>" +
                "</div>" +
                "</div>";
                $(nTd).html(appendToResult);

                $(nTd).on('click',"#linkdashboard", function(e) {
                    self.compliance_detailreport_table(oData[5]);
                    $('.nav-tabs a[href="#hr3"]').tab('show');
                });

            } }
            ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                $('#dt_compliance_dashboard tfoot > tr > td').remove();

                var totalselected = 0;
                for ( var i=0 ; i<aaData.length ; i++ )
                {
                    totalselected += aaData[i][4]*1;
                }

                var totalcomplete = 0;
                for ( var i=0 ; i<aaData.length ; i++ )
                {
                    totalcomplete += aaData[i][1]*1 + aaData[i][3]*1;
                }

                var totalnotcomplete = 0;
                for ( var i=0 ; i<aaData.length ; i++ )
                {
                    totalnotcomplete += aaData[i][2]*1;
                }

                var totalpercomplete = parseFloat(totalcomplete/totalselected * 100);
                if (totalpercomplete !== 0) {
                    totalpercomplete=totalpercomplete.toFixed(2);
                }

                var totalpernotcomplete = parseFloat(totalnotcomplete/totalselected * 100);
                if (totalpernotcomplete !== 0) {
                    totalpernotcomplete=totalpernotcomplete.toFixed(2);
                }
                if(totalselected == 0){
                    totalpercomplete=100;
                    totalpernotcomplete=0;
                }
                if (iEnd > 0) {
                    var appendTotalResult = document.createElement('td');

                    $(appendTotalResult).html("<div class=\"row\">" +
                        "<div class=\"col-sm-4\" style=\"padding: 4px;font-weight: bold;\">Total "+DeptTitle_Name+"</div>" +
                        "<div class=\"col-sm-8\" style=\"padding: 2px;\">" +
                        "<div style=\"BORDER-TOP: black 1px solid;BORDER-RIGHT: 0;WIDTH: 85%;BORDER-BOTTOM: black 1px solid;POSITION: relative;BORDER-LEFT: black 1px solid;line-height: 23px;float:left\">" +
                        "<span style=\"BACKGROUND-COLOR: #008000;width: "+totalpercomplete+"%;display: inline-block;text-align: center;\">&nbsp;</span>" +
                        "<span style=\"BACKGROUND-COLOR: #f0bcbc;width: "+totalpernotcomplete+"%;display: inline-block;text-align: center;\">&nbsp;</span>" +
                        "</div>" +
                        "<div style=\"BORDER-TOP: black 1px solid;BORDER-RIGHT: black 1px solid;WIDTH: 15%;BORDER-BOTTOM: black 1px solid;POSITION: relative;BORDER-LEFT: 0;line-height: 23px;display:inline-block;text-align: right;font-weight: bold;padding-right:5px;\">"+totalpercomplete+"%</div>" +
                        "</div>" +
                        "</div>");
                    $(nRow).append(appendTotalResult);
                }
            }
        });


}

    //load table status report
    self.compliance_statusreport_table = function () {
        var DeptTitle_Name="";
        if ($("#drpdeptid option:selected").val() == '999999'){
            DeptTitle_Name="All Control Points";
        }else{
            DeptTitle_Name=$("#drpdeptid option:selected").text().substring($("#drpdeptid option:selected").text().indexOf("-")+1)
        }
        $("#statusreport_titlecolum1").text(DeptTitle_Name);
        $("#statusreport_titlecolum2").text($('#reportdate').val());


        var responsiveHelper_dt_compliance_statusreport = undefined;
        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };

        //Load status report table
        $('#dt_compliance_statusreport').DataTable().clear().destroy();
        $('#dt_compliance_statusreport tfoot > tr > td').remove();
        var oTable = $('#dt_compliance_statusreport').DataTable({
            "autoWidth": false,
            "bFilter" : false,
            "infoEmpty": "No records available",
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "paging": false,
            "ordering": false,
            "bInfo": false,
            language: {
                processing: "<img src='assets/images/loading.gif' alt='loading'> Processing...",
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": base_url + 'compliance/ajax_listcompliance_statusreport',
                "type": "POST",
                "data" : {
                    "reportdate" :$('#reportdate').val(),
                    "deptid" : $("#drpdeptid option:selected").val(),
                    "busunit" :$("#drpbusunit option:selected").val()
                },
                "error":function(err,xhr){
                    alert('There was a problem loading compliance status report items. Please try reloading the page.');
                }
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_dt_compliance_statusreport) {
                    responsiveHelper_dt_compliance_statusreport = new ResponsiveDatatablesHelper($('#dt_compliance_statusreport'), breakpointDefinition);
                }
            },
            "rowCallback": function( row, sData, index ) {
                responsiveHelper_dt_compliance_statusreport.createExpandIcon(row);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_compliance_statusreport.respond();
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            {    "orderable": false,"order": [],"class": "","targets": 0, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html('<span id="linkstatusreport" style="color: blue;text-decoration: underline;cursor: pointer;">'+oData[0].substring(oData[0].indexOf("_")+1)+'</span>');

                $(nTd).on('click',"#linkstatusreport", function(e) {
                    self.compliance_detailreport_table(oData[5]);
                    $('.nav-tabs a[href="#hr3"]').tab('show');
                });

            } },
            {   "class": "text-right","targets": 1,"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                
                $(nTd).html(oData[1]+oData[4]);
                
            }},
            {   "class": "text-right","targets": 2 },
            {   "class": "text-right","targets": 3}
            ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                $('#dt_compliance_statusreport tfoot > tr > td').remove();

                var totalcomplete = 0;
                for ( var i=0 ; i<aaData.length ; i++ )
                {
                    totalcomplete += aaData[i][1]*1;
                    totalcomplete += aaData[i][4]*1;
                }

                var totalnotverified = 0;
                for ( var i=0 ; i<aaData.length ; i++ )
                {
                    totalnotverified += aaData[i][2]*1;
                }

                var totalselected = 0;
                for ( var i=0 ; i<aaData.length ; i++ )
                {
                    totalselected += aaData[i][3]*1;
                }

                var totalpercomplete = parseFloat(totalcomplete/totalselected * 100);
                if (totalpercomplete != 0 && totalpercomplete !=100) {
                    totalpercomplete=totalpercomplete.toFixed(2);
                }

                var totalpernotcomplete = parseFloat(totalnotverified/totalselected * 100);
                if (totalpernotcomplete != 0 && totalpernotcomplete !=100 ) {
                    totalpernotcomplete=totalpernotcomplete.toFixed(2);
                }

                if (iEnd > 0) {
                    var td0 = document.createElement('td');
                    $(td0).html('<span><strong>Total '+ DeptTitle_Name+'</strong></span>');
                    $(nRow).append(td0);

                    var td1 = document.createElement('td');
                    $(td1).html('<div class="text-right"><strong>'+(totalcomplete + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+'<br>'+totalpercomplete+'%</strong></div>');
                    $(nRow).append(td1);

                    var td2 = document.createElement('td');
                    $(td2).html('<div class="text-right"><strong>'+(totalnotverified + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+'<br>'+totalpernotcomplete+'%</strong></div>');
                    $(nRow).append(td2);

                    var td3 = document.createElement('td');
                    $(td3).html('<div class="text-right"><strong>'+(totalselected + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+'<br>100%</strong></div>');
                    $(nRow).append(td3);
                }
            }
        });
}

    //load table detail report
    self.compliance_detailreport_table = function ($deptcd) {
        $("#detailreport_reportdate").text($('#reportdate').val());

        var responsiveHelper_dt_compliance_detailreport = undefined;
        var breakpointDefinition = {
            tablet : 1024,
            phone : 480
        };

        //Load detail report table
        $('#dt_compliance_detailreport').DataTable().clear().destroy();
        $('#dt_compliance_detailreport tfoot > tr > td').remove();
        var oTable = $('#dt_compliance_detailreport').DataTable({
            "autoWidth": false,
            "bFilter" : false,
            "infoEmpty": "No records available",
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            //"fixedHeader": true,
            //"scrollX":  '100%',
            //"scrollY":  '60vh',
            //"scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "bInfo": false,
            language: {
                processing: "<img src='assets/images/loading.gif' alt='loading'> Processing...",
            },
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": base_url + 'compliance/ajax_listcompliance_detailreport',
                "type": "POST",
                "data" : {
                    "reportdate" :$('#reportdate').val(),
                    "deptid" : $deptcd,
                    "busunit" :$("#drpbusunit option:selected").val()
                },
                "error":function(err,xhr){
                    alert('There was a problem loading compliance detail report items. Please try reloading the page.');
                }
            },
            "preDrawCallback" : function() {
                // Initialize the responsive datatables helper once.
                if (!responsiveHelper_dt_compliance_detailreport) {
                    responsiveHelper_dt_compliance_detailreport = new ResponsiveDatatablesHelper($('#dt_compliance_detailreport'), breakpointDefinition);
                }
            },
            "rowCallback": function( row, sData, index ) {
                responsiveHelper_dt_compliance_detailreport.createExpandIcon(row);
            },
            "drawCallback" : function(oSettings) {
                responsiveHelper_dt_compliance_detailreport.respond();
            },
            //Set column definition initialisation properties.
            "columnDefs": [
            {    "orderable": false,"order": [],"class": "","targets": 0, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {

                if(oData[6]=='level1'){
                    $(nTd).css('font-weight', 'bold');
                    $(nTd).css('background-color', '#a2d1ea');
                    $(nTd).html('DEPT ID '+ oData[5] +' '+sData.substring(sData.indexOf("_")+1).toUpperCase());
                }
                if(oData[6]=='level2'){
                    $(nTd).html('<span id="linkdetailreport" style="color: blue;text-decoration: underline;cursor: pointer;padding-left: 20px">'+sData.substring(sData.indexOf("_")+1)+'</span>');
                }
                if(oData[6]=='Total'){
                    $(nTd).css('padding-left', '30px');
                    $(nTd).css('font-weight', 'bold');
                    $(nTd).css('border-bottom', '1px solid #333');
                    $(nTd).css('vertical-align', 'middle');
                    $(nTd).html('TOTAL '+sData.substring(sData.indexOf("_")+1).toUpperCase());
                }
                $(nTd).on('click',"#linkdetailreport", function(e) {
                    ShowBusy();
                    window.location.href = base_url + 'glverification?deptid='+ oData[5];
                });

            } },
            {  "class": "text-right", "targets": 1, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                if(oData[6]=='level1'){
                    $(nTd).css('background-color', '#a2d1ea');
                    $(nTd).html('');
                }
                if(oData[6]=='level2') {
                        //$(nTd).css('font-weight', 'bold');
                        $(nTd).html(oData[1]+oData[4]);                        
                    }
                    if(oData[6]=='Total'){
                        var detailreport_percomplete = parseFloat(((oData[1]+oData[4])/oData[3]*100));
                        if (detailreport_percomplete!== 0 && detailreport_percomplete !=100) {
                            detailreport_percomplete=detailreport_percomplete.toFixed(2);
                        }
                        if(oData[3]==0){
                            detailreport_percomplete=100;
                        }
                        $(nTd).css('font-weight', 'bold');
                        $(nTd).css('background-color', '#fff');
                        $(nTd).css('border-top', '1px solid #999');
                        $(nTd).css('border-bottom', '1px solid #333');
                        $(nTd).html(((oData[1]+oData[4]) + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+"<br>"+detailreport_percomplete+"%");
                    }
                } },
                {  "class": "text-right","targets": 2, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                 
                    if(oData[6]=='level1'){
                        $(nTd).css('background-color', '#a2d1ea');
                        $(nTd).html('');
                    }
                    if(oData[6]=='level2') {
                        $(nTd).html(oData[2]);
                    }
                    if(oData[6]=='Total'){
                        var detailreport_pernotverified = parseFloat((oData[2]/oData[3]*100));
                        if (detailreport_pernotverified!== 0 && detailreport_pernotverified !=100) {
                            detailreport_pernotverified=detailreport_pernotverified.toFixed(2);
                        }
                        if(oData[3]==0){
                            detailreport_pernotverified=0;
                        }
                        $(nTd).css('font-weight', 'bold');
                        $(nTd).css('background-color', '#fff');
                        $(nTd).css('border-top', '1px solid #999');
                        $(nTd).css('border-bottom', '1px solid #333');
                        $(nTd).html((oData[2] + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+"<br>"+detailreport_pernotverified+"%");
                    }

                } },
                {  "class": "text-right","targets": 3, "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                    if(oData[6]=='level1'){
                        $(nTd).css('background-color', '#a2d1ea');
                        $(nTd).html('');
                    }
                    if(oData[6]=='level2') {
                       //$(nTd).css('font-weight', 'bold');
                   }
                   if(oData[6]=='Total'){
                    $(nTd).css('font-weight', 'bold');
                    $(nTd).css('background-color', '#fff');
                    $(nTd).css('border-top', '1px solid #999');
                    $(nTd).css('border-bottom', '1px solid #333');
                    $(nTd).html((sData + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")+"<br>100%");
                }
            } },
            ],
            "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
                if ($deptcd == '999999') {
                    var totalcomplete = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        if (aaData[i][6]=='level2') {
                            totalcomplete += aaData[i][1] * 1;
                            totalcomplete += aaData[i][4] * 1;
                        }
                    }

                    var totalnotverified = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        if (aaData[i][6]=='level2') {
                            totalnotverified += aaData[i][2] * 1;
                        }
                    }

                    var totalselected = 0;
                    for (var i = 0; i < aaData.length; i++) {
                        if (aaData[i][6]=='level2') {
                            totalselected += aaData[i][3] * 1;
                        }
                    }

                    var totalpercomplete = parseFloat(totalcomplete / totalselected * 100);
                    if (totalpercomplete != 0 && totalpercomplete != 100) {
                        totalpercomplete = totalpercomplete.toFixed(2);
                    }

                    var totalpernotcomplete = parseFloat(totalnotverified / totalselected * 100);
                    if (totalpernotcomplete != 0 && totalpernotcomplete != 100) {
                        totalpernotcomplete = totalpernotcomplete.toFixed(2);
                    }
                    if(totalselected==0){
                        totalpercomplete==100;
                        totalpernotcomplete==0;
                    }

                    if (iEnd > 0) {
                        var td0 = document.createElement('td');
                        $(td0).html('<span><strong>TOTAL - ALL UCSF</strong></span>');
                        $(nRow).append(td0);

                        var td1 = document.createElement('td');
                        $(td1).html('<div class="text-right"><strong>' + (totalcomplete + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + '<br>' + totalpercomplete + '%</strong></div>');
                        $(nRow).append(td1);

                        var td2 = document.createElement('td');
                        $(td2).html('<div class="text-right"><strong>' + (totalnotverified + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + '<br>' + totalpernotcomplete + '%</strong></div>');
                        $(nRow).append(td2);

                        var td3 = document.createElement('td');
                        $(td3).html('<div class="text-right"><strong>' + (totalselected + "").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + '<br>100%</strong></div>');
                        $(nRow).append(td3);
                    }
                }
            }
        });

}

}
