<?php
    use Cake\Routing\Router;
?>
<?= $this->Flash->render() ?>
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
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0"  class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center col-md-1"><?= __('id'); ?></th>
                        <th class="text-center"><?= __('Main Title'); ?></th>
                        <th class="text-center"><?= __('Created'); ?></th>
                        <th class="text-center col-md-1"><?= __('Sort'); ?></th>
                        <th class="text-center col-md-1"><?= __('Status'); ?></th>
                        <th class="text-center col-md-1"><?= __('Action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($helps as $help): ?>
                        <tr>
                            <td class="text-center"><?php echo h($help->id); ?></td>
                            <td class="text-center"><?php echo $this->Html->link(h($help->title), array('action' => 'index', $help->id), array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?></td>
                            <td class="text-center"><?php echo h($help->created); ?></td>
                            <td class="text-center" nowrap="nowrap">
                                    <?php echo $this->Form->postLink('<span class="glyphicon glyphicon-arrow-up"></span>', array('action' => 'moveup', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))), array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                    <?php echo $this->Form->postLink('<span class="glyphicon glyphicon-arrow-down"></span>', array('action' => 'movedown', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))), array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                            </td>
                            <td class="text-center" nowrap="nowrap">
                                <?php if($help->status):?>
                                    <?= $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs active">'.__('On').'</button><button type="button" class="btn btn-default btn-xs inactive">'.__('Off').'</button></div>', ['action' => 'active', $help->id,'?'=> ['redirect_url'=>urlencode(Router::reverse($this->request, true))]], ['escape'=>false, 'confirm' => __("Confirm inactive title ''{0}''", $help->title)]); ?>
                                <?php else :?>
                                    <?= $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs inactive">'.__('On').'</button><button type="button" class="btn btn-default btn-xs active">'.__('Off').'</button></div>', ['action' => 'active', $help->id,1,'?'=>['redirect_url'=>urlencode(Router::reverse($this->request, true))]], ['escape'=>false, 'confirm' => __("Confirm active title ''{0}''", $help->title)]); ?>
                                <?php endif;?>
                            </td>
                            <td class="text-center" nowrap="nowrap">
                                <?php echo $this->Html->link(__('Edit'), array('action' => 'add', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                <?php if(!$help->status):?>
                                    <?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $help->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-danger btn-xs','escape'=>false, 'confirm' => __("Confirm delete of title ''{0}''?", trim($help->title)))); ?>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>


