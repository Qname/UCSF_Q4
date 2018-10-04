<script type="text/javascript">
    $(function(){
        var vh= new ComplianceManagement();
        vh.init_Compliance();
    });
</script>

<!-- MAIN CONTENT -->
<div id="content">
    <section id="widget-grid" class="">
        <div class="well headerfilter " style="width:100%;display: inline-block;padding: 2px 2px;">
            <div class="row">
                <div class="col-sm-2">
                    <div style="line-height: 40px;color: #fff; background-color: #6da9a4; font-weight: bold; padding: 0px 10px; vertical-align: middle; width: 100%; box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; -webkit-box-sizing: border-box;">Current Selections</div>
                </div>
                <div class="form-inline col-sm-10" style="line-height: 35px;" mousedown="return false;">
                    <div class="form-group" style="margin-right:10px;">
                        <label for="reportdate">Report Date:</label>
                        <div class="input-group" style="max-width:140px;">
                            <input type="text" readonly="true" class="form-control date-picker col-sm-2" id="reportdate" value="<?= $defaultreportdate ?>" style="width: 90px; background-color: white;"/>
                            <span class="input-group-addon cursor-poiter" id="btnshowreportdate"><span class="glyphicon glyphicon-calendar"></span> </span>
                        </div>
                    </div>
                    <div class="form-group" style="margin-right:10px;">
                        <label for="drpdeptid">Dept ID:</label><select class="form-control input-sm" id="drpdeptid" name="drpdeptid">
                            <?php
                            echo '<option value="999999">ALL CONTROL POINTS</option>';
                            foreach($listControlPoints as $item) {
                             ?>
                             <option value="<?= $item->DeptCd ?>"><?= $item->DeptTitle ?></option>
                             <?php
                         } ?>
                     </select>
                 </div>
                 <div class="form-group" style="margin-right:10px;">
                    <label for="drpbusunit">BU:</label>
                    <select class="form-control input-sm"  id="drpbusunit" >
                        <option value="SFCMP" <?php if($bu == 'SFCMP') echo ' selected="selected"' ?> >SFCMP</option>
                        <option value="SFFDN"  <?php if($bu == 'SFFDN') echo ' selected="selected"' ?> >SFFDN</option>
                    </select>
                </div>
                <button class="btn3d btn-blue btn-c" style="line-height:1.42857143;" type="button" id="btnfilter_compliance">
                    Submit
                </button>
            </div>
        </div>
    </div>
    <div class="jarviswidget" id="wid-id-7" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false" role="widget">
        <header role="heading">
         
            <ul class="nav nav-tabs pull-left in">

                <li id="tabscompliance_dashboard" class="active">
                    <a id="tabscompliance_dashboard_a"  data-toggle="tab" href="#hr1" aria-expanded="true"><span class="hidden-mobile hidden-tablet"> Dashboard</span> </a>
                </li>
                <li id="tabscompliance_statusreport" >
                    <a id="tabscompliance_statusreport_a"  data-toggle="tab" href="#hr2" aria-expanded="false"><span class="hidden-mobile hidden-tablet"> Status Report </span></a>
                </li>
                <li id="tabscompliance_detailsreport">
                    <a id="tabscompliance_detailsreport_a"  data-toggle="tab" href="#hr3" aria-expanded="false"><span class="hidden-mobile hidden-tablet">Detail Report</span></a>
                </li>
            </ul>
            <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span></header>
            <div role="content" style="display: block;">
                <div class="widget-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="hr1">
                            <div class="row">
                                <div class="col-sm-12 maxw800">
                                    <table id="dt_compliance_dashboard" class="table table-hover no-footer dataTable">
                                        <thead>
                                            <tr>
                                                <th data-class="expand"><div class="col-sm-4 text-left" id="titletblcolumn" style="padding-left:0;">All Control Points</div><div class="col-sm-8"><span id="comp_reportdate"></span> - % GL Verifications Completed</div></th>
                                            </tr>
                                        </thead>
                                        <tbody id="dt_compliance_dashboard_info">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="hr2">
                            <div class="row">
                                <div class="col-sm-12 maxw800">
                                    <table id="dt_compliance_statusreport" class="table table-striped table-hover dataTable">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="padding:10px;vertical-align: middle;text-align: center !important;" data-class="expand"><span id="statusreport_titlecolum1">All Control Points</span></th>
                                                <th colspan="3" style="text-align: center !important;vertical-align: middle;" data-hide="phone,tablet"><span id="statusreport_titlecolum2"></span> - GL Verification Status by Number of Transactions</th>
                                            </tr>
                                            <tr>
                                                <th style="width:110px;text-align: center !important;vertical-align: middle;" data-hide="phone,tablet">Verification Completed</th>
                                                <th style="width:110px;text-align: center !important;vertical-align: middle;" data-hide="phone,tablet">Items Not Verified</th>
                                                <th style="width:110px;text-align: center !important;vertical-align: middle;" data-hide="phone,tablet">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dt_compliance_statusreport_info">
                                        </tbody>
                                        <tfoot>
                                            <tr data-hide="phone,tablet">
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="hr3">
                            <div class="row">
                                <div class="col-sm-12 maxw800">
                                    <table id="dt_compliance_detailreport" class="table table-striped table-hover no-footer dataTable">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="padding:10px;vertical-align: middle;text-align: center !important;" data-class="expand">Control Point/Department (Dept ID Level 2)</th>
                                                <th colspan="3" style="line-height: 25px;text-align: center !important;vertical-align: middle;" data-hide="phone,tablet"><span id="detailreport_reportdate"></span> % GL Verification Status by Dept</th>
                                            </tr>
                                            <tr>
                                                <th style="width:110px;text-align: center !important;vertical-align: middle;" data-hide="phone,tablet">Verification Completed</th>
                                                <th style="width:110px;text-align: center !important;vertical-align: middle;" data-hide="phone,tablet">Items Not Verified</th>
                                                <th style="width:110px;text-align: center !important;vertical-align: middle;" data-hide="phone,tablet">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dt_compliance_detailreport_info">
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
<!-- END MAIN CONTENT -->

