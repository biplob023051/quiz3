<?= $this->Html->script(['buy'.$minify], ['inline' => false]); ?>
<!-- How it works tabs content -->
<div class="container" id="body-content">
    <h2><?php echo __('PRICES'); ?></h2>
    <p class="text-muted"><?php echo __('SELECT_PACKAGE'); ?></p>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th><?php echo __('FREE'); ?></th>
                        <th><?php echo __('19_EUR'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo __('USERS'); ?></td>
                        <td><?php echo __('1'); ?></td>
                        <td><?php echo __('1'); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('TESTS'); ?></td>
                        <td><?php echo __('1'); ?></td>
                        <td><?php echo __('UNLIMITED'); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('STUDENTS_TEST'); ?></td>
                        <td><?php echo __('40'); ?></td>
                        <td><?php echo __('UNLIMITED'); ?></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><?php echo $this->Html->link(__('REGISTER_NOW'), '/user/create', array('class' => 'btn btn-success')); ?></td>
                        <td><button type="button" id="buy-button" class="btn btn-success"><?php echo __('BUY'); ?></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="text-center">
                <img src="<?php echo $this->request->webroot; ?>img/bg-girl.png" class="img-responsive" />
            </div>
        </div>
    </div>
</div>
<?php echo $this->element('Page/buy_modal', array('package' => __('19_EUR'))); ?>
<script id="app-data" type="application/json">
    <?php
    echo json_encode(array(
        'baseUrl' => $this->Html->url('/', true)
    ));
    ?>
</script>
<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>