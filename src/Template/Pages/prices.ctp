 <!-- If you're using Stripe for payments -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
<script src="https://js.stripe.com/v2/"></script>
<script src="https://js.stripe.com/v3/"></script>
<?= $this->Html->script(['jquery.uploadfile.min'], ['inline' => false]); ?>
<?= $this->Html->css(['uploadfile'], ['inline' => false]); ?>
<!-- How it works tabs content -->
<div class="container" id="body-content">
    <h2><?= __('PRICES'); ?></h2>
    <p class="text-muted"><?= __('SELECT_PACKAGE'); ?></p>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th><?= __('FREE'); ?></th>
                        <th><?= __('29_EUR'); ?></th>
                        <th><?= __('49_EUR'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= __('USERS'); ?></td>
                        <td><?= __('1'); ?></td>
                        <td><?= __('1'); ?></td>
                        <td><?= __('1'); ?></td>
                    </tr>
                    <tr>
                        <td><?= __('TESTS'); ?></td>
                        <td><?= __('1'); ?></td>
                        <td><?= __('UNLIMITED'); ?></td>
                        <td><?= __('UNLIMITED'); ?></td>
                    </tr>
                    <tr>
                        <td><?= __('DAYS_TO_USE'); ?></td>
                        <td><?= '30 ' . __('DAYS'); ?></td>
                        <td><?= '365 ' . __('DAYS'); ?></td>
                        <td><?= '365 ' . __('DAYS'); ?></td>
                    </tr>
                    <tr>
                        <td><?= __('QUIZ_BANK'); ?></td>
                        <td><?= __('LIMITED_ACCESS'); ?></td>
                        <td><?= '-'; ?></td>
                        <td><?= __('UNLIMITED'); ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><?= $this->Html->link(__('REGISTER_NOW'), '/users/create', array('class' => 'btn btn-success')); ?></td>
                        <td><button type="button" id="buy-button-29" class="btn btn-success"><?= __('BUY'); ?></button></td>
                        <td><button type="button" id="buy-button-49" class="btn btn-success"><?= __('BUY'); ?></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="text-center">
                <img src="<?= $this->request->webroot; ?>img/bg-girl.png" class="img-responsive" />
            </div>
        </div>
    </div>
</div>
<?= $this->element('Page/buy_modal'); ?>
<?= $this->element('Invoice/invoice_success_dialog'); ?>
<script id="app-data" type="application/json">
    <?php
    // echo json_encode(array(
    //     'baseUrl' => $this->Html->url('/', true)
    // ));
    ?>
</script>
<script type="text/javascript">
    var lang_strings = <?= json_encode($lang_strings) ?>;
</script>

<?= $this->Html->script('buy', array('inline' => false)); ?>