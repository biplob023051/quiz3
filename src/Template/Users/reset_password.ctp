<?php
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
                'placeholder' => __('Enter New Password'),
                'type' => 'password',
                'data-toggle' => 'tooltip',
                'data-placement' => 'bottom',
                'data-original-title' => __('Password must be 8 characters long')
            ));
            echo $this->Form->input('passwordVerify', array('placeholder' => __('New Password Verify'),'type'=>'password'));

            // echo $this->Form->end(array(
            //     'label' => __("Reset"),
            //     'div' => array('class' => 'col-md-7 col-md-offset-4 col-xs-12'),
            //     'before' => '<div class="form-group">',
            //     'after' => '</div>',
            //     'class' => 'btn btn-success btn-block btn-lg'
            // ));

            echo $this->Form->submit(__("Reset"), ['before' => '<div class="form-group">', 'div' => array('class' => 'col-md-7 col-md-offset-4 col-xs-12'), 'after' => '</div>', 'class' => 'btn btn-success btn-block btn-lg'])

            echo $this->Form->end();
        ?>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="text-center">
            <?php echo $this->Html->image('bg-moniter.png', array('class' => 'img-responsive')); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    var lang_strings = <?php echo json_encode($lang_strings) ?>;
</script>