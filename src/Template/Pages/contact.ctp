<script src='https://www.google.com/recaptcha/api.js?hl=fi'></script>
<!-- How it works tabs content -->
<div class="container" id="body-content">
<h2><?php echo __('CONTACT'); ?></h2>
<p class="text-muted"><?php echo __('MeSTRADA Oy / Pietu Halonen'); ?><br /><?php echo __('040-5866 105'); ?><br /></p>
<?= $this->Flash->render() ?>
<div class="row">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <h3><?php echo __('Get in Touch'); ?></h3>
        <?php 
        echo $this->Form->create('', [
            'horizontal' => true,
            'id' => 'contactForm',
            'novalidate' => 'novalidate',
            'url' => '/users/contact'
        ]);
        ?>
            <div class="alert alert-danger" id="error-message" style="display: none;"></div>
            <div class="form-group">
                <label for="" class="col-sm-4 control-label"><?php echo __('YOUR_EMAIL'); ?></label>
                <div class="col-sm-7">
                    <input type="email" name="email" class="form-control" id="email" placeholder="">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-4 control-label"><?php echo __('MESSAGE'); ?></label>
                <div class="col-sm-7">
                    <textarea name="message" id="message" rows="5" class="form-control"></textarea>
                </div>
            </div>
            <div class="form-group required">
                <div class="col-sm-offset-4 col-sm-7">
                    <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SECRET_KEY; ?>"></div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-7">
                    <button type="submit" id="submitForm" class="btn btn-success btn-lg"><?php echo __('SEND_MESSAGE'); ?></button>
                </div>
            </div>
        <?php echo $this->Form->end(); ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <img src="<?php echo $this->request->webroot; ?>img/bg-girl.png" class="img-responsive" />
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>
<?= $this->Html->script('contact', array('inline' => false)); ?>