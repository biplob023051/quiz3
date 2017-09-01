<script src='https://www.google.com/recaptcha/api.js?hl=<?php echo $site_language; ?>'></script>
<?php
$session = $this->request->session();
// pr($session);
// exit;
$this->assign('title', __('CREATE_ACCOUNT'));
?>
<?= $this->Flash->render() ?>
<div class="row">
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
        <div class="alert alert-danger" id="error-message" style="display : none;"></div>
        <div id="email-exist" style="display : none;">
            <div class="col-sm-4 col-xs-12"><?= __('ALREADY_REGISTERED'); ?></div>
            <div class="col-md-4 col-xs-12">
                <?= $this->Html->link(__('LOGGIN'), '/users/login'); ?>
            </div>
            <div class="col-md-4 col-xs-12">
                <?= $this->Html->link(__('RECOVER_PASSWORD'), '/users/password_recover'); ?>
            </div>
        </div>
        <?php
        echo $this->Form->create($user, [
            'horizontal' => true,
            'id' => 'UserCreateForm',
            'columns' => [ 
                'sm' => [
                    'label' => 4,
                    'input' => 8,
                    'error' => 8
                ],
                'md' => [
                    'label' => 4,
                    'input' => 8,
                    'error' => 8
                ]
            ],
            'novalidate' => 'novalidate'
        ]);

        echo $this->Form->input('name', [
            'label' => ['text' => __('NAME')],
            'placeholder' => __('ENTER_YOUR_NAME'),
        ]);

        echo $this->Form->input('email', [
            'label' => ['text' => __('EMAIL')],
            'placeholder' => __('ENTER_VALID_EMAIL'),
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'data-original-title' => __('We\'ll send a confirmation email there')
        ]);

        echo $this->Form->input('password', [
            'label' => ['text' => __('PASSWORD')],
            'type' => 'password',
            'placeholder' => __('ENTER_PASSWORD'),
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'data-original-title' => __('PASSWORD_MUST_BE_LONGER')
        ]);

        echo $this->Form->input('passwordVerify', [
            'label' => ['text' => __('PASSWORD_VERIFY')],
            'type' => 'password',
            'placeholder' => __('PASSWORD_VERIFY')
        ]);
        ?>

        <div class="form-group required">
            <div class="col-sm-offset-4 col-sm-7">
                <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_SECRET_KEY; ?>"></div>
            </div>
        </div>
       
        <?= $this->Form->submit(__("CREATE_ACCOUNT_LOG_IN"), ['class' => 'btn btn-success btn-block btn-lg', 'id' => 'create_acc']); ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <?php if (!empty($create_video)) : ?>
                <a href="javascript:void(0)" id="play_video"><img src="<?php echo $this->Quiz->getHelpPicture($create_video, 'videos'); ?>" class="img-responsive"></a>
                <?php echo $this->element('User/video_modal', array('create_video' => $create_video)); ?>
            <?php else: ?>
                <?php //echo $this->Html->image('bg-moniter.png', array('class' => 'img-responsive')); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings); ?>;
    <?php if (!empty($create_video)) : ?>
        var url_src = '<?php echo $create_video['url_src']; ?>';
    <?php else: ?>
        var url_src = '';
    <?php endif; ?>
</script>

<?= $this->Html->script(['video', 'user-create'], ['inline' => false]); ?>

<style type="text/css">
    .form-group.required .control-label:after {
        content:"*";
        color:red;
    }
</style>