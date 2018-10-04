<!-- MAIN CONTENT -->
<div id="content">
    <div class="row"><!-- col -->
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h1 class="page-title"><!-- PAGE HEADER -->
                Users Management
            </h1>
        </div><!-- end col -->		
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
            <section id="widget-grid" class="">
                <div class="jarviswidget jarviswidget-sortable borderorrange" id="wid-id-01" data-widget-colorbutton="false" data-widget-editbutton="false" role="widget">
                    <header role="heading">
                    
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span>  
                    </header>
                    <div role="content">
                        <!-- widget content -->
                        <div class="widget-body">
                           <input type="button" style="margin-bottom:10px;" class="btn btn-default" id="add_new_user" value="Add User" onclick="return add_user();"/>
                           <div id="user_content_table"><?php $this->load->view('list_users');?></div>
                       </div>
                       <!-- end widget content -->

                   </div>
               </div>
           </section>
       </div>
   </div>
</div>

<div class="modal fade" style="z-index:1100;" id="loadingModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog mid-cen-screen" role="document">
      <img src="<?php echo base_url();?>assets/smartAdminTemplate/img/ajax-loader.gif" alt="loading" />
  </div>
</div>

<!-- Modal where you will be able to add new rule -->
<div class="modal fade" id="ModalEditUser" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    
</div>