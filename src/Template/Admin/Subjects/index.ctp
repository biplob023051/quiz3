<?php use Cake\Routing\Router; ?>
<?= $this->Flash->render(); ?>
<?php $this->assign('title', $title_for_layout); ?>
<?php $languages = $this->Quiz->allLanguages(); ?>
<div class="row">
    <div class="col-sm-12">
        <ul class="nav nav-pills">
            <li><?= $this->Html->link(__('ALL_SUBJECT'), ['controller'=>'subjects','action'=>'index'], ["role"=>"button", "class"=>"btn btn-link"]);?></li> 
            <li><?= $this->Html->link(__('NEW_SUBJECT'), ['controller'=>'subjects','action'=>'insert'], ["role"=>"button", "class"=>"btn btn-link"]);?></li> 
        </ul>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12 text-right font-10 font-bold">
                <?php 
                    echo $this->Paginator->counter(
                        'Page {{page}} of {{pages}}, showing {{current}} records out of
                         {{count}} total, starting on record {{start}}, ending on {{end}}'
                    ); 
                ?>
            </div>
        </div> 
        <br>
        <div class="table-responsive">
            <table cellpadding="0" cellspacing="0"  class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center col-md-1"><?= $this->Paginator->sort('id', __('ID')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('title', __('TITLE')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('language', __('LANGUAGE')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('created', __('CREATED')); ?></th>
                        <th class="text-center col-md-1"><?= $this->Paginator->sort('isactive', __('STATUS')); ?></th>
                        <th class="text-center col-md-1"><?= __('ACTION'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($subjects)) : ?>
                        <tr><td colspan="5"><?= __('Subject not found'); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ($subjects as $subject): ?>
                            <tr>
                                <td class="text-center"><?= h($subject->id); ?></td>
                                <td class="text-center"><?= h($subject->title); ?></td>
                                <td class="text-center"><?= h($languages[$subject->language]); ?></td>
                                <td class="text-center"><?= h($subject->created); ?></td>
                                 <td class="text-center" nowrap="nowrap">
                                    <?php if($subject->isactive):?>
                                        <?= $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs active">'.__('ON').'</button><button type="button" class="btn btn-default btn-xs inactive">'.__('OFF').'</button></div>', ['action' => 'active', $subject->id,'?'=> ['redirect_url'=>urlencode(Router::reverse($this->request, true))]], ['escape'=>false, 'confirm' => __("Confirm inactive subject ''{0}''", $subject->title)]); ?>
                                    <?php else :?>
                                        <?= $this->Form->postLink('<div class="btn-group"><button type="button" class="btn btn-default btn-xs inactive">'.__('ON').'</button><button type="button" class="btn btn-default btn-xs active">'.__('OFF').'</button></div>', ['action' => 'active', $subject->id,1,'?'=>['redirect_url'=>urlencode(Router::reverse($this->request, true))]], ['escape'=>false, 'confirm' => __("Confirm active subject ''{0}''", $subject->title)]); ?>
                                    <?php endif;?>
                                </td>
                                <td class="text-center" nowrap="nowrap">
                                    <?= $this->Html->link(__('EDIT'), array('action' => 'insert', $subject->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))),array('class'=>'btn btn-primary btn-xs','escape'=>false)); ?>
                                    <?php if(!$subject->isactive):?>
                                        <?= $this->Form->postLink(__('DELETE'), array('action' => 'delete', $subject->id,'?'=>array('redirect_url'=>urlencode(Router::reverse($this->request, true)))), ['class'=>'btn btn-danger btn-xs','escape'=>false, 'confirm' => __("Confirm delete subject ''{0}''", $subject->title)]); ?>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="row">
            <div class="col-md-12 text-center">
                <ul class="pagination pagination-sm">
                    <?php 
                    echo $this->Paginator->prev('&larr; ' . __('PREVIOUS'),array('tag'=>'li','escape'=>false),'<a>&larr; '. __('PREVIOUS') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));
                    echo $this->Paginator->numbers(array('tag'=>'li','separator'=>null,'currentClass'=>'active','currentTag'=>'a','modulus'=>'4','first' => 2, 'last' => 2,'ellipsis'=>'<li><a>...</a></li>'));
                    echo $this->Paginator->next(__('NEXT') . ' &rarr;',array('tag'=>'li','escape'=>false),'<a>&rarr; '. __('NEXT') .'</a>',array('class'=>'disabled','tag'=>'li','escape'=>false));
                    ?>
                </ul>
            </div>
        </div>

        
    </div>
</div>