var GLVerification_filter_management = function () {
    var self = this;

    $(document).ready(function () {
        /***** START: FILTERS *****/
        //load data
        $("#btn_edit_filter").click(function () {
            self.init_load();
            var deptId = $("#lbl-filter-deptCd").text();            
            self.load_filter_site($('#drpFilters').val(),deptId);
            $("#modal_build_filter").modal('toggle');
        });
        //close modal - reset data
        $("#btn-close-filters").click(function () {
            self.reset_data();
            self.clear_action();
        });
        //onchange drodownlist My Filters
        $('#select-filter-name').on('change', function () {
            self.enableAllFilter();
            var deptId = $("#lbl-filter-deptCd").text();
            self.clear_action();
            self.load_data_ddl(deptId, this.value);
            self.load_filter_site($('#select-filter-name').val(),deptId);
        });
        //button clear - form confirm clear
        $("#btn-filter-clear").click(function () {
            $("#modal_confirm_clear_filter").modal('toggle');
        });
        $("#btn-yes-clear-filter").click(function () {
            self.clear_action();
            $("#modal_confirm_clear_filter").modal('toggle');
        });
        //button DELETE
        $("#btn-filter-delete").click(function () {
            $("#modal_confirm_delete_filter").modal('toggle');
        });
        $("#btn-yes-delete-filter").click(function () {
            var deptId = $("#lbl-filter-deptCd").text();
            var filterId = $("#select-filter-name").val();
            $("#modal_confirm_delete_filter").modal('toggle');
            self.delete_action(deptId, filterId);
        });
        //button SAVE
        $("#btn-filter-save").click(function () {
            var deptId = $("#lbl-filter-deptCd").text();
            var filterId = $("#select-filter-name").val();
            self.save_action(deptId, filterId, "save");
        });
        //button SAVE AS
        $("#btn-close-saveAs").click(function () {
            $("#msgErrReq").addClass("dispay-none").removeClass("dispay-block");
        });
        $("#btn-filter-save_as").click(function () {
            $("#txtNewNameFilter").val("");
            $("#modal_save_as_filter").modal('toggle');
        });
        $("#btn-yes-duplicate-filter").click(function () {
            var deptId = $("#lbl-filter-deptCd").text();
            var filterId = $("#txtNewNameFilter").val();
            self.save_action(deptId, filterId, "save");
        });
        $("#btn-ok-filter_saveAs").click(function () {
            ShowBusy();
            var deptId = $("#lbl-filter-deptCd").text();
            var filterId = self.stripHTML($("#txtNewNameFilter").val());
            filterId = $.trim(filterId.replace(/[\*\^\'\%\#\$\+\=\~\`\&\!]/g, ''));
            if (filterId != "" && filterId != null) {
                $.ajax({
                    url: base_url + '/glverification/check_duplicate_filterName',
                    data: { deptId: deptId, filterName: filterId },
                    type: 'POST',
                    dataType: "json",
                    success: function (data) {
                        $("#modal_save_as_filter").modal('toggle');
                        $("#msgErrReq").addClass("dispay-none").removeClass("dispay-block");
                        HideBusy();
                        if (data) {
                            $("#modal_confirm_duplicate_filter").modal('toggle');
                        }
                        else
                            self.save_action(deptId, filterId, "save_as");
                    },
                    error: function (request, status, error) {
                        HideBusy();
                        alert("Error check Filter Name");
                    }
                });
            }
            else {
                HideBusy();
                $("#msgErrReq").removeClass("dispay-none").addClass("dispay-block");
            }
        });
        /***** END: FILTERS *****/

        /***** START: Action project Cd *****/
        $("#btn-add-projectCd").click(function () {
            self.add_action('filter-all-projectCd', 'select-filter-projectCd', "false");
        });
        $("#btn-remove-projectCd").click(function () {
            self.remove_action('filter-all-projectCd', 'select-filter-projectCd');
        });
        $("#btn-not-projectCd").click(function () {
            self.not_action('filter-all-projectCd', "");
        });
        $("#filter-all-projectCd").on("click", "div", function (event) {
            self.select_elem("filter-all-projectCd", event.target.id);
        });
        /***** END: Action project Cd *****/

        /***** START: Action Func Cd *****/
        $("#btn-add-funcCd").click(function () {
            self.add_action('filter-all-funcCd', 'select-filter-funcCd', "false");
        });
        $("#btn-remove-funcCd").click(function () {
            self.remove_action('filter-all-funcCd', 'select-filter-funcCd');
        });
        $("#btn-not-funcCd").click(function () {
            self.not_action('filter-all-funcCd', "");
        });
        $("#filter-all-funcCd").on("click", "div", function (event) {
            self.select_elem("filter-all-funcCd", event.target.id);
        });
        /***** END: Action Func Cd *****/

        /***** START: Action Project Mgr *****/
        $("#btn-add-projectMgr").click(function () {
            self.add_action('filter-all-projectMgr', 'select-filter-projectMgr', "true");
        });
        $("#btn-remove-projectMgr").click(function () {
            self.remove_action('filter-all-projectMgr', 'select-filter-projectMgr');
        });
        $("#btn-not-projectMgr").click(function () {
            self.not_action('filter-all-projectMgr', "true");
        });
        $("#filter-all-projectMgr").on("click", "div", function (event) {
            self.select_elem("filter-all-projectMgr", event.target.id);
        });
        /***** END: Action Project Mgr *****/

        /***** START: Action Project Use *****/
        $("#btn-add-projectUse").click(function () {
            self.add_action('filter-all-projectUse', 'select-filter-projectUse', "false");
        });
        $("#btn-remove-projectUse").click(function () {
            self.remove_action('filter-all-projectUse', 'select-filter-projectUse');
        });
        $("#btn-not-projectUse").click(function () {
            self.not_action('filter-all-projectUse', "");
        });
        $("#filter-all-projectUse").on("click", "div", function (event) {
            self.select_elem("filter-all-projectUse", event.target.id);
        });
        /***** END: Action Project Use *****/
    });

/***** START: LOAD data *****/
self.init_load = function () {
    self.get_deptId('select-filter-name');
    self.load_funcCd();
    self.load_projectUse();
}

    // Generate select box data
    self.select_box_generate = function (listData, idSelect, nameValue, nameText, selectedValue) {
        var select = document.getElementById(idSelect);
        select.innerHTML = "";
        listData.forEach(function (element) {
            var option = document.createElement("option");
            option.value = element[nameValue];
            option.text = element[nameText];
            if (selectedValue == element.FilterName0)
                option.selected = true;
            select.appendChild(option);
        }, this);
    };

    // Get dept Id by Default
    self.get_deptId = function (idSelect) {
        $("#loadingModal").modal('show');
        var selectedDeptCd = $("#drpdeptid option:selected").val();
        var filterSelected = "";
        if (idSelect != "drpFilters") {
            filterSelected = $("#drpFilters").val();
            self.load_projectCd(selectedDeptCd);
            self.load_projectMgr(selectedDeptCd);
        }
        self.load_myfilters(selectedDeptCd, idSelect, filterSelected);
        $("#lbl-filter-deptCd").text(selectedDeptCd);
    };

    // Load data for dropdown list 
    self.load_data_ddl = function (deptId, filterId) {
        $("#loadingModal").modal('show');
        $.ajax({
            url: base_url + '/glverification/get_filter_data_ddl',
            data: { deptId: deptId, filterId: filterId },
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if (data && Object.keys(data).length != 0) {
                    data.forEach(function (element) {
                        var selectId = element.Value;
                        if (element.Type == "ProjectManagerCd")
                            selectId = element.ProMgrId;
                        var containId = self.swich_id_containBox(element.Type);
                        var ddlId = self.swich_id_ddlFilter(element.Type);
                        if (containId != "")
                            self.element_ddl_generate(ddlId, containId, selectId, element.Value, element.not_value);
                    }, this);

                }
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                }, 1000)

            },
            error: function (request, status, error) {
                alert("Load data for ddl fail!");
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                }, 1000)
            }
        });
    };

    // Switch Id for ddl filter
    self.swich_id_ddlFilter = function (key) {
        var value = "";
        switch (key) {
            case "ProjectCd": value = "select-filter-projectCd"; break;
            case "FundCd": value = "select-filter-funcCd"; break;
            case "ProjectManagerCd": value = "select-filter-projectMgr"; break;
            case "ProjectUseShort": value = "select-filter-projectUse"; break;
        }
        return value;
    };
    // Switch Id for contain box
    self.swich_id_containBox = function (key) {
        var value = "";
        switch (key) {
            case "ProjectCd": value = "filter-all-projectCd"; break;
            case "FundCd": value = "filter-all-funcCd"; break;
            case "ProjectManagerCd": value = "filter-all-projectMgr"; break;
            case "ProjectUseShort": value = "filter-all-projectUse"; break;
        }
        return value;
    };
    // Switch contain box for Id
    self.swich_containBox_id = function (key) {
        var value = "";
        switch (key) {
            case "filter-all-projectCd": value = "ProjectCd"; break;
            case "filter-all-funcCd": value = "FundCd"; break;
            case "filter-all-projectMgr": value = "ProjectManagerCd"; break;
            case "filter-all-projectUse": value = "ProjectUseShort"; break;
        }
        return value;
    };

    // Load list filters data
    self.load_myfilters = function (deptIdSubmtit, idSelect, filterSelected) {
        $("#loadingModal").modal('show');
        $.ajax({
            url: base_url + '/glverification/get_list_filters',
            data: { deptId: deptIdSubmtit },
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if (data && Object.keys(data).length != 0) {
                    self.select_box_generate(data, idSelect, "FilterName0", "FilterName0", filterSelected);
                }
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                }, 1000)
            },
            error: function (request, status, error) {
                //alert("Load Filters fail!");
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                }, 1000)
            }
        });
    };

    // Load site data for filter
    self.load_filter_site = function (filterId,deptId) {   
     
        $.ajax({
            url: base_url + '/glverification/get_filters_site',
            data: { filterId: filterId,deptId:deptId},
            type: 'POST',
            dataType: "json",
            success: function (data) {
                
                $("#drpsite").val(data);

            },
            error: function (request, status, error) {
                alert("Load Site data fail!");
            }
        });
    };

    // Load project CD
    self.load_projectCd = function (deptId) {
        $.ajax({
            url: base_url + '/glverification/get_filters_projectCd',
            data: { deptId: deptId },
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if (data && Object.keys(data).length != 0) {
                    self.select_box_generate(data, "select-filter-projectCd", "ProjectCd", "ProjectTitleCd", "");
                    //search filter
                    $("#select-filter-projectCd").SumoSelect({
                        csvDispCount: 2, search: true, searchText: 'Enter here',
                        placeholder: 'Select Values', showTitle : false ,
                    });
                    $('#select-filter-projectCd')[0].sumo.reload();
                    //fix wave errors
                    self.fixWaveErrSumoSelect("form-filter-projCd");
                }

            },
            error: function (request, status, error) {
                alert("Load Project Cd fail!");
            }
        });
    };

    // Load Function CD
    self.load_funcCd = function () {
        $.ajax({
            url: base_url + '/glverification/get_filters_funcCd',
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if (data && Object.keys(data).length != 0) {
                    self.select_box_generate(data, "select-filter-funcCd", "FundCd", "FundTreeTitleShort", "");
                    //search filter
                    $("#select-filter-funcCd").SumoSelect({
                        csvDispCount: 2, search: true, searchText: 'Enter here',
                        placeholder: 'Select Values', showTitle : false,
                    });
                    $('#select-filter-funcCd')[0].sumo.reload();
                    //load box
                    self.load_data_ddl($("#drpdeptid option:selected").val(), $("#drpFilters").val());
                    //fix wave errors
                    self.fixWaveErrSumoSelect("form-filter-funcCd");
                }
            },
            error: function (request, status, error) {
                alert("Load Func Cd fail!");
            }
        });
    };

    // Load projetc Manager
    self.load_projectMgr = function (deptId) {
        $.ajax({
            url: base_url + '/glverification/get_filters_projectMgr',
            data: { deptId: deptId },
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if (data && Object.keys(data).length != 0) {
                    self.select_box_generate(data, "select-filter-projectMgr", "ProjectManagerCd", "ProjectManager", "");
                    $("#select-filter-projectMgr").SumoSelect({
                        csvDispCount: 2, search: true, searchText: 'Enter here',
                        placeholder: 'Select Values', showTitle : false,
                    });
                    $('#select-filter-projectMgr')[0].sumo.reload();
                    //fix wave errors
                    self.fixWaveErrSumoSelect("form-filter-projectMgr");
                }

            },
            error: function (request, status, error) {
                alert("Load Project Mgr fail!");
            }
        });
    };

    // Load project use title
    self.load_projectUse = function () {
        $.ajax({
            url: base_url + '/glverification/get_filters_projectUse',
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if (data && Object.keys(data).length != 0) {
                    self.select_box_generate(data, "select-filter-projectUse", "ProjectUseShort", "ProjectUseTitle", "");
                    $("#select-filter-projectUse").SumoSelect({
                        csvDispCount: 2, search: true, searchText: 'Enter here',
                        placeholder: 'Select Values', showTitle : false,
                    });
                    $('#select-filter-projectUse')[0].sumo.reload();
                    //fix wave errors
                    self.fixWaveErrSumoSelect("form-filter-projectUse");
                }
            },
            error: function (request, status, error) {
                alert("Load Project Use fail!");
            }
        });
    };

    self.fixWaveErrSumoSelect= function(formId) {
        var span = "<span style='display:none;'>txthidden</span>";
        $("#" +formId+" .SumoSelect p label").append(span);
        $("#" +formId+" .SumoSelect p input").attr('aria-label','lblprojectUse');
    }
    /***** END: LOAD data *****/

    /***** START: Data Processing: SAVE_AS/SAVE/DELETE/CLEAR *****/
    self.save_action = function (deptId, filterId, type) {
        $("#loadingModal").modal('show');
        var listItems = self.get_value_all_box();
        $.ajax({
            url: base_url + '/glverification/save_filters',
            data: { deptId: deptId, filterName: filterId, listItems: listItems, type: type },
            type: 'POST',
            dataType: "json",
            success: function (data) {
                glv_filter.get_deptId('drpFilters');
                self.reset_data();
                self.clear_action();
                alert(data);
                $("#modal_confirm_duplicate_filter").modal('hide');
                $("#modal_save_as_filter").modal('hide');
                $("#modal_build_filter").modal('hide');
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                    $("#drpFilters").val(filterId.toString()).attr("selected", "selected");
                }, 1000)
            },
            error: function (request, status, error) {
                alert("Error save exist record!");
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                }, 1000)
            }
        });
    };
    self.delete_action = function (deptId, seletedFilter) {
        $("#loadingModal").modal('show');
        $.ajax({
            url: base_url + '/glverification/delete_filters',
            data: { deptId: deptId, filterName: seletedFilter },
            type: 'POST',
            dataType: "json",
            success: function (data) {
                self.clear_action();
                self.init_load();
                glv_filter.get_deptId('drpFilters');
                alert(data);
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                }, 1000)
            },
            error: function (request, status, error) {
                alert("Error delete exist record!");
                setTimeout(function () {
                    $("#loadingModal").modal('hide');
                }, 1000)
            }
        });
    };
    self.clear_action = function () {
        self.enableAllFilter();
        $('#drpsite').val("(any)");
        document.getElementById("filter-all-projectCd").innerHTML = "";
        document.getElementById("filter-all-funcCd").innerHTML = "";
        document.getElementById("filter-all-projectMgr").innerHTML = "";
        document.getElementById("filter-all-projectUse").innerHTML = "";
    };
    /***** END: Data Processing: SAVE_AS/SAVE/DELETE/CLEAR *****/

    /***** START: RESET MODAL *****/
    self.reset_data = function () {
        document.getElementById("select-filter-name").innerHTML = "";
        document.getElementById("select-filter-projectCd").innerHTML = "";
        document.getElementById("select-filter-funcCd").innerHTML = "";
        document.getElementById("select-filter-projectMgr").innerHTML = "";
        document.getElementById("select-filter-projectUse").innerHTML = "";
        document.getElementById("lbl-filter-deptCd").innerHTML = "";
    };
    /***** END: RESET MODAL *****/

    /***** START: Action Processing *****/
    self.get_all_id_value_box = function (containId) {
        var listItems = [];
        var divContain = document.getElementById(containId).children;
        if (divContain.length > 0) {
            for (var i = 0; i < divContain.length; i++) {
                var divId = divContain[i].getAttribute("id");
                listItems.push(divId);
            }
        }
        return listItems;
    };

    // Get value for all dropdown list
    self.get_value_all_box = function () {
        var listItems = [];
        var arrBoxId = ["filter-all-projectCd", "filter-all-funcCd", "filter-all-projectMgr", "filter-all-projectUse"];
        arrBoxId.forEach(function (element) {
            listItems.push.apply(listItems, self.get_value_box(element));
        }, this);
        // add site data for filter
        var data = {};
        data.Type = "Site";
        data.Value = $("#drpsite option:selected").val();
        data.Except = "";
        listItems.push(data);
        return listItems;
    };

    // Get value for dropdown list
    self.get_value_box = function (containId) {
        var list = [];
        var divContain = document.getElementById(containId).children;
        if (divContain.length > 0) {
            for (var i = 0; i < divContain.length; i++) {
                var data = {}
                data.Type = self.swich_containBox_id(containId);
                data.Value = divContain[i].getAttribute("id");
                if (containId == "filter-all-projectMgr") {
                    data.Value = divContain[i].innerHTML.trim();
                    lastStr = data.Value.substr(data.Value.length - 6);
                    if (lastStr === " (not)")
                        data.Value = data.Value.substr(0, data.Value.length - 6);
                }
                var notVal = divContain[i].getAttribute("fil-not");
                if (notVal == "true")
                    data.Except = "-";
                else if (notVal == "false")
                    data.Except = "+";

                list.push(data);
            }
        }
        return list;
    };

    // Get selected box
    self.get_selected_box = function (containId) {
        var id = "";
        var divContain = document.getElementById(containId).children;
        if (divContain.length > 0) {
            for (var i = 0; i < divContain.length; i++) {
                var divSelected = divContain[i].getAttribute("fil-select");
                if (divSelected == "true")
                    id = divContain[i].getAttribute("id");
            }
        }
        return id;
    };

    // Reset selected box
    self.reset_selected = function (containId) {
        var divContain = document.getElementById(containId).children;
        if (divContain.length > 0) {
            for (var i = 0; i < divContain.length; i++) {
                divContain[i].setAttribute("fil-select", "false");
                divContain[i].classList.remove("filter_active");
            }
        }
    };

    // Set select element
    self.select_elem = function (containId, selectedId) {
        self.reset_selected(containId);
        $("#" + containId + " #" + selectedId).attr("fil-select", "true").addClass("filter_active");
    };

    // Add action for dropdown list
    self.add_action = function (containId, ddlId, isProMgr) {
        var listItems = [];
        $('#' + ddlId + ' option:selected').each(function (i) {
            listItems.push($(this).val());
            //disable filter
            $('#' + ddlId)[0].sumo.disableItem($("#" + ddlId + " option[value='" + $(this).val() + "']").index());
        });
        var arr = self.get_all_id_value_box(containId);

        //grep and inArray of jquery to check common in lists
        $.grep(listItems, function (element) {
            if ($.inArray(element, arr) === -1) {
                var txtContent = element;
                if (isProMgr == "true") {
                    txtContent = $("#" + ddlId + " option[value='" + element + "']").text();
                }
                self.element_ddl_generate(ddlId, containId, element, txtContent, "false");
            }
        });
    };

    // Generate dropdown list element
    self.element_ddl_generate = function (ddlId, containId, selectId, txtContent, notValue) {
        var divContain = document.getElementById(containId);
        var div = document.createElement("div");
        var text = txtContent;
        if (notValue == "true")
            text = txtContent + " (not)";
        div.setAttribute("id", selectId);
        div.setAttribute("fil-not", notValue);
        div.setAttribute("fil-select", "false");
        div.innerHTML = text;
        divContain.appendChild(div);
        //disable filter
        $('#' + ddlId)[0].sumo.disableItem($("#" + ddlId + " option[value='" + selectId + "']").index());
    };

    // Remove action in dropdown list
    self.remove_action = function (containId, ddlId) {
        var id = self.get_selected_box(containId);
        if (id != "") {
            $("#" + containId + " #" + id).remove();
            //enable filter
            $('#' + ddlId)[0].sumo.enableItem($("#" + ddlId + " option[value='" + id + "']").index());
        }

    };

    // Set data for not action
    self.not_action = function (containId, isProMgr) {
        var txtContent = "";
        var id = self.get_selected_box(containId);
        txtContent = id;
        if (isProMgr == "true")
            txtContent = $("#select-filter-projectMgr option[value='" + id + "']").text();
        if (id != "") {
            var isNot = $("#" + containId + " #" + id).attr("fil-not");
            if (isNot == "true") {
                $("#" + containId + " #" + id).attr("fil-not", "false");
                $("#" + containId + " #" + id).text(txtContent);
            } else if (isNot == "false") {
                $("#" + containId + " #" + id).attr("fil-not", "true");
                $("#" + containId + " #" + id).text(txtContent + " (not) ");
            }
        }
    };

    self.stripHTML = function (str) {
        var strippedText = $("<div/>").html(str).text();
        return strippedText;
    };

    /***** END: Action Processing *****/

    self.enableAllFilter = function () {
        $(".body-filter .optWrapper.multiple")
        var arrDdl = ["select-filter-projectCd","select-filter-funcCd","select-filter-projectMgr","select-filter-projectUse"];
        

        for (let index = 0; index < arrDdl.length; index++) {
            const ddlId = arrDdl[index];
            var listDisOpts = $("#" + ddlId + " option[disabled]");
            
            if (listDisOpts.length > 0) {
                for (let index = 0; index < listDisOpts.length; index++) {
                    var element = listDisOpts[index];
                    $('#' + ddlId)[0].sumo.enableItem($("#" + ddlId + " option[value='" + element.value + "']").index());
                }
            }
        }
    }
}