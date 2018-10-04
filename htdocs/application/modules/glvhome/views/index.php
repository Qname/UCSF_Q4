<script type="text/javascript">
    $(function(){
        var vh= new GLVHomeManagement();
    });
</script>

<!-- MAIN CONTENT -->
<div id="content">
    <div class="row mag-b-20"><!-- col -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1 class="page-title txt-color-black" style="margin:0;margin-bottom:8px;">
                GL VERIFICATION
            </h1>
            <p style="margin-bottom:8px;">General Ledger Verification (GLV) is a key internal control of UCSF and is a requirement per Campus Administrative Policies. It is the responsibility of each department to verify the financial transactions recorded in the general ledger are reasonable and accurately represent Dept ID activity, have been properly recorded, and errors have been identified/corrected.</p>
            <p style="margin-bottom:8px;">The GLV tool automatically selects items for review and verification based on established business rules. The selected items include: high risk transactions, large dollar value transactions, transactions approved outside the reconciling Dept ID, unusual items, and selected sample transactions.</p>
            <p style="margin-bottom:8px;">The GL Verification process must be completed within the following month after each month-end close.</p>
            <p style="margin-bottom:8px;">To get started, enter a Dept ID in the filter below and ‘Submit’ to launch the GL Verification tools:</p>
            <ul>
                <li><strong>GL Verification</strong> – Dashboard overview of GLV completion status
                    <ul>
                        <li><span style="text-decoration: underline;">Review and Verify Transactions</span> tab – Summary of transactions selected for verification by Source Code. Provides links to journal line detail reports.</li>
                        <li><span style="text-decoration: underline;">Review and Verify Payroll</span> tab – Payroll transactions selected for verification. Also provides a detail payroll report with all Dept ID payroll transactions for ease of reference/research.</li>
                        <li><span style="text-decoration: underline;">Review and Verify Monthly Trends</span> tab – generates a Monthly Report summarizing Dept ID revenue and expense trends.&nbsp; Review the report for deviations from Plan or historical actuals, and overall reasonableness of department results.</li>
                    </ul>
                </li>
            </ul>
        </div><!-- end col -->		
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <section id="widget-grid" class="">
                <div class="jarviswidget jarviswidget-sortable borderorrange" id="wid-id-01" data-widget-colorbutton="false" data-widget-editbutton="false" role="widget">
                    <!--<header role="heading">
                       
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>  
                    </header>-->
                    <div role="content">
                        <!-- widget content -->
                        <div class="widget-body">
                            <div class="row" style="width:100%;display: inline-block;">
                                <div class="col-md-2">
                                    <button class="btn3d btn-blue btn-c" type="button" id="btnSubmitHome" tabindex="4">
                                        Submit
                                    </button>
                                </div>
                                <div class="col-md-10">
                                    <div style="display: inline-block; box-sizing: border-box; -moz-box-sizing: border-box; -ms-box-sizing: border-box; -webkit-box-sizing: border-box;">1. Select your <b>Control Point</b><br>
                                        2. If you know your <b>Dept ID</b>, enter it in the Dept ID field, OR,<br>
                                        3. You can use the <b>Dept ID Rollup</b> field to search for and select a Dept ID from the dropdown menu
                                    </div>
                                </div>
                            </div>
                            <form class="form-horizontal" action="GLVHome/save_as_default" method="post">
                                <fieldset class="custom-field">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="ddl_points">Control Point:</label>
                                        <div class="col-md-10 destop-width-45percent">
                                            <select class="form-control input-sm" id="ddl_points" name="controlPoint"  tabindex="1">
                                                <?php
                                                foreach($listControlPoints as $item) { 
                                                    if ($item->DeptCd == $controlPointDefault) {?>
                                                    <option value="<?= $item->DeptCd ?>" selected><?= $item->DeptTitle ?></option>
                                                    <?php   } else {?>
                                                    <option value="<?= $item->DeptCd ?>"><?= $item->DeptTitle ?></option>
                                                    <?php   }   
                                                } ?>
                                            </select>
                                            
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="txtDeptId">Dept ID</label>
                                        <div class="col-md-10 destop-width-45percent">
                                            <?php if ($rollUpDefault != 0) {?>
                                            <input id="txtDeptId" class="form-control" placeholder="Dept ID" type="text" value="<?= $rollUpDefault ?>" name="deptId" >
                                            <?php } else {?>
                                            <input id="txtDeptId" class="form-control" placeholder="Dept ID" type="text" name="deptId" >
                                            <?php } ?>
                                            <span id ="error_dept" style="color: red"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-2 control-label"></div>
                                        <div class="col-md-10">
                                            <div>--OR--</div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="ddl_rollup">Dept ID Rollup:</label>
                                        <div class="col-md-10 destop-width-45percent">
                                            <select class="form-control input-sm" id="ddl_rollup" name="rollUp"  tabindex="2">
                                                <?php
                                                foreach($listRollUps as $item) { 
                                                    if ($item->DeptCd == $rollUpDefault) {?>
                                                    <option value="<?= $item->DeptCd ?>" selected><?= $item->DeptTreeTitleAbbrev ?></option>
                                                    <?php   } else {?>
                                                    <option value="<?= $item->DeptCd ?>"><?= $item->DeptTreeTitleAbbrev ?></option>
                                                    <?php   }   
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-2 control-label"></div>
                                        <div class="col-md-10">
                                            <button class="btn btn-default" type="button" id="btnSaveAsDefault" tabindex="3">
                                                <i class="fa fa-save"></i>
                                                Save as My Default
                                            </button>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <!-- end widget content -->

                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="row mag-b-20"><!-- col -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p>GLV also provides a high level compliance summary for oversight and control purposes:</p>
            <ul>
                <li><strong>Compliance Dashboard</strong> – Displays a management overview of GL Verification completion status by Control Point or by Level 2 Dept ID</li>
            </ul>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade z-index-max" id="loadingModal" tabindex="-1" role="dialog"  data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog mid-cen-screen" role="document">
      <img src="<?php echo base_url();?>assets/smartAdminTemplate/img/ajax-loader.gif" alt="loading" />
  </div>
</div>