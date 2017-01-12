<?= $this->Html->script(array('tinymce/tinymce.min', 'admin-insert-help'), array('inline' => false)); ?>
<?= $this->Flash->render(); ?>
<?php $this->assign('title', $title_for_layout); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?php echo $this->Html->link(__('Main Title List'),array('controller'=>'helps','action'=>'titles'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('New Main Title'),array('controller'=>'helps','action'=>'add'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('Helps List'),array('controller'=>'helps','action'=>'index'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
            <li><?php echo $this->Html->link(__('New Help'),array('controller'=>'helps','action'=>'insert'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <?php 
            echo $this->Form->create($help, ['novalidate'=>'novalidate']);
            echo $this->Form->input('parent_id', array('label'=>array('text'=>__('Main Title')),'options' => $parentsOptions, 'empty' => __('Select One') ));
            echo $this->Form->input('title', array('label'=>array('text'=>__('Title')), 'placeholder' => __('Please insert help title')));
            echo $this->Form->input('sub_title', array('label'=>array('text'=>__('Sub Title')), 'placeholder' => __('Please insert help sub title')));
            echo $this->Form->input('url', array('label'=>array('text'=>__('Youtube Video Url')), 'placeholder' => __('Please insert youtube video url here')));
            echo $this->Form->input('body', array('label'=>array('text'=>__('Description')), 'placeholder' => __('Description here'), 'type' => 'textarea'));   
            echo $this->Form->button(__('SAVE'), ['class' => 'btn btn-primary btn-xlarge']) . ' '; 
            echo empty($this->request->query['redirect_url']) ? $this->Html->link(__('BACK'),array('controller'=>'helps','action'=>'index'),array('class'=>'btn btn-danger')) : $this->Html->link(__('BACK'), urldecode($this->request->query['redirect_url']),array('class'=>'btn btn-danger'));
            echo $this->Form->end();
            ?>
    </div>
</div>