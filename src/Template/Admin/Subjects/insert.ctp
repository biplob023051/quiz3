<?= $this->Flash->render(); ?>
<?php $this->assign('title', $title_for_layout); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?= $this->Html->link(__('All Subjects'), ['controller'=>'subjects','action'=>'index'], ["role"=>"button", "class"=>"btn btn-link"]);?></li> 
            <li><?= $this->Html->link(__('New Subject'), ['controller'=>'subjects','action'=>'insert'], ["role"=>"button", "class"=>"btn btn-link"]);?></li> 
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <?php 
            echo $this->Form->create($subject, [
                'novalidate' => true
            ]); 
            echo $this->Form->input('title', array('label'=>array('text'=>__('Title'), 'class' => 'col-md-3'), 'placeholder' => __('Please enter subject name')));
            echo $this->Form->button(__('SAVE'), ['class' => 'btn btn-primary btn-xlarge', 'escape' => true]) . ' ';
            echo empty($this->request->query['redirect_url']) ? $this->Html->link(__('BACK'), ['controller'=>'subjects','action'=>'index'], ['class'=>'btn btn-danger']) : $this->Html->link(__('BACK'), urldecode($this->request->query['redirect_url']), ['class'=>'btn btn-danger']); 
        ?>
        <?= $this->Form->end(); ?>
    </div>
</div>