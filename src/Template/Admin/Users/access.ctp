<?php $this->assign('title', __('Admin Access')); ?>
<div id="login-container">
    <?= $this->Flash->render(); ?>
    <?= $this->Form->create(); ?>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php
            echo $this->Form->input('email', array(
                'label' => __("Email")
            ));
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <?php
            echo $this->Form->input('password', array(
                'label' => __("Password")
            ));
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="form-group">
                <button type="submit" class="btn btn-success btn-lg btn-block"><?php echo __('Login') ?></button>
            </div>
        </div>
    </div>

    <?php echo $this->Form->end(); ?>
</div>