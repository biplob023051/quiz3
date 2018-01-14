 <!-- If you're using Stripe for payments -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
<script src="https://js.stripe.com/v2/"></script>
<script src="https://js.stripe.com/v3/"></script>
<?php if (empty($eng_domain)) : ?>
    <?= $this->Html->script(['jquery.uploadfile.min'], ['inline' => false]); ?>
    <?= $this->Html->css(['uploadfile'], ['inline' => false]); ?>
<?php endif; ?>
<!-- How it works tabs content -->
<div class="container" id="body-content">
    <h2><?= __('PRICES'); ?></h2>
    <p class="text-muted"><?= __('SELECT_PACKAGE'); ?></p>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <?= empty($eng_domain) ? $this->element('Page/prices_table_general') : $this->element('Page/prices_table_eng'); ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="text-center">
                <img src="<?= $this->request->webroot; ?>img/bg-girl.png" class="img-responsive" />
            </div>
        </div>
    </div>
</div>
<?= empty($eng_domain) ? $this->element('Page/buy_modal') : $this->element('Page/buy_modal_bank'); ?>
<?= $this->element('Invoice/invoice_success_dialog'); ?>
<script type="text/javascript">
    var lang_strings = <?= json_encode($lang_strings) ?>;
</script>

<?= empty($eng_domain) ? $this->Html->script(['buy'.$minify], ['inline' => false]) : $this->Html->script(['buy_bank'.$minify], ['inline' => false]); ?>

<style>
    .ajax-file-upload-statusbar {
        border: none !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .ajax-file-upload-filename {
        width: 100% !important;
    }
</style>