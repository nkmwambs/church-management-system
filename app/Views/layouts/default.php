<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Neon Admin Panel" />
    <meta name="author" content="" />
    <link rel="icon" href="/assets/images/favicon.ico">

    <title><?= $this->renderSection('title') ?></title>

    <?php include "include_top.php"; ?>
</head>

<body class="page-body  page-fade gray" data-url="http://neon.dev">

    <div class="page-container">
        <!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
        <?= $this->renderSection('sidebar') ?>

        <div class="main-content">
            <div class="row">
                <!-- Profile Info and Notifications -->
                <div class="col-md-6 col-sm-8 clearfix">

                    <?= $this->renderSection('profile_info') ?>

                    <ul class="user-info pull-left pull-right-xs pull-none-xsm">
                        <!-- Raw Notifications -->
                        <?= $this->renderSection('raw_notification') ?>
                        <!-- Message Notifications -->
                        <?= $this->renderSection('message_notification') ?>
                        <!-- Task Notifications -->
                        <?= $this->renderSection('task_notification') ?>
                    </ul>

                </div>


                <!-- Raw Links -->
                <div class="col-md-6 col-sm-4 clearfix hidden-xs">
                    <ul class="list-inline links-list pull-right">
                        <!-- Language Selector -->
                        <?= $this->renderSection('language_selector') ?>
                        <li class="sep"></li>
                        <?= $this->renderSection('chat') ?>
                        <li class="sep"></li>

                        <li>
                            <a href="<?php echo base_url(); ?>login/logout">
                                Log Out <i class="entypo-logout right"></i>
                            </a>
                        </li>
                    </ul>

                </div>

            </div>

            <hr />

            <?= $this->renderSection('content') ?>

            <!-- Footer -->
            <?= $this->renderSection('footer') ?>

        </div>

        <?= $this->renderSection('conversations') ?>

    </div>

    <?php include "include_bottom.php"; ?>

</body>

</html>