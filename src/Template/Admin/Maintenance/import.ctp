<?php $this->assign('title', $title_for_layout); ?>
<?= $this->Flash->render(); ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <?php 
            echo $this->Form->create('', ['novalidate'=>'novalidate']);
            echo $this->Form->input('user_id', array('type' => 'number', 'label'=>array('text'=>__('USER_ID')), 'placeholder' => __('INSERT_USER_ID')));

            echo $this->Form->submit(__('IMPORT'), array(
                'div' => false,
                'class' => 'btn btn-primary btn-xlarge'
            ));
            echo $this->Form->end(); 
        ?>
    </div>
</div>