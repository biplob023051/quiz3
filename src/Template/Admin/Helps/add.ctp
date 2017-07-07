<?= $this->Flash->render(); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?= $this->Html->link(__('MAIN_TITLE_LIST'), ['controller'=>'helps','action'=>'titles'], ["role"=>"button", "class"=>"btn btn-link"]);?></li>
            <li><?= $this->Html->link(__('NEW_MAIN_TITLE'), ['controller'=>'helps','action'=>'add'], ["role"=>"button", "class"=>"btn btn-link"]);?></li>
            <li><?= $this->Html->link(__('HELPS_LIST'), ['controller'=>'helps','action'=>'index'], ["role"=>"button", "class"=>"btn btn-link"]);?></li> 
            <li><?= $this->Html->link(__('NEW_HELP'), ['controller'=>'helps','action'=>'insert'], ["role"=>"button", "class"=>"btn btn-link"]);?></li> 
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?php echo $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body"> 
        <?php 
            echo $this->Form->create($help, ['novalidate'=>'novalidate']); 
            echo $this->Form->input('id');
            echo $this->Form->input('title', array('label'=>array('text'=>__('TITLE')), 'placeholder' => __('INSERT_MAIN_TITLE')));
            echo $this->Form->button(__('SAVE'), ['class' => 'btn btn-primary btn-xlarge']) . ' ';
            echo empty($this->request->query['redirect_url']) ? $this->Html->link(__('BACK'),array('controller'=>'helps','action'=>'titles'),array('class'=>'btn btn-danger')) : $this->Html->link(__('BACK'),urldecode($this->request->query['redirect_url']),array('class'=>'btn btn-danger'));  
            echo $this->Form->end(); 
        ?>
    </div>
</div>