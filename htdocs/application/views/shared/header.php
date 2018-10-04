    <div class="col-md-4" style="width:33.3%;"></div>
    <div class="col-md-4"><h1 class="front-header">General Ledger Verification</h1></div>
    <div class="col-md-4"><!-- collapse menu button -->
        <div id="hide-menu" class="btn-header pull-right">
            <span> <a title="Collapse Menu" class="cursor-poiter" data-action="toggleMenu"><i class="fa fa-reorder"></i></a> </span>
        </div><!-- end collapse menu --><!-- #MOBILE --><!-- Top menu profile link : this shows only when top menu is active -->
        <ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
            <li>
                <a class="dropdown-toggle no-margin userdropdown" href="#" data-toggle="dropdown">
                    <img src="<?php echo base_url();?>assets/smartAdminTemplate/img/avatars/avatar_generic.png" alt="John Doe" class="online" />
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a class="padding-10 padding-top-0 padding-bottom-0 cursor-poiter"><i class="fa fa-cog"></i> Setting</a>
                    </li><li class="divider">
                    </li><li>
                        <a class="padding-10 padding-top-0 padding-bottom-0" href="profile.html"> <i class="fa fa-user"></i> <u>P</u>rofile</a>
                    </li><li class="divider">
                    </li><li>
                        <a class="padding-10 padding-top-0 padding-bottom-0 cursor-poiter" data-action="toggleShortcut"><i class="fa fa-arrow-down"></i> <u>S</u>hortcut</a>
                    </li><li class="divider">
                    </li><li>
                        <a class="padding-10 padding-top-0 padding-bottom-0 cursor-poiter" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i> Full <u>S</u>creen</a>
                    </li><li class="divider">
                    </li><li>
                        <a class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-sign-out fa-lg"></i> <strong><u>L</u>ogout</strong></a>
                    </li>
                </ul>
            </li>
        </ul><!-- logout button -->
        <div id="logout" class="btn-header transparent pull-right">
            <span><a title="Sign Out" aria-label="Signout" class="cursor-poiter" href="<?php echo base_url();?>account/logout"   data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i> </a> </span>
        </div><!-- end logout button --><!-- fullscreen button -->
        <div id="fullscreen" class="btn-header transparent pull-right">
            <span> <a title="Full Screen" class="cursor-poiter" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i></a> </span>
        </div><!-- end fullscreen button -->
    </div>

