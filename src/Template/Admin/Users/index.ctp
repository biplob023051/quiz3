<style>
    .update-user {
      display: none;
      width: 90px;
    }
    .user-info {
        cursor: pointer;
    }
    .inactive-row {
        color: #bbb;
    }
</style>
<?php 
    use Cake\Routing\Router; 
    use Carbon\Carbon;
?>
<?php $this->assign('title', $title_for_layout); ?>

<?= $this->Flash->render(); ?>
<?php 
    $allAccOptions = [
        '0' => __('TRIAL_LIMIT'),
        '1' => __('PAID_29'),
        '2' => __('PAID_49'),
        '22' => __('TRIAL_DAYS'),
        '51' => __('ADMIN')
    ];

    $accountOptions = [
        '0' => __('TRIAL_LIMIT'),
        '1' => __('PAID_29'),
        '2' => __('PAID_49'),
        '22' => __('TRIAL_DAYS')
    ];

    $accoutLists = [
        'all' => __('ALL'),
        'active' => __('ACTIVE'),
        'inactive' => __('INACTIVE'),
        'expired' => __('EXPIRED'),
        'paid' => __('PAID'),
        'trial_days' => __('TRIAL_DAYS'),
        'trial_limit' => __('TRIAL_LIMIT'),
    ];
    $limitOptions = [50 => 50, 100 => 100, 500 => 500, 1000 => 1000];
?>
<div class="panel panel-default" id="manage-user">
    <!-- <div class="panel-heading">
        <h3 class="panel-name"><span class="glyphicon glyphicon-th"></span> <b><?= $title_for_layout;?></b></h3>
    </div> -->
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <?= $this->Form->create(''); ?>
                <?= $this->Form->input('limit_size', ['options' => $limitOptions, 'class' => 'on-select', 'label' => false]); ?>
                <?= $this->Form->end(); ?>
            </div>
            <div class="col-md-3 col-md-offset-6">
                <?= $this->Form->create(''); ?>
                <?= $this->Form->input('acc_type', ['options' => $accoutLists, 'class' => 'on-select', 'label' => false]); ?>
                <?= $this->Form->end(); ?>
            </div>
        </div> 
        <hr>
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
                        <th class="text-center" style="width: 1%"><?= $this->Paginator->sort('id', __('ID')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('name', __('NAME')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('email', __('EMAIL')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('UserStatistic.created', __('LAST_LOGIN'), ['model' => 'UserStatistic']); ?></th>
                        
                        <th class="text-center"><?= __('LOGIN_COUNT'); ?></th>
                        <th class="text-center"><?= __('QUIZ_COUNT'); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('account_level', __('ACC_TYPE')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('created', __('CREATED')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('expired', __('EXP_DATE')); ?></th>
                        <th class="text-center" style="width: 1%"><?= $this->Paginator->sort('isactive', __('STATUS')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)) : ?>
                        <tr><td colspan="10"><?= __('USERS_NOT_FOUND'); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ($users as $user): ?>
                            <?php if ($user->account_level != 51) : ?>
                                <tr id="user-<?= $user->id; ?>" class="<?= ($user->isactive) ? '' : 'inactive-row'; ?>">
                                    <td class="text-center"><?= $user->id; ?></td>
                                    <td class="text-center"><span class="user-info"><?= h($user->name); ?> <i class="glyphicon pencil-small"></i></span><input type="text" placeholder="<?= __('ENTER_NAME'); ?>" class="form-control update-user" name="class" data-rel="name-<?= $user->id; ?>" value="<?= $user->name; ?>" data-value="<?= $user->name ?>"></td>
                                    <td class="text-center"><?= h($user->email); ?></td>
                                    <td class="text-center"><?= !empty($user->user_statistic->created) ? h($user->user_statistic->created->todatestring()) : ''; ?></td>
                                    <td class="text-center"><?= !empty($user->statistics[0]['total_login']) ? $user->statistics[0]['total_login'] : '0'; ?></td>
                                    <td class="text-center"><?= !empty($user->quizzes[0]['total_quiz']) ? $user->quizzes[0]['total_quiz'] : '0'; ?></td>
                                    <td class="text-center">
                                        <span class="user-info"><?= $allAccOptions[$user->account_level]; ?> <i class="glyphicon pencil-small"></i></span>
                                        <select name="account_level" class="form-control change-level update-user" data-rel="account_level-<?= $user->id; ?>" data-value="<?= $user->account_level; ?>">
                                            <?php foreach ($accountOptions as $key => $option) {
                                                echo ($user->account_level != $key) ? '<option value="'. $key .'">'. $option .'</option>' : '<option selected="selected" value="'. $key .'">'. $option .'</option>';
                                            } ?>
                                        </select>
                                    </td>
                                    <td class="text-center"><?= $user->created->todatestring(); ?></td>
                                    <td class="text-center">
                                        <?php if ($user->expired) : ?>
                                            <span class="user-info"><?= h($user->expired->todatestring()); ?> <i class="glyphicon pencil-small"></i></span><input type="text" placeholder="<?= __('ENTER_EXPIRE_DATE'); ?>" class="form-control update-user" name="class" data-rel="expired-<?= $user->id; ?>" value="<?= $user->expired->todatestring(); ?>" data-value="<?= $user->expired->todatestring(); ?>">
                                        <?php else : ?>
                                            <span class="user-info"><?= __('NO_EXPIRE_DATE'); ?> <i class="glyphicon pencil-small"></i></span><input type="text" placeholder="<?= __('ENTER_EXPIRE_DATE'); ?>" class="form-control update-user" name="class" data-rel="expired-<?= $user->id; ?>" value="0000-00-00" data-value="0000-00-00">
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center" nowrap="nowrap">
                                        <?= $this->Form->checkbox('isactive', ['checked' => $user->isactive, 'class' => 'make-inactive', 'data-rel' => 'isactive-' . $user->id, 'data-value' => $user->isactive]); ?>
                                    </td>
                                </tr>
                            <?php else : ?>
                                <tr">
                                    <td class="text-center"><?= $user->id; ?></td>
                                    <td class="text-center"><?= h($user->name); ?></td>
                                    <td class="text-center"><?= h($user->email); ?></td>
                                    <td class="text-center"><?= !empty($user->user_statistic->created) ? h($user->user_statistic->created->todatestring()) : ''; ?></td>
                                    <td class="text-center"><?= !empty($user->statistics[0]['total_login']) ? $user->statistics[0]['total_login'] : '0'; ?></td>
                                    <td class="text-center"><?= !empty($user->quizzes[0]['total_quiz']) ? $user->quizzes[0]['total_quiz'] : '0'; ?></td>
                                    <td class="text-center"><?= $allAccOptions[$user->account_level]; ?></td>
                                    <td class="text-center"><?= $user->created ? $user->created->todatestring() : ''; ?></td>
                                    <td class="text-center"><?= $user->expired ? $user->expired->todatestring() : ''; ?></td>
                                    <td class="text-center" nowrap="nowrap">
                                        
                                    </td>
                                </tr>
                            <?php endif; ?>
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
<?= $this->element('Admin/confirm'); ?>
<?= $this->element('Admin/active-inactive'); ?>
<script>
    var lang_strings = <?= json_encode($lang_strings); ?>;
</script>
<?= $this->Html->script('user-index', ['inline' => false]); ?>