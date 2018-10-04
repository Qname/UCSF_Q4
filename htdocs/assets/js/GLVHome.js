var GLVHomeManagement = function() {
    var self = this;

    self.init_glvhome = function() {};
    
    $(document).ready(function(){
        /***** DEPTID: finishes typing instead of on key up *****/
        var typingTimer = 0;                
        var doneTypingInterval = 1500;
        var $input = $('#txtDeptId');

        /***** CONTROL POINT: will change Rollup *****/
        $('select#ddl_points').on('change', function() {
            var deptCd = this.value;
            var url = 'glvhome/get_rollUps';
            clearTimeout(typingTimer);
            self.ajaxCallUpdateRollup(deptCd, url, "point");
            setTimeout(function () {
                $('#error_dept').text("");
            }, 3100);
            
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
            $('#btnSubmitHome').prop('disabled', true);
            $('#btnSaveAsDefault').prop('disabled', true);
            clearTimeout(typingTimer);
            typingTimer = setTimeout(self.doneTyping, doneTypingInterval);
        }); 
        
        //on keyup, start the countdown
        $input.on('keyup', function () {            
            $('#btnSubmitHome').prop('disabled', true);
            $('#btnSaveAsDefault').prop('disabled', true);
            clearTimeout(typingTimer);
            typingTimer = setTimeout(self.doneTyping, doneTypingInterval);
        });
        /***** DEPTID: finishes typing instead of on key up *****/

        /***** ROLL UP: will change deptId *****/
        $('select#ddl_rollup').on('change', function() {
            document.getElementById("txtDeptId").value = this.value;
            $('#error_dept').text("");
            $('#btnSubmitHome').prop('disabled', false);
            $('#btnSaveAsDefault').prop('disabled', false);
        });
        /***** ROLL UP: will change deptId *****/

        /***** SUBMIT HOME *****/
        $("#btnSubmitHome").click(function() {
            ShowBusy();
            self.ajaxCallRedirectVerification();
        });
        /***** SUBMIT HOME *****/

        /***** SAVE AS DEFAULT *****/
        $('#btnSaveAsDefault').click(function() {
            $("#loadingModal").modal('toggle');
            self.ajaxSaveDefault();
        });
        /***** SAVE AS DEFAULT *****/
    });

    /***** ajax call function update roll up *****/
    self.ajaxCallUpdateRollup = function(deptCd, url, type) {
        $("#loadingModal").modal('toggle');
        $.ajax({ 
            url: url,
            data: {deptCd: deptCd },
            type: 'POST',
            dataType: "JSON",
            success: function(data) {
                if(data != null && data != "") {
                    var select = document.getElementById("ddl_rollup");
                    select.innerHTML = "";
                    data.forEach(function(element) {
                        self.createOption(element.DeptCd, element.DeptTreeTitleAbbrev, select);
                    }, this);
                    $('#error_dept').text("");
                    if(type == "point")
                        $('#txtDeptId').val($('select#ddl_rollup').val());

                    $('select#ddl_rollup').val($.trim($('#txtDeptId').val()));
                    $('select#ddl_points').val(data[0].DeptCd);
                    $('#btnSubmitHome').prop('disabled', false);
                    $('#btnSaveAsDefault').prop('disabled', false);

                }
                else
                {       
                    $('#error_dept').text("You must enter a valid department code.");
                }
                $("#loadingModal").modal('toggle');
            },
            error: function (request, status, error) {
                $("#loadingModal").modal('toggle');
                $('#error_dept').text("You must enter a valid department code.");
            }
        });
        setTimeout(function () {
            $('#txtDeptId').focus();
        }, 1600);        
    }
    /***** ajax call function update roll up *****/

    /***** create option element for select *****/
    self.createOption = function(value, text, select) {
        var option = document.createElement("option");
        option.innerHTML = text;
        option.value = value;
        select.appendChild(option);
    }
    /***** create option element for select *****/

    /***** finish typing, do sth *****/
    self.doneTyping = function () {
        var deptId = $('#txtDeptId').val();
        var url = 'glvhome/get_rollUp_with_deptId';
        if(deptId.length==6)
            self.ajaxCallUpdateRollup(deptId,url, "deptId");
    }
    /***** finish typing, do sth *****/

    /***** button SUBMIT *****/
    self.ajaxCallRedirectVerification = function() {
        var cp = $("#ddl_points").val();
        var deptId = $("#txtDeptId").val();
        localStorage.setItem('ChosenDeptLevel2Cd',deptId);
        var rollup = $("#ddl_rollup").val();

        $.ajax({ 
            url: 'glvhome/redirect_verification',
            data: { controlPoint: cp, deptId: deptId, rollUp: rollup },
            type: 'POST',
            dataType: "json",
            success: function(data) {
                window.location.href = base_url + 'glverification?deptid='+ data.deptId;
            },
            error: function (request, status, error) {
                alert("Error");
                HideBusy();
            }
        });
    }
    /***** button SUBMIT *****/

    /***** button SAVE as MY DEFAULT *****/
    self.ajaxSaveDefault = function() {
        var cp = $("#ddl_points").val();
        var deptId = $("#txtDeptId").val();
        var rollup = $("#ddl_rollup").val();

        $.ajax({ 
            url: 'glvhome/save_as_default',
            data: { controlPoint: cp, deptId: deptId, rollUp: rollup },
            type: 'POST',
            dataType: "json",
            success: function(data) {
                if(data) {
                    if (deptId == "" || deptId != rollup) {
                        $("#txtDeptId").val(rollup);
                    }
                    alert("Default Saved!");
                }
                $("#loadingModal").modal('toggle');
            },
            error: function (request, status, error) {
                $("#loadingModal").modal('toggle');
            }
        });
    }
    /***** button SAVE as MY DEFAULT *****/
}