<ul class="user-info pull-left pull-none-xsm">

                        <!-- Profile Info -->
                        <li class="profile-info dropdown">
                            <!-- add class "pull-right" if you want to place this from right -->

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="/assets/images/thumb-1.png" alt="" class="img-circle" width="44" />
                                <?=$userFullName;?>
                            </a>

                            <ul class="dropdown-menu">

                                <!-- Reverse Caret -->
                                <li class="caret"></li>

                                <!-- Profile sub-links -->
                                <li>
                                    <a href="<?=site_url('user/edit/'.$user_id)?>">
                                        <i class="entypo-user"></i>
                                        <?=get_phrase('edit_profile');?>
                                    </a>
                                </li>

                                <!-- <li>
                                    <a href="mailbox.html">
                                        <i class="entypo-mail"></i>
                                        Inbox
                                    </a>
                                </li>

                                <li>
                                    <a href="extra-calendar.html">
                                        <i class="entypo-calendar"></i>
                                        Calendar
                                    </a>
                                </li>

                                <li>
                                    <a href="#">
                                        <i class="entypo-clipboard"></i>
                                        Tasks
                                    </a>
                                </li> -->
                            </ul>
                        </li>

                    </ul>