<?php
$this->assign('title', $title_for_layout);
echo $this->Html->script('password-recover', array('inline' => false));
echo $this->Flash->render();
?>
<div class="row">
    <div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
        <div class="alert alert-danger" id="error-message" style="display: none;"></div>
        <?php
            echo $this->Form->create($user, [
                'horizontal' => true,
                'id' => 'UserResetPasswordForm',
                'columns' => [ 
                    'sm' => [
                        'label' => 4,
                        'input' => 7,
                        'error' => 7
                    ],
                    'md' => [
                        'label' => 4,
                        'input' => 7,
                        'error' => 7
                    ]
                ],
                'novalidate' => 'novalidate',
            ]);

            echo $this->Form->input('id');
            echo $this->Form->input('password', array(
                'label' => array('text' => __('PASSWORD')),
                'placeholder' => __('ENTER_NEW_PASSWORD'),
                'type' => 'password',
                'data-toggle' => 'tooltip',
                'data-placement' => 'bottom',
                'data-original-title' => __('PASSWORD_MUST_BE_LONGER')
            ));
            echo $this->Form->input('passwordVerify', array(
                'label' => array('text' => __('PASSWORD_VERIFY')),
                'placeholder' => __('VERIFY_PASSWORD'),
                'type'=>'password'
            ));

            // echo $this->Form->end(array(
            //     'label' => __("Reset"),
            //     'div' => array('class' => 'col-md-7 col-md-offset-4 col-xs-12'),
            //     'before' => '<div class="form-group">',
            //     'after' => '</div>',
            //     'class' => 'btn btn-success btn-block btn-lg'
            // ));

            echo $this->Form->submit(__("RESET"), ['id' => 'do-reset', 'before' => '<div class="form-group">', 'div' => array('class' => 'col-md-7 col-md-offset-4 col-xs-12'), 'after' => '</div>', 'class' => 'btn btn-success btn-block btn-lg']);

            echo $this->Form->end();
        ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <?php //echo $this->Html->image('bg-moniter.png', array('class' => 'img-responsive')); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>