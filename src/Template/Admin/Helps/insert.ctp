<?= $this->Html->script(['tinymce/tinymce.min', 'admin-insert-help'.$minify], ['inline' => false]); ?>
<?= $this->Flash->render(); ?>
<?php $this->assign('title', $title_for_layout); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?php echo $this->Html->link(__('MAIN_TITLE_LIST'),array('controller'=>'helps','action'=>'titles'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('NEW_MAIN_TITLE'),array('controller'=>'helps','action'=>'add'),array("role"=>"button", "class"=>"btn btn-link"));?></li>
            <li><?php echo $this->Html->link(__('HELPS_LIST'),array('controller'=>'helps','action'=>'index'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
            <li><?php echo $this->Html->link(__('NEW_HELP'),array('controller'=>'helps','action'=>'insert'),array("role"=>"button", "class"=>"btn btn-link"));?></li> 
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
            echo $this->Form->input('parent_id', array('label'=>array('text'=>__('MAIN_TITLE')),'options' => $parentsOptions, 'empty' => __('SELECT_ONE') ));
            echo $this->Form->input('title', array('label'=>array('text'=>__('TITLE')), 'placeholder' => __('INSERT_HELP_TITLE')));
            echo $this->Form->input('sub_title', array('label'=>array('text'=>__('SUB_TITLE')), 'placeholder' => __('INSERT_HELP_SUB_TITLE')));
            echo $this->Form->input('url', array('label'=>array('text'=>__('YOUTUBE_URL')), 'placeholder' => __('INSERT_YOUTUBE_URL_HERE')));
            echo $this->Form->input('body', array('label'=>array('text'=>__('DESCRIPTION')), 'placeholder' => __('Description here'), 'type' => 'textarea'));   
            echo $this->Form->button(__('SAVE'), ['class' => 'btn btn-primary btn-xlarge']) . ' '; 
            echo empty($this->request->query['redirect_url']) ? $this->Html->link(__('BACK'),array('controller'=>'helps','action'=>'index'),array('class'=>'btn btn-danger')) : $this->Html->link(__('BACK'), urldecode($this->request->query['redirect_url']),array('class'=>'btn btn-danger'));
            echo $this->Form->end();
            ?>
    </div>
</div>