<div class="user-info">  
    <a class="navbar-brand" href="<?php echo base_url();?>glvhome">
          <img width="219" height="100" class="logo" alt="ucsflogo" src="<?php echo base_url();?>assets/smartAdminTemplate/img/ucsflogo.png">
          <img width="44" height="44" class="logo-xs" alt="ucsflminiogo" src="<?php echo base_url();?>assets/smartAdminTemplate/img/ucsflogo_mini.png">
    </a>
    <span id="userId" class="usesr-email"><?php if (isset($this->session->userdata['email'])){ echo $this->session->userdata['email']; }?></span>
    <span id="hidden_userid" class="display-none"><?php if (isset($this->session->userdata['userid'])){ echo $this->session->userdata['userid']; }?></span>
</div>
<nav>
    <ul style="">
        <li class="<?php echo activate_menu('glvhome');?>">
            <a href="<?php echo base_url();?>glvhome" class="<?php echo activate_menu('glvhome');?>" title="GLV Home"><i class="fa fa-lg fa-fw fa-home"></i> <span class="menu-item-parent">GLV Home</span></a>
        </li>
        <li class="<?php echo activate_menu('glverification');?>">
            <a href="<?php echo base_url();?>glverification" class="<?php echo activate_menu('glverification');?>" title="GL Verification"><i class="fa fa-lg fa-fw fa-check-square-o"></i> <span class="menu-item-parent">GL Verification</span></a>
        </li>
        <li class="<?php echo activate_menu('compliance');?>">
            <a href="<?php echo base_url();?>compliance" class="<?php echo activate_menu('compliance');?>" title="Compliance Dashboard"><i class="fa fa-lg fa-fw fa-university"></i> <span class="menu-item-parent">Compliance Dashboard</span></a>
        </li>
        <?php
        if ( $this->session->userdata('authorized_role')=="Sysadmin") { ?>
            <li class="<?php echo activate_menu('usersmanagement');?>">
                <a href="<?php echo base_url();?>usersmanagement" class="<?php echo activate_menu('usersmanagement');?>" title="Users Management"><i class="fa fa-lg fa-fw fa-user"></i> <span class="menu-item-parent">Users Management</span></a>
            </li>
        <?php
        }
        ?>
        <?php
        if ( $this->session->userdata('authorized_role')=="Sysadmin") { ?>
            <li class="<?php echo activate_menu('glvsetting');?>">
                <a href="<?php echo base_url();?>glvsetting" class="<?php echo activate_menu('glvsetting');?>" title="GLV Settings"><i class="fa fa-lg fa-fw fa-user"></i> <span class="menu-item-parent">GLV Settings</span></a>
            </li>
        <?php
        }
        ?>
    </ul>
</nav>
<span class="minifyme" data-action="minifyMenu"> <i class="fa fa-arrow-circle-left hit"></i></span>

