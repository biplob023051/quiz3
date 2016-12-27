<?= $this->Html->script(['user-create'], ['inline' => false]); ?>

<?php
$session = $this->request->session();
// pr($session);
// exit;
$this->assign('title', __('Create Account'));
?>
<?= $this->Flash->render() ?>
<div class="row">
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
        <div class="alert alert-danger" id="error-message" style="display : none;"></div>
        <div id="email-exist" style="display : none;">
            <div class="col-sm-4 col-xs-12"><?php echo __('Already Registered?'); ?></div>
            <div class="col-md-4 col-xs-12">
                <?php echo $this->Html->link(__('Login'), '/users/login'); ?>
            </div>
            <div class="col-md-4 col-xs-12">
                <?php echo $this->Html->link(__('Password Recover'), '/users/password_recover'); ?>
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
            'placeholder' => __('Enter Your Name'),
        ]);

        echo $this->Form->input('email', [
            'placeholder' => __('Enter Valid Email'),
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'data-original-title' => __('We\'ll send a confirmation email there')
        ]);

        echo $this->Form->input('password', [
            'type' => 'password',
            'placeholder' => __('Enter Password'),
            'data-toggle' => 'tooltip',
            'data-placement' => 'bottom',
            'data-original-title' => __('Password must be 8 characters long')
        ]);

        echo $this->Form->input('passwordVerify', [
            'type' => 'password',
            'placeholder' => __('Password Verify')
        ]);
        ?>

        <div class="form-group required">
            <label for="UserCaptcha" class="col-sm-4 control-label"><?php echo $captcha; ?></label>
            <div class="col-md-7 col-xs-12">
                <?php 
                    echo $this->Form->input('captcha', array(
                        'label' => false,
                        'placeholder' => __('Enter result')
                    )); 
                ?>
            </div>
        </div>
       
        <?php
        echo $this->Form->submit(__("Create Account & Log In"), ['class' => 'btn btn-success btn-block btn-lg', 'id' => 'create_acc']);
        ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <?php if (!empty($create_video)) : ?>
                <a href="javascript:void(0)" id="play_video"><img src="<?php echo $this->Quiz->getHelpPicture($create_video, 'videos'); ?>" class="img-responsive"></a>
                <?php echo $this->element('User/video_modal', array('create_video' => $create_video)); ?>
            <?php else: ?>
                <?php echo $this->Html->image('bg-moniter.png', array('class' => 'img-responsive')); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
    <?php if (!empty($create_video)) : ?>
        var url_src = <?php echo json_encode($create_video['Help']['url_src']) ?>;
    <?php else: ?>
        var url_src = '';
    <?php endif; ?>

</script>

<style type="text/css">
    .form-group.required .control-label:after {
        content:"*";
        color:red;
    }
</style>