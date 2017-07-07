<?php $this->assign('title', __('LOGGIN')); ?>
<div id="login-container">
    <?= $this->Flash->render(); ?>
    <?= $this->Form->create(); ?>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php
            echo $this->Form->input('email', array(
                'label' => __("EMAIL")
            ));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php
            echo $this->Form->input('password', array(
                'label' => __("PASSWORD")
            ));
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg btn-block"><?= __('LOGGIN') ?></button>
            </div>
        </div>
    </div>

    <?= $this->Form->end(); ?>
</div>

<p class="text-center text-muted">
    <?= __('NOT_ACCOUNT'); ?> 
    <?= $this->Html->link(__('REGISTER_NOW'), '/users/create'); ?>
</p>
<p class="text-center text-muted">
    <?= __('FORGOT_PASSWORD'); ?> 
    <?= $this->Html->link(__('RECOVER_PASSWORD'), '/users/password_recover'); ?>
</p>



