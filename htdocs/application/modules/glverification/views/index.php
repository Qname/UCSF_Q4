<script type="text/javascript">
    $(function(){
        var vh= new GLVerificationManagement();
        vh.init_glverification();
    });
</script>
<!-- MAIN CONTENT -->
<div id="content">
    <section id="widget-grid" class="">
        <div class="row headerfilter">
            <div class="col-sm-12">
                <div class="well" style="width:100%;display: inline-block;padding: 10px 20px;">
                    <div class="form-inline row">
                        <div class="form-group" style="margin-right:10px;" mousedown="return false;">
                            <label for="reportdate">Report Date:</label>
                            <div class="input-group" style="max-width:140px;">
                                <input type="text" class="form-control date-picker col-sm-2" id="reportdate" value="<?= $reportdate ?>" style="width: 90px;" tabindex="1"/>
                                <span class="input-group-addon" id="btnshowreportdate" style="cursor:pointer;"><span class="glyphicon glyphicon-calendar"></span> </span>
                            </div>
                        </div>
                        <div class="form-group" style="margin-right:10px;">
                            <label for="drpdeptid">Dept ID:</label>
                            <select class="form-control input-sm" id="drpdeptid" style="min-width: 200px;max-width: 200px;" tabindex="2">
                                <?php
                                if($listdeptid != null){
                                    foreach($listdeptid as $item) {
                                        if ($item->DeptCd == $defaultdeptid) {?>
                                        <option value="<?= $item->DeptCd ?>" selected><?= $item->DeptCd ?><?= $item->DeptTreeTitleAbbrev ?><?=$item->Checked ? ' --- '.$item->Checked : ''  ?><?=$item->UserName ? ' --- '.$item->UserName : ''  ?></option>
                                        <?php   } else {?>
                                        <option value="<?= $item->DeptCd ?>"><?= $item->DeptCd ?><?= $item->DeptTreeTitleAbbrev?><?=$item->Checked ? ' --- '.$item->Checked : ''  ?><?=$item->UserName ? ' --- '.$item->UserName: ''  ?></option>
                                        <?php   }
                                    }
                                } else{?>
                                <option value="<?= $defaultdeptid ?>" selected><?= $defaultdeptid ?></option> <?php 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" style="margin-right:10px;">
                        <label for="drpbusunit">BU:</label>
                        <select class="form-control input-sm"  id="drpbusunit" tabindex="3">
                            <option value="SFCMP" <?php if($bu == 'SFCMP') echo ' selected="selected"' ?> >SFCMP</option>
                            <option value="SFFDN"  <?php if($bu == 'SFFDN') echo ' selected="selected"' ?> >SFFDN</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0 20px 0 30px;">                     
                        <div class="form-inline row" style=" border: 1px solid #e1d5d5;padding: 2px 4px;">
                            <div class="input-group filterrgroup">
                                <label class="input-group-addon" style="font-size: 12px;" for="drpFilters">My Filters:</label>
                                <select class="form-control input-sm"  id="drpFilters"  tabindex="4"></select>
                                <span class="input-group-addon" style="font-size: 12px;"><button type="button" class="btn btn-default" id="btn_edit_filter" tabindex="5">Edit</button></span>
                            </div>
                        </div>
                    
                </div>

                <button class="btn3d btn-blue btn-c" type="button" id="btnfilter_glverification" tabindex="6">
                    Submit
                </button>
            </div>
        </div>
    </div>   
</div>
<div class="jarviswidget" id="wid-id-7" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false" role="widget" style="margin-bottom: 0;">
    <header >
       
        <ul class="nav nav-tabs pull-left in">
            <li id="tabsglv_dashboard" class="active">
                <a id="tabsdashboard" data-toggle="tab" href="#hr0" ><span class="hidden-mobile hidden-tablet">Dashboard</span> </a>
            </li>

            <li id="tabsglv_transactions" >
                <a id="tabstransaction" data-toggle="tab" href="#hr1"><span class="hidden-mobile hidden-tablet">Review and Verify Transactions</span> </a>
            </li>
            <li id="tabsglv_payroll" >
                <a id="tabspayroll" data-toggle="tab" href="#hr2" ><span class="hidden-mobile hidden-tablet">Review and Verify Payroll </span></a>
            </li>

            <li id="tabsglv_monthtrendreport" >
                <a id="tabsmonthtrend" data-toggle="tab" href="#hr3" ><span class="hidden-mobile hidden-tablet">Review and Verify Monthly Trends</span></a>
            </li>
        </ul>
        <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span></header>
        <div role="content" style="display: block;">
            <div class="widget-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="hr0">                        
                        <p style="margin-bottom:0;">&nbsp;</p>
                        <div class="bggrad" style="border-bottom: none;"><strong>1. Review and verify the following selected high risk transactions, large dollar value transactions, transactions approved outside the reconciling Dept ID, unusual items, and selected sample transactions.</strong></div>
                        <div class="bd-darkgray glvdashboard" style="min-height: 265px;">
                            <table id="dt_glverification_dashboard" class="table table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th data-class="expand">
                                            <span>Transaction Type</span>
                                        </th>
                                        <th data-hide="phone,tablet">
                                            <div class="text-center">
                                                <u>Selected For Verification</u><br>
                                                <span style="width:60%;display: inline-block;margin:0;">$ Amount</span>
                                                <span style="width:35%;display: inline-block;margin:0; text-align: left;"># of Items</span>
                                            </div>
                                        </th>
                                        <th data-hide="phone,tablet">
                                            <div class="text-center">
                                                <u>Total Month Activity</u><br>
                                                <span style="width:60%;display: inline-block;margin:0;">$ Amount</span>
                                                <span style="width:35%;display: inline-block;margin:0; text-align: left;"># of Items</span>
                                            </div>
                                        </th>
                                        <th data-hide="phone,tablet">
                                            <div class="text-center">
                                                <span style="padding-left: 0px; padding-right: 0px; width:220px;"><br>% Items Completed or Pending</span>
                                            </div>
                                        </th>
                                        <th data-hide="phone,tablet">
                                            <div class="text-center">
                                                <u style="color:#686868;">Total Not Verified</u><br>
                                                <span style="width:60%;display: inline-block;margin:0;color:#686868;">$ Amount</span>
                                                <span style="width:35%;display: inline-block;margin:0; text-align: left;color:#686868;"># of Items</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="dt_glverification_dashboard_info">
                                </tbody>
                            </table>
                        </div>
                        <p>&nbsp;</p>
                        <div class="bggrad" style="margin-bottom: 20px;"><strong>2. Review department revenue and expense trends, look for deviations from plan, historical actuals, or forecast, and verify that the department trend is reasonable:</strong></div>
                        <div style="width: 100%; box-sizing: border-box;">
                            <div style="width: 50%; display: inline-block;"><button style="width: 60%; border-radius: 0px; margin-top: 10px;" type="button" id="btnmonthtrendreport"  tabindex="7">Monthly Trend Report</button></div>
                            <div style="width: 45%; display: inline-block;">% Monthly Trend Analysis Completed<br />
                                <div style="width: 85%; display: inline-block; border: 1px solid #686868;">
                                    <input disabled aria-label="monthly_percentage_actual" id="monthly_percentage_actual" style="width:<?php echo ($this->session->userdata('monthly_percentage') != null ? $this->session->userdata('monthly_percentage'): 0);?>%;border: none;background-color:#6ea400;padding:0;height: 20px;" type="text" class="display-inline"><input disabled aria-label="monthly_percentage_remain" id="monthly_percentage_remain" style="width: <?php echo (100 -($this->session->userdata('monthly_percentage') !=null ? $this->session->userdata('monthly_percentage'):0) );?>%; padding:0; border: none; background-color: #FDC0CE; height: 20px;" type="text" />
                                </div>
                                <div style="width:13%; display: inline-block; margin-left:-1.5%; border: 1px solid #686868; ">
                                    <input disabled aria-label="monthly_percentage" id="monthly_percentage"  style="background-color:white;width:100%;border:none;text-align: right;padding:0 5px 0 0;height: 20px;" type="text" class="display-inline">
                                </div>
                            </div>
                        </div>

                        <div class="bggrad col-sm-12" style="margin-bottom: 20px; border:1px solid gray;" id="divacknowlegement">
                            <p><strong><u>APPROVAL</u>:</strong></p>
                            <p>All GL Verification procedures for this Dept ID have been completed. To the best of my knowledge, all posted general ledger transactions accurately represent Dept ID activity, have been properly recorded, and errors have been identified/corrected.</p>
                        </div>
                        <?php
                        if ($approve_ack==='Disable' ){
                         echo '<button type="button" id="btnacknowlege" disabled style="width: 20%; box-sizing: border-box;margin-top: 10px;" class="btn btn-ack-disable-grey" >Approve and Submit</button>';
                     }

                     if($approve_ack==='EnableAcknowledged' ){
                         echo '<button type="button" id="btnacknowlege" disabled style="width: 20%; box-sizing: border-box;margin-top: 10px;" class="btn btn-ack-disable-blue" tabindex="8">Approved</button>';
                     }

                     if($approve_ack==='EnableNotAcknowledged'){
                         echo '<button type="button" id="btnacknowlege" style="width: 20%; box-sizing: border-box;margin-top: 10px;" class="btn btn-ack-enable-grey" tabindex="8">Approve and Submit</button>';
                     }
                     ?>


                 </div>
                 <div class="tab-pane tbltrans" id="hr1">
                    <div class="col-sm-10 bgcolor-light-grey bd-lightgrey" style="margin-bottom: 10px;">
                        <div id="flip">
                            <p >1. For each 'Not Verified' amount below, double click the value to display a GLV Detail report.</p>
                        </div>
                        <div id="panel">
                            <p>2. Verify that all transaction lines are accurately recorded; change the line status to 'Complete' to acknowledge review and verification.</p>
                            <p>3. Continue until all lines in the 'Not Verified' column below have been cleared and the balance is zero.</p>
                            <p>4. See GL Verification Job Aid for detailed instructions.</p>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <input type="radio" id="rdoshowdollar" name="rdotransactionshowcountdollar" value="Dollars"/><label for='rdoshowdollar'>Show Dollars</label><br>
                        <input type="radio" id="rdoshowcount" name="rdotransactionshowcountdollar" value="Count"/><label for='rdoshowcount'>Show Count</label>
                    </div>
                    <button class="pull-right btn3d btn-blue btn-c" id="exportTransaction" ><i class="fa fa-file-excel-o"></i> Export Data</button>
                    <table id="dt_transaction" class="table table-striped table-bordered table-hover dataTable no-footer" width="2500px">
                        <thead>
                            <tr>
                                <th data-hide="phone">uniqueid</th>
                                <th data-class="expand">Group</th>
                                <th data-hide="phone">Type</th>
                                <th data-hide="phone,tablet">Not Verified</th>
                                <th data-hide="phone,tablet">Pending</th>
                                <th data-hide="phone,tablet">Complete</th>
                                <th data-hide="phone,tablet">Auto Complete</th>
                                <th data-hide="phone,tablet">Prior Not Verified</th>
                                <th data-hide="phone,tablet">Prior Pending</th>
                                <th data-hide="phone,tablet">Not Verified</th><!--count-->
                                <th data-hide="phone,tablet">Pending</th><!--count-->
                                <th data-hide="phone,tablet">Complete</th><!--count-->
                                <th data-hide="phone,tablet">Auto Complete</th><!--count-->
                                <th data-hide="phone,tablet">Prior Not Verified</th><!--count-->
                                <th data-hide="phone,tablet">Prior Pending</th><!--count-->
                                <th data-hide="phone,tablet" id="col_1">Nov 2016</th>
                                <th data-hide="phone,tablet" id="col_2">Oct 2016</th>
                                <th data-hide="phone,tablet" id="col_3">Sep 2016</th>
                                <th data-hide="phone,tablet" id="col_4">Aug 2016</th>
                                <th data-hide="phone,tablet" id="col_5">Jul 2016</th>
                                <th data-hide="phone,tablet" id="col_6">Jun 2016</th>
                                <th data-hide="phone,tablet" id="col_7">May 2016</th>
                                <th data-hide="phone,tablet" id="col_8">Apr 2016</th>
                                <th data-hide="phone,tablet" id="col_9">Mar 2016</th>
                                <th data-hide="phone,tablet" id="col_10">Feb 2016</th>
                                <th data-hide="phone,tablet" id="col_11">Jan 2016</th>
                                <th data-hide="phone,tablet" id="col_12">Dec 2015</th>
                                <th data-hide="phone,tablet">Total</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                         <div>
                            <div class="display-none" id="reconitemcd_data"></div>
                            <div class="display-none" id="reconstatuscd_data"></div>
                            <div class="display-none" id="recongrouptitle_data"></div>
                            <div class="display-none" id="priormonth_data"></div>
                            <div class="display-none" id="recongrouptitle_data_transactionItem"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="hr2">
                        <div class="mag-b-50">
                            <label style="display: none;" id="lblhidden_status">hidden label</label>
                            <p>
                                <b>GL Verification</b><br />
                                Review and verify the following selected employees with current month payroll amount or distribution changes
                            </p>
                            <table id="dt_payroll_verification" class="table table-striped table-bordered table-hover dataTable no-footer" width="2500px">
                                <thead>
                                    <tr>
                                        <th data-hide="phone">uniqueid</th>
                                        <th data-class="expand" class="text-center">Category</th>
                                        <th data-hide="phone" class="text-center">EmployeeId</th>
                                        <th data-hide="phone,tablet" class="text-center">Employee Name</th>
                                        <th data-hide="phone,tablet" class="text-center">Amount</th>
                                        <th data-hide="phone,tablet" class="text-center">Status</th>
                                        <th data-hide="phone,tablet" class="text-center">Comments</th>
                                        <th data-hide="phone,tablet" class="text-center">Attachments</th>
                                        <th data-hide="phone,tablet" class="text-center">Dept</th>
                                        <th data-hide="phone,tablet" class="text-center">Fund</th>
                                        <th data-hide="phone,tablet" class="text-center">Project</th>
                                        <th data-hide="phone,tablet" class="text-center">Function</th>
                                        <th data-hide="phone,tablet" class="text-center">Flex</th>
                                        <th data-hide="phone,tablet" class="text-center">Dept Site</th>
                                        <th data-hide="phone,tablet" class="text-center">Primary Title</th>
                                        <th data-hide="phone,tablet" class="text-center">Verifier</th>
                                        <th data-hide="phone,tablet" class="text-center">Verification Date</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>                                        
                                        <td colspan="4" style="cursor: default;color:black;font-weight: bold;text-align: right !important;border: none !important;">Total:</td>
                                        <td colspan="13" style="cursor: default;color:black;font-weight: bold;border: none !important;padding-left: 10px;"></td>
                                    </tr>
                                </tfoot>
                                <tbody id="dt_payroll_verification_info">
                                </tbody>
                            </table>
                            <button id="btnSubmitVerifyPayroll" class="btn3d btn-blue btn-c">Submit</button>
                        </div>
                        <div class="mag-b-50">
                            <p>
                                <b>FTE & Salary</b> - Summary totals by month
                            </p>
                            <table id="dt_payroll_fte" class="table table-striped table-bordered table-hover dataTable no-footer" width="2500px">
                                <thead>
                                    <tr>
                                        <th data-hide="phone" class="text-center">Category</th>
                                        <th data-class="phone,tablet" id="fte_1">Jan 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_2">Feb 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_3">Mar 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_4">Apr 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_5">May 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_6">Jun 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_7">Jul 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_8">Aug 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_9">Sep 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_10">Oct 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_11">Nov 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="fte_12">Dec 2016 FTE</th>
                                        <th data-hide="phone,tablet" id="sal_1">Jan 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_2">Feb 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_3">Mar 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_4">Apr 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_5">May 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_6">Jun 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_7">Jul 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_8">Aug 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_9">Sep 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_10">Oct 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_11">Nov 2016 SAL</th>
                                        <th data-hide="phone,tablet" id="sal_12">Dec 2016 SAL</th>
                                    </tr>
                                </thead>
                                <tbody id="dt_payroll_fte_info">
                                </tbody>
                            </table>
                        </div>
                        <div class="mag-b-50">
                            <div class="row  mb-5">
                                <div class="col-md-9">
                                     <p>
                                    <b>Payroll Expense Detail and FTE Report</b> - Highlighted amounts in current month column indicate payroll amount or distribution changes from previous month
                                </p>
                                </div>
                                <div  class="col-md-3">
                                     <div class="mt-5 mag-b-40">
                                       <button class="pull-right btn3d btn-blue btn-c" id="exportPayroll" ><i class="fa fa-file-excel-o"></i> Export All Data</button>
                                         <button class="pull-right btn3d btn-blue btn-c mr-5" id="exportPayrollChanged" ><i class="fa fa-file-excel-o"></i> Export Chg Data</button>
                                   </div>
                                </div>
                                <!-- Pending function
                                 <div class="col-md-3" >
                                    <div class="col-xs-12">
                                         <form class="form-inline">                                        
                                            <label for="drPayrollFilter" class="col-md-5">Filter Results By</label>
                                            <select class="col-md-7"  id="drPayrollFilter">
                                                <option value="PositionTitleCategory">Category</option>
                                                <option value="Employee_Name">Name</option>
                                                <option value="Employee_Id">Emp ID</option>
                                                <option value="RecType">Rec Type</option>
                                                <option value="DeptCd">Dept ID</option>
                                                <option value="FundCd">Fund</option>
                                                <option value="ProjectCd">Project</option>
                                                <option value="FunctionCd">Funct</option>
                                                <option value="FlexCd">Flex</option>
                                                <option value="PositionTitleCd">Pr Title</option>
                                                <option value="EmpChanged">Chg fields</option>
                                            </select>
                                        </form>  
                                    </div>
                                    <div class="col-xs-12" id="inputFilter">
                                        <form class="form-inline">                                        
                                          <label for="txtPayrollFilter" class="col-md-5">Input Data</label>
                                          <input type="text" class="  col-md-7" id="txtPayrollFilter" />
                                        </form>  
                                    </div>
                                    
                                                              
                                </div>
                            -->
                               
                            </div>
                           
                           
                            <table id="dt_payroll_expense_detail" class="table table-striped table-bordered table-hover dataTable no-footer" width="2500px">
                                <thead>
                                    <tr>
                                        <th data-hide="phone">uniqueid</th>
                                        <th data-class="expand" class="text-center">Category</th>
                                        <th data-hide="phone" class="text-center">Name</th>
                                        <th data-hide="phone,tablet" class="text-center">Emp Id</th>
                                        <th data-hide="phone,tablet" class="text-center">Rec Type</th>
                                        <th data-hide="phone,tablet" class="text-center">Dept ID</th>
                                        <th data-hide="phone,tablet" class="text-center">Fund</th>
                                        <th data-hide="phone,tablet" class="text-center">Project</th>
                                        <th data-hide="phone,tablet" class="text-center">Funct</th>
                                        <th data-hide="phone,tablet" class="text-center">Flex</th>
                                        <th data-hide="phone,tablet" class="text-center">Pr Title</th>
                                        <th data-hide="phone,tablet" class="text-center">Chg</th>
                                        <th data-hide="phone,tablet" id="exp_1">Jan 2016</th>
                                        <th data-hide="phone,tablet" id="exp_2">Feb 2016</th>
                                        <th data-hide="phone,tablet" id="exp_3">Mar 2016</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                             <div>
                                <div class="display-none" id="empName_data"></div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="hr3">
                        <div style="margin-bottom:10px;" class="pull-left">
                             <label for="monthlyTrendDrop"  class="bgcolor-light-grey bd-lightgrey pull-right" style="margin-bottom: 0px;">
                                Review department revenue and expense trends, look for deviations from Plan, historical actuals, or forecast, and verify that transactions are accurately recorded.
                            </label> 
                            <div  class="pull-left  mr-5">
                                 <select id="monthlyTrendDrop" style="width:100px;" class=" pull-right">
                                    <option value="(multiple)">(multiple)</option>
                                    <option value="Not Verified">Not Verified</option>
                                    <option value="Complete">Complete</option>
                                </select>
                            </div>
                           
                           
                                
                            
                           
                        </div>
                        <div class=" col-xs-12 no-padding-left">
                            <button type="button" disabled style="width:100px;margin-bottom:10px;" id="monthlyTrendDropBtn">Save</button>
                        </div>
                        <div id="commentMonthlyTrend" style="margin-bottom: 5px;"  class=" col-xs-12 no-padding-left">
                            <div class="col-xs-1 no-padding-left bgcolor-light-grey bd-lightgrey" style="text-align: center;padding-top: 3px;padding-right: 0px;margin-right: -14px;width: 100px;">
                                <label>Comments:</label>
                            </div>                             
                            <div class=" bgcolor-light-grey bd-lightgrey" style="padding-left: 70px;width: 120px;" > 
                                <span class="glyphicon  gi-2x" id="commentMonthly" ></span>
                            </div>
                        </div>
                        <div >
                            <iframe id="monthlyTrendUrl" src="" style="width:100%;height:900px; "></iframe>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
</div>

<!-- END MAIN CONTENT -->

<!-- Modal where you will be able to add new rule -->
<div class="modal fade" id="ModalGLVItemDetails" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" class="close" id="closeModalGLVItemDetails" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title modal-title-main" style="font-weight: bold;">Verify GLV Items</h4>
            </div>
            <div class="modal-body bodyglvdetail">
                <!-- Pending Show/hide column function
                <div class="panel panel-default">
                    <div class="panel-heading modal-title">Show/hide columns</div>                   
                    <div class="panel-body">
                        <div class="col-lg-7">
                            <div class="col-lg-3">
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="11" class="toggle-vis columns">
                                    <span class="label-text">Verification Status</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="6" class="toggle-vis columns">
                                    <span class="label-text">Account</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="8" class="toggle-vis columns">
                                    <span class="label-text">Flex</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="16" class="toggle-vis columns">
                                    <span class="label-text">Journal Line Description</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="33" class="toggle-vis columns">
                                    <span class="label-text">Vendor No.</span>                       
                                  </label>
                                </div>                      
                            </div>
                            <div class="col-lg-3">
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="12" class="toggle-vis columns">
                                    <span class="label-text">Verification Comments</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="23" class="toggle-vis columns">
                                    <span class="label-text">Account Description</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="15" class="toggle-vis columns">
                                    <span class="label-text">Reference</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="27" class="toggle-vis columns">
                                    <span class="label-text">PO</span>                       
                                  </label>
                                </div>                        
                            </div>
                            <div class="col-lg-3">
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="26" class="toggle-vis columns">
                                    <span class="label-text">Attachment</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="3" class="toggle-vis columns">
                                    <span class="label-text">Fund</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="10" class="toggle-vis columns">
                                    <span class="label-text">Amount</span>                       
                                  </label>
                                </div>  
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="28" class="toggle-vis columns">
                                    <span class="label-text">Invoice</span>                       
                                  </label>
                                </div>                       
                            </div>
                            <div class="col-lg-3">
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="20" class="toggle-vis columns">
                                    <span class="label-text">GLV assign Description</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="2" class="toggle-vis columns">
                                    <span class="label-text">Dept ID</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="13" class="toggle-vis columns">
                                    <span class="label-text">Journal ID</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="29" class="toggle-vis columns">
                                    <span class="label-text">Voucher</span>                       
                                  </label>
                                </div>                      
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="col-lg-4">
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="24" class="toggle-vis columns">
                                    <span class="label-text">Verifier</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="4" class="toggle-vis columns">
                                    <span class="label-text">Project ID</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="14" class="toggle-vis columns">
                                    <span class="label-text">Jrnl Post Dt</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="30" class="toggle-vis columns">
                                    <span class="label-text">Invoice Date</span>                       
                                  </label>
                                </div>                       
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="25" class="toggle-vis columns">
                                    <span class="label-text">Verification Date</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="7" class="toggle-vis columns">
                                    <span class="label-text">Activity Period</span>                       
                                  </label>
                                </div>     
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="19" class="toggle-vis columns">
                                    <span class="label-text">Jrnl Opr Desc</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="31" class="toggle-vis columns">
                                    <span class="label-text">Invoice Req Date</span>                       
                                  </label>
                                </div>                      
                            </div>
                            <div class="col-lg-4">
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="1" class="toggle-vis columns">
                                    <span class="label-text">Business Unit</span>                       
                                  </label>
                                </div>
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="5" class="toggle-vis columns">
                                    <span class="label-text">Function</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="17" class="toggle-vis columns">
                                    <span class="label-text">Journal Description</span>                       
                                  </label>
                                </div>   
                                <div class="form-check">
                                  <label >
                                 <input type="checkbox" checked data-column="32" class="toggle-vis columns">
                                    <span class="label-text">Vendor name</span>                       
                                  </label>
                                </div>                      
                            </div>
                        </div>
                    </div>
                </div>
                -->
                <div class="col-lg-12">
                    <label >
                                Note: If you wish to add comments or upload attachments on transaction rows, do so before changing the Verification Status to Pending or Complete.
                            </label> 
                </div>
                
                <div class="col-lg-3 padding-bottom-10 no-padding-right pull-right" >
                                    <div class="col-xs-12 no-padding-right no-padding-left">
                                         <div class="form-inline">                                        
                                            <label for="drGLVItemFilter" class="col-md-4  no-padding-left">Filter Results By</label>
                                            <select class="col-md-5"  id="drGLVItemFilter">
                                                <!--option value="ReconStatusCd">Verification Status</option-->
                                                <option value="AllData">Choose one</option>
                                                <option value="ReconAssignDesc">GLV assign Description</option>
                                                <option value="CommentGLVTypeId">Verification Comments</option>
                                                <option value="ReconLink">Attachment</option>
                                                <option value="user_name">Verifier</option>
                                                <option value="ReconDate">Verification Date</option>
                                                <option value="BusinessUnitCd">Business Unit</option>
                                                <option value="AccountCd">Account</option>
                                                <option value="AccountTitleCd">Account Description</option>
                                                <option value="FundCd">Fund</option>
                                                <option value="DeptCd">Dept ID</option>
                                                <option value="ProjectCd">Project ID</option>
                                                <option value="ActivityCd">Activity Period</option>
                                                <option value="FunctionCd">Function</option>
                                                <option value="FlexCd">Flex</option>
                                                <option value="JournalLineRef">Reference</option>
                                                <option value="Amount">Amount</option>
                                                <option value="JournalId">Journal ID</option>
                                                <option value="JournalPostDt">Jrnl Post Dt</option>
                                                <option value="JournalOprDesc">Jrnl Opr Desc</option>
                                                <option value="JournalTitle">Journal Description</option>
                                                <option value="JournalLineDesc">Journal Line Description</option>
                                                <option value="InvoicePO">PO</option>
                                                <option value="InvoiceId">Invoice</option>
                                                <option value="InvoiceVoucherId">Voucher</option>
                                                <option value="InvoiceDate">Invoice Date</option>
                                                <option value="InvoiceReqDeptCd">Invoice Req Date</option>
                                                <option value="InvoiceVendorName">Vendor name</option>
                                                <option value="InvoiceVendorCd">Vendor No.</option>

                                            </select>
                                            <div class="col-md-3 pull-right">
                                                <button id="resetGLVItemDetails" >Clear&nbsp;<i class="fa fa-undo" style="font-size: 10px;"></i></button>  
                                    </div>
                                        </div>
                                       
                                    
                                                
                                    </div>
                                    <div class="col-xs-12  no-padding-right no-padding-left" id="inputGLVItemFilter">
                                        <form class="form-inline ">                                        
                                          <label for="txtGLVItemFilter" class="col-md-4  no-padding-left" >Input Data</label>
                                          <input type="text" class="  col-md-5  col-xs-12" id="txtGLVItemFilter" />
                                        </form>  
                                    </div>
                                    
                                                              
                                </div>
                                 
               
                <div class="col-lg-12 pull-right no-padding-right" >
                    
                     <button class="pull-right btn3d btn-blue btn-c mag-r-12 mb-5" id="exportGLVItemDetails" ><i class="fa fa-file-excel-o"></i> Export Data</button>
                </div>
               
                <div class="col-lg-12" id="glvItemGrid">
                    <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-0" data-widget-editbutton="false" role="widget" style="margin-bottom: 0;">
                    <!-- widget options:
                    usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

                    data-widget-colorbutton="false"
                    data-widget-editbutton="false"
                    data-widget-togglebutton="false"
                    data-widget-deletebutton="false"
                    data-widget-fullscreenbutton="false"
                    data-widget-custombutton="false"
                    data-widget-collapsed="true"
                    data-widget-sortable="false"
                -->
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Verify GLV Items</h2>
                    <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span>
                </header>
                <div role="content">
                    <div class="widget-body no-padding">                        
                        <table id="dt_verifyglvitems" class="table table-striped table-bordered table-hover dataTable no-footer">
                            <thead>
                                <tr>
                                    <th data-hide="phone">uniqueid</th>
                                    <!-- Old order
                                    <th>BU</th>
                                    <th>Dept ID</th>
                                    <th>Fund</th>
                                    <th>Project</th>
                                    <th>Funct</th>
                                    <th>Account</th>
                                    <th>Actvy</th>
                                    <th>Flex</th>
                                    <th data-hide="phone">AcctMedCtr</th>
                                    <th>Amount</th>
                                    <th>Verification Status</th>
                                    <th>Verification Comments</th>
                                    <th>Jrnl ID</th>
                                    <th>Jrnl Post Dt</th>
                                    <th>Reference</th>
                                    <th>Jrnl Line Desc</th>
                                    <th>Jrnl Desc</th>
                                    <th data-hide="phone">Jrnl Src Title</th>
                                    <th>Jrnl Opr Desc</th>
                                    <th>GLV Assign Description</th>
                                    <th data-hide="phone">Project Use</th>
                                    <th data-hide="phone">Project Title</th>
                                    <th>Account Title</th>
                                    <th>Verifier</th>
                                    <th>Verification Date</th>
                                    <th>Attachments</th>
                                    <th>PO</th>
                                    <th>Invoice</th>
                                    <th>Voucher</th>
                                    <th>Invoice Date</th>
                                    <th>Invoice Req Dept</th>
                                    <th>Vendor Name</th>
                                    <th>Vendor No</th>
                                     -->
                                     <!-- New order -->
                                     <th>Verification Status</th>
                                    <th>Verification Comments</th>
                                    <th>Attachment</th>
                                    <th>GLV assign Description</th>
                                    <th>Verifier</th>
                                    <th>Verification Date</th>
                                    <th>Business Unit</th>
                                    <th>Account</th>
                                    <th>Account Description</th>
                                    <th>Fund</th>
                                    <th>Dept ID</th>
                                    <th>Project ID</th>
                                    <th>Activity Period</th>
                                    <th>Function</th>
                                    <th>Flex</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Journal ID</th>
                                    <th>Jrnl Post Dt</th>
                                    <th>Jrnl Opr Desc</th>
                                    <th>Journal Description</th>
                                    <th>Journal Line Description</th>
                                    <th>PO</th>
                                    <th>Invoice</th>
                                    <th>Voucher</th>
                                    <th>Invoice Date</th>
                                    <th>Invoice Req Date</th>
                                    <th>Vendor name</th>
                                    <th>Vendor No.</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end widget div -->
            </div>
        </div>
    </div>
    <div class="modal-footer" style="text-align: left;">
        <input id="txtreconitemcd" type="hidden" value=""/>
        <input id="txtreconstatuscd" type="hidden" value=""/>
        <input id="txtrecongrouptitle" type="hidden" value=""/>
        <button type="button" class="btn btn-default" id="btnmoveallto_verified">Move all to Not Verified</button>
        <button type="button" class="btn btn-default" id="btnmoveallto_pending">Move all to Pending</button>
        <button type="button" class="btn btn-default" id="btnmoveallto_completed">Move all to Completed</button>
        <button type="button" class="btn btn-default" id="btnresetall">Reset all</button>
        <button type="button" class="btn btn-primary" id="saveGLVItemDetails">Save</button>
    </div>
</div>
</div>
</div>

<!-- Modal: My filters -->
<div class="modal fade" id="modal_build_filter" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="width: 1120px">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="btn-close-filters">x</button>
                <h4 class="modal-title modal-title-main">Build Filter</h4>
            </div>
            <div class="modal-body">
                <div class="header-filter row">
                    <div class="col-xs-12 col-sm-7 dd-filter">
                        <div class="form-group row">
                            <label class="col-xs-4 col-md-2 mag-top-7" for="select-filter-name">My Filters:</label>
                            <div class="col-xs-8 col-md-10" >
                                <select class="form-control" id="select-filter-name">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-5 group-btn">
                        <a type="button" class="btn btn-default btn-lg" id="btn-filter-save_as">Save As</a>
                        <a type="button" class="btn btn-default btn-lg" id="btn-filter-save">Save</a>
                        <a type="button" class="btn btn-default btn-lg" id="btn-filter-clear">Clear</a>
                        <a type="button" class="btn btn-default btn-lg" id="btn-filter-delete">Delete</a>
                        <span class="box-deptCd" id="lbl-filter-deptCd"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <label for="drpsite">Site:</label>
                        <select class="form-control input-sm"  id="drpsite">
                            <option value="(any)">(any)</option>
                            <option value="CORE">CORE</option>
                            <option value="SFGH">SFGH</option>
                            <option value="FRES">FRES</option>
                            <option value="VAMC">VAMC</option>
                        </select>
                    </div>
                </div>
                <div class="body-filter row margin-top-10">
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="form-group" id="form-filter-projCd">
                            <label for="select-filter-projectCd">Project:</label>
                            <select multiple="multiple" id="select-filter-projectCd" style="height: 29px;width:100%;">
                            </select>
                        </div>
                        <div class="form-group">
                            <a type="button" class="btn btn-default btn-lg" id="btn-add-projectCd">Add</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-remove-projectCd">Remove</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-not-projectCd">Not</a>
                        </div>
                        <div class="GrBlueWin list-projectCd" id="filter-all-projectCd">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="form-group" id="form-filter-funcCd">
                            <label for="select-filter-funcCd">Fund:</label>
                            <select multiple="multiple" id="select-filter-funcCd" style="height: 29px;width:100%;">
                            </select>
                        </div>
                        <div class="form-group">
                            <a type="button" class="btn btn-default btn-lg" id="btn-add-funcCd">Add</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-remove-funcCd">Remove</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-not-funcCd">Not</a>
                        </div>
                        <div class="GrBlueWin list-funcCd" id="filter-all-funcCd">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="form-group" id="form-filter-projectMgr">
                            <label for="select-filter-projectMgr">Project Mgr:</label>
                            <select multiple="multiple" id="select-filter-projectMgr" style="height: 29px;width:100%;">
                            </select>
                        </div>
                        <div class="form-group">
                            <a type="button" class="btn btn-default btn-lg" id="btn-add-projectMgr">Add</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-remove-projectMgr">Remove</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-not-projectMgr">Not</a>
                        </div>
                        <div class="GrBlueWin list-projectMgr" id="filter-all-projectMgr">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-3">
                        <div class="form-group" id="form-filter-projectUse">
                            <label for="select-filter-projectUse">Project Use:</label>
                            <select multiple="multiple" id="select-filter-projectUse" style="height: 29px;width:100%;">
                            </select>
                        </div>
                        <div class="form-group">
                            <a type="button" class="btn btn-default btn-lg" id="btn-add-projectUse">Add</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-remove-projectUse">Remove</a>
                            <a type="button" class="btn btn-default btn-lg" id="btn-not-projectUse">Not</a>
                        </div>
                        <div class="GrBlueWin list-projectUse" id="filter-all-projectUse">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: confirm clear filter -->
<div class="modal fade" id="modal_confirm_clear_filter" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Clear Filter</h4>
    </div>
    <div class="modal-body text-center">
        <div>Are you sure you want to clear this filter?</div>
        <div class="mag-top-30">
            <button type="button" class="btn btn-primary"  id="btn-yes-clear-filter">Yes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        </div>
    </div>
</div>
</div>
</div>

<!-- MODAL: confirm delete filter -->
<div class="modal fade" id="modal_confirm_delete_filter" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Delete Filter</h4>
    </div>
    <div class="modal-body text-center">
        <div>Are you sure you want to delete this filter?</div>
        <div class="mag-top-30">
            <button type="button" class="btn btn-primary"  id="btn-yes-delete-filter">Yes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        </div>
    </div>
</div>
</div>
</div>

<!-- MODAL: SAVE AS filter -->
<div class="modal fade" id="modal_save_as_filter" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Filter name</h4>
    </div>
    <div class="modal-body">
        <label for="txtNewNameFilter">Please name this filter</label>
        <input class="form-control" id="txtNewNameFilter" type="text" maxlength="50"/>
        <span id="msgErrReq" class="errText dispay-none">Please name this filter</span>
        <div class="mag-top-30 text-right">
            <button type="button" class="btn btn-primary"  id="btn-ok-filter_saveAs">OK</button>
            <button type="button" class="btn btn-default" data-dismiss="modal" id="btn-close-saveAs">Cancel</button>
        </div>
    </div>
</div>
</div>
</div>

<!-- MODAL: confirm duplicate filter -->
<div class="modal fade" id="modal_confirm_duplicate_filter" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Save as Filter</h4>
    </div>
    <div class="modal-body text-center">
        <div>This name already exits, do you want to overwrite it?</div>
        <div class="mag-top-30">
            <button type="button" class="btn btn-primary"  id="btn-yes-duplicate-filter">Yes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        </div>
    </div>
</div>
</div>
</div>

<!-- Modal where you will be able to need to upload documents -->
<div class="modal fade" id="ModalUploadDocument" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" class="close" id="closeUploadBtn" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title modal-title-main" style="font-weight: bold;">Attachments - [<span id="jnrlinedescription"></span>]</h4>
            </div>
            <div class="modal-body bodyglvdetail">
            <section id="widget-grid" class="padding-bottom-10">
                <p id="pUploadFiles">Upload Files</p>
                <input aria-labelledby="pUploadFiles" type="file"   multiple = "multiple" id="files" name="files" style="width:100%;" /><br>
                <button id="upload_file"  style="padding-right:8px;padding-left:8px;">Upload Files</button>
                <p id="msg_upload" style="padding-top:10px;"></p>
            </section>    
            <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-0" data-widget-editbutton="false" role="widget" style="margin-bottom: 0;">
                    <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Attachments</h2>
                    <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span>
                    </header>
             
                    <div role="content">
                        <div class="widget-body no-padding">
                            <table id="dt_attchments" class="table table-striped table-bordered table-hover dataTable no-footer">
                                <thead id="dt_attchments_info">
                                    <tr>
                                        <th>File Name</th>   
                                        <td style="background-color: #eee !important;"></td>                              
                                    </tr>
                                </thead>
                                <tbody id="body_document_tables">
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
            </div>
            <p class="display-none;" id="document_uniqueid"></p>
            <p class="display-none;" id="upload_glvType"></p>
            <p class="display-none;" id="document_glvtypeid"></p>
        </div>
    </div>
</div>
</div>
</div>

<!-- Model popup comment-->
<div class="modal fade" id="ModalGLVComments" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button type="button" id="closeModalCommentBtn" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h4 class="modal-title modal-title-main" style="font-weight: bold;">Comments - <span id="uniqueIdComment"></span></h4>
            </div>
            <!-- hidden element div -->
            <div>
                <div class="display-none" id="comment_Type"></div>
                <div class="display-none" id="comment_glvtype"></div>
                <div class="display-none" id="uniqueId"></div>
            </div>
          

            <div class="modal-body bodyglvdetail" style="padding-top:10px">
                <div class="col-lg-12 padding-bottom-10 display-none" id="addEditCommentDiv">
                    <div id="currentCommentDiv" style="margin-bottom:6px;">Add Comment</div>
                    <div class="display-none" id="currentCommentId"></div>                    
                    <textarea aria-labelledby="currentCommentDiv" rows="4" id="current_comment" style="width: 100%; padding: 2px 5px;">  </textarea>                   
                </div>

                <div class="col-lg-12 padding-bottom-10" >
                    <button id="addCommentBtn">Add Comment</button>
                    <button id="saveCommentBtn" class="display-none">Add Comment</button>
                    <button class="display-none" id="cancelCommentBtn">Cancel</button>
                </div>
                <div id="comment_result" style="margin:0px 0px 10px 12px;display:none;"><span></span></div>
                <div class="col-lg-12">
                    <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-0" data-widget-editbutton="false" role="widget" style="margin-bottom: 0;">
                <header role="heading">
                    <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                    <h2>Comments</h2>
                    <span class="jarviswidget-loader" style="display: none;"><i class="fa fa-refresh fa-spin"></i></span>
                </header>
             
                <div role="content">
                    <div class="widget-body no-padding">
                        <table id="dt_glvcomments" class="table table-striped table-bordered table-hover dataTable no-footer">
                            <thead>
                                <tr>
                                    <th style="display:none;">Id</th>
                                    <th>Comment</th>                    
                                    <th>User</th>
                                    <th>Comment Date</th>         
                                    <th style="display:none;">UserId</th>
                                </tr>
                            </thead>
                            <tbody id="dt_glvcomments_info">
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end widget div -->
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- MODAL: confirm delete file -->
<div class="modal fade" id="modal_confirm_delete_file" tabindex="-1" role="dialog"  aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleteModalLabel">Delete File</h4>
    </div>
    <div class="modal-body text-center">
        <div>Are you sure you want to delete the file "<span id="delete_documentname"></span>"?</div>
        <div class="mag-top-30">
            <button type="button" class="btn btn-primary"  id="btn-yes-delete-file">Yes</button>
            <button type="button" class="btn btn-default" id="btn-no-delete-file" data-dismiss="modal">No</button>
        </div>
        <p class="display-none" id="delete_documentid"></span>
        <p class="display-none" id="delete_document_glvtypeid"></span>
    </div>
</div>
</div>
</div>



<!-- Loading Modal -->
<div class="modal fade z-index-max" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog mid-cen-screen" role="document">
      <img src="<?php echo base_url();?>assets/smartAdminTemplate/img/ajax-loader.gif" alt="loading" />
  </div>
</div>