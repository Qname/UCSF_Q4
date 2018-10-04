
var GLVerification_MonthlyTrend_Report =  function(){
    var self = this;
     //var previousValue = "";

     self.getUrlMonthlyTrend = function () {
        var deptId = $("#drpdeptid option:selected").val()
        var bu = $("#drpbusunit").val();
        var site = $("#drpsite").val();
        var realPercent = Number(localStorage.getItem('MonthlyPercentage'));
        var dropdownText = $("#drpdeptid option[value="+$("#drpdeptid option:selected").val()+"]").text();
        if( realPercent  == 0){
            $('#monthlyTrendDrop').val('Not Verified');
            
        } else if(realPercent > 0 && realPercent < 100){
            $('#monthlyTrendDrop').val('(multiple)');
            
        } else{
            $('#monthlyTrendDrop').val('Complete');
        }

        $.ajax({ url: base_url + 'glverification/GetUrlReport',
            data: {deptId: deptId, businessUnit: bu, site:site},
            type: 'POST',
            dataType: "json",
            success: function(data) {
                // if (data && data.toString().indexOf("reports2012.medschool.ucsf.edu")!=-1){
                //     $("#monthlyTrendUrl").prop("sandbox","allow-forms allow-pointer-lock allow-popups allow-same-origin allow-scripts");
                // }else{
                //     $("#monthlyTrendUrl").prop("sandbox","allow-forms allow-pointer-lock allow-same-origin allow-scripts ");
                // }
                $("#monthlyTrendUrl").prop("sandbox","allow-forms allow-pointer-lock allow-same-origin allow-scripts ");
                if(!data){
                    document.getElementById('monthlyTrendUrl').src = "https://duckduckgo.com/";
                } else{
                    document.getElementById('monthlyTrendUrl').src =  data.toString();
                }
                
                HideBusy();
            },
            error: function (request, status, error) {
                alert(error);
                HideBusy();
            }
        });
    }
    
    self.onSaveDropdownMonthlyTrend = function(){
        var previousValue = localStorage.getItem('prevMonthlyTrenDropVal');
        var getDrop = localStorage.getItem('currentMonthlyTrenDropVal');
        var deptId = $("#drpdeptid").val();
        var site = $("#drpsite").val();
        //get depCd information
        $.ajax({
            url: base_url + 'glverification/GetDeptCdInformation',
            type:'GET',
            success: function(data){
                if(data){
                    var res = JSON.parse(data);
                    if ( getDrop == '(multiple)' )
                    {
                        alert('You can not change this to multiple');
                        $("#monthlyTrendDrop").val(previousValue);
                        localStorage.setItem('currentMonthlyTrenDropVal',previousValue);
                        $('#monthlyTrendDropBtn').prop('disabled', true);
                        HideBusy();
                    }
                    else if(res[0] && res[0].PostingLevel) // if depcd has posting level
                    {
                        var r = confirm("Are you sure?");
                        if (r == true) {
                            $.ajax({ url: base_url + 'glverification/MonthlyTrendChange',
                                data:  {deptId: deptId, trendStatus: getDrop, site:site},
                                type: 'POST',
                                dataType: "json",
                                success: function(data) {
                                    if (!data){
                                        alert('The current department level is too high for this feature.');
                                        $("#monthlyTrendDrop").val( localStorage.getItem('prevMonthlyTrenDropVal'));
                                        $('#monthlyTrendDropBtn').prop('disabled', true);
                                        localStorage.setItem('currentMonthlyTrenDropVal',localStorage.getItem('prevMonthlyTrenDropVal'));
                                    } else{
                                        alert('Update trend successfully.');
                                        $("#monthly_percentage").val("0%");
                                        $("#monthly_percentage_actual").css("width","0%");
                                        $("#monthly_percentage_remain").css("width","100%");
                                        self.ReloadDeptId();
                                    }
                                    HideBusy();
                                    
                                },
                                error: function (request, status, error) {
                                    alert(error);
                                    HideBusy();
                                }
                            });
                            
                        } else {
                            $("#monthlyTrendDrop").val( localStorage.getItem('prevMonthlyTrenDropVal'));
                            $('#monthlyTrendDropBtn').prop('disabled', true);
                            localStorage.setItem('currentMonthlyTrenDropVal',localStorage.getItem('prevMonthlyTrenDropVal'));
                            HideBusy();
                        }
                    }
                    else
                    {
                        var r = confirm("Note: this will change the child department code status under this parent. Are you sure?");
                        if (r == true) {
                            $.ajax({ url: base_url + 'glverification/MonthlyTrendChange',
                                data:  {deptId: deptId, trendStatus: getDrop, site:site},
                                type: 'POST',
                                dataType: "json",
                                success: function(data) {
                                    if (!data){
                                        alert('The current department level is too high for this feature.');
                                        $("#monthlyTrendDrop").val( localStorage.getItem('prevMonthlyTrenDropVal'));
                                        $('#monthlyTrendDropBtn').prop('disabled', true);
                                        localStorage.setItem('currentMonthlyTrenDropVal',localStorage.getItem('prevMonthlyTrenDropVal'));
                                    } else{
                                        alert('Update trend successfully.');
                                        $("#monthly_percentage").val("100%");
                                        $("#monthly_percentage_actual").css("width","100%");
                                        $("#monthly_percentage_remain").css("width","0%");
                                        self.ReloadDeptId();
                                    }
                                    
                                    HideBusy();
                                },
                                error: function (request, status, error) {
                                    alert(error);
                                    HideBusy();
                                }
                            });
                        } else {
                            $("#monthlyTrendDrop").val( localStorage.getItem('prevMonthlyTrenDropVal'));
                            $('#monthlyTrendDropBtn').prop('disabled', true);
                            localStorage.setItem('currentMonthlyTrenDropVal',localStorage.getItem('prevMonthlyTrenDropVal'));
                            HideBusy();
                        }
                    }
                    
                }
            },
            error:function(req,status, err){
                HideBusy();
            }
        });     

}
     //refresh value on dropdown dep Id
     self.ReloadDeptId= function(){
        ShowBusy();
        $.ajax({
            url: base_url + 'glverification/GetListDepartment',
            async: false,
            method: 'POST',
            data: {selectedDeptId: $("#drpdeptid option:selected").val() },
            success: function (data) {
               var selectedDeptId = $("#drpdeptid option:selected").val() ;
                 //clear current dropdown
                 var dropdownM =  $('#drpdeptid');
                 dropdownM.html('');
                 if(data && JSON.parse(data) && JSON.parse(data).length >0 ){
                    
                    var res = JSON.parse(data);
                    res.forEach(function(elem){
                        if(elem.DeptCd == selectedDeptId){
                            dropdownM.append("<option value='"+elem.DeptCd+"' selected >"
                                +elem.DeptCd+elem.DeptTreeTitleAbbrev
                                +(elem.Checked ? " --- "+elem.Checked:"") 
                                +(elem.UserName ? " --- "+elem.UserName:"")+"</option>"
                                );
                        } else{
                            dropdownM.append("<option value='"+elem.DeptCd+"'>"
                                +elem.DeptCd+elem.DeptTreeTitleAbbrev
                                +(elem.Checked  ? " --- "+elem.Checked:"") 
                                +(elem.UserName ? " --- "+elem.UserName:"")+"</option>"
                                );
                        }
                    });    
                } else {
                    var chosenDeptLevel2Cd = localStorage.getItem('ChosenDeptLevel2Cd');
                    dropdownM.append("<option value='"+chosenDeptLevel2Cd+"'>"+chosenDeptLevel2Cd +"</option>");
                }
                HideBusy();
            },
            error: function (xhr) {
                HideBusy();
            }
        });
    }

    //refresh value on dropdown dep Id base on report date change
    self.ReloadDeptIdBaseOnReportDates= function(year,month){
        $.ajax({
            url: base_url + 'glverification/GetListDepartmentBaseOnReportDate',
            async: false,
            method: 'POST',
            data:{  year: year, month: month},
            dataType: "json",
            success: function (data) {
                var selectedDeptId = $("#drpdeptid option:selected").val() ;
                //clear current dropdown
                var dropdownM =  $('#drpdeptid');
                dropdownM.html('');
                if(data && data.length >0 ){
                    data.forEach(function(elem){
                        if( selectedDeptId && elem.DeptCd == selectedDeptId){
                            dropdownM.append("<option value='"+elem.DeptCd+"' selected >"
                                +elem.DeptCd+elem.DeptTreeTitleAbbrev
                                +(elem.Checked ? " --- "+elem.Checked:"") 
                                +(elem.UserName ? " --- "+elem.UserName:"")+"</option>"
                                );
                        } else{
                            dropdownM.append("<option value='"+elem.DeptCd+"'>"
                                +elem.DeptCd+elem.DeptTreeTitleAbbrev
                                +(elem.Checked ? " --- "+elem.Checked: "") 
                                +(elem.UserName ? " --- "+elem.UserName:"") +"</option>"
                                );
                        }
                    });
                } else{
                    var chosenDeptLevel2Cd = localStorage.getItem('ChosenDeptLevel2Cd');
                    dropdownM.append("<option value='"+chosenDeptLevel2Cd+"'>"+chosenDeptLevel2Cd +"</option>");
                }
                HideBusy();
                
            },
            error: function (xhr) {
                HideBusy();
            }
        });
    }
}