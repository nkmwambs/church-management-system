<?= $this->extend('layouts/default') ?>

<?= $this->section('title') ?>
<?=view_cell('App\Cells\TitleCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('sidebar') ?>
<?=view_cell('App\Cells\SideBarCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('profile_info') ?>
<?=view_cell('App\Cells\ProfileInfoCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('raw_notification') ?>
<?=view_cell('App\Cells\RawNotificationCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('message_notification') ?>
<?=view_cell('App\Cells\MessageNotificationCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('task_notification') ?>
<?=view_cell('App\Cells\TaskNotificationCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('language_selector') ?>
<?=view_cell('App\Cells\LanguageSelectorCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('chat') ?>
<?=view_cell('App\Cells\ChatCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <?php $session = session();?>
    <?=view_cell('App\Cells\ContentCell::show', ['page_data' => $page_data]);?>
<?= $this->endSection() ?>

<?= $this->section('footer') ?>
<?=view_cell('App\Cells\FooterCell::show', []);?>
<?= $this->endSection() ?>

<?= $this->section('conversations') ?>
<?=view_cell('App\Cells\ConversationsCell::show', []);?>
<?= $this->endSection() ?>


