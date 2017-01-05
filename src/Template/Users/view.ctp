<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete User'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Helps'), ['controller' => 'Helps', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Help'), ['controller' => 'Helps', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Imported Quizzes'), ['controller' => 'Downloads', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Imported Quiz'), ['controller' => 'Downloads', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Quizzes'), ['controller' => 'Quizzes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Quiz'), ['controller' => 'Quizzes', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Statistics'), ['controller' => 'Statistics', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Statistic'), ['controller' => 'Statistics', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="users view large-9 medium-8 columns content">
    <h3><?= h($user->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($user->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Password') ?></th>
            <td><?= h($user->password) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Language') ?></th>
            <td><?= h($user->language) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Reset Code') ?></th>
            <td><?= h($user->reset_code) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Activation') ?></th>
            <td><?= h($user->activation) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Account Level') ?></th>
            <td><?= $this->Number->format($user->account_level) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($user->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($user->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Expired') ?></th>
            <td><?= h($user->expired) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Resettime') ?></th>
            <td><?= h($user->resettime) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Subjects') ?></h4>
        <?= $this->Text->autoParagraph(h($user->subjects)); ?>
    </div>
    <div class="row">
        <h4><?= __('Imported Ids') ?></h4>
        <?= $this->Text->autoParagraph(h($user->imported_ids)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Helps') ?></h4>
        <?php if (!empty($user->helps)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Parent Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Slug') ?></th>
                <th scope="col"><?= __('Sub Title') ?></th>
                <th scope="col"><?= __('Body') ?></th>
                <th scope="col"><?= __('Url') ?></th>
                <th scope="col"><?= __('Url Src') ?></th>
                <th scope="col"><?= __('Photo') ?></th>
                <th scope="col"><?= __('Status') ?></th>
                <th scope="col"><?= __('Type') ?></th>
                <th scope="col"><?= __('Lft') ?></th>
                <th scope="col"><?= __('Rght') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->helps as $helps): ?>
            <tr>
                <td><?= h($helps->id) ?></td>
                <td><?= h($helps->user_id) ?></td>
                <td><?= h($helps->parent_id) ?></td>
                <td><?= h($helps->title) ?></td>
                <td><?= h($helps->slug) ?></td>
                <td><?= h($helps->sub_title) ?></td>
                <td><?= h($helps->body) ?></td>
                <td><?= h($helps->url) ?></td>
                <td><?= h($helps->url_src) ?></td>
                <td><?= h($helps->photo) ?></td>
                <td><?= h($helps->status) ?></td>
                <td><?= h($helps->type) ?></td>
                <td><?= h($helps->lft) ?></td>
                <td><?= h($helps->rght) ?></td>
                <td><?= h($helps->created) ?></td>
                <td><?= h($helps->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Helps', 'action' => 'view', $helps->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Helps', 'action' => 'edit', $helps->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Helps', 'action' => 'delete', $helps->id], ['confirm' => __('Are you sure you want to delete # {0}?', $helps->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Imported Quizzes') ?></h4>
        <?php if (!empty($user->imported_quizzes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Quiz Id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->imported_quizzes as $importedQuizzes): ?>
            <tr>
                <td><?= h($importedQuizzes->id) ?></td>
                <td><?= h($importedQuizzes->user_id) ?></td>
                <td><?= h($importedQuizzes->quiz_id) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Downloads', 'action' => 'view', $importedQuizzes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Downloads', 'action' => 'edit', $importedQuizzes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Downloads', 'action' => 'delete', $importedQuizzes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $importedQuizzes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Quizzes') ?></h4>
        <?php if (!empty($user->quizzes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Name') ?></th>
                <th scope="col"><?= __('Description') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col"><?= __('Modified') ?></th>
                <th scope="col"><?= __('Student Count') ?></th>
                <th scope="col"><?= __('Status') ?></th>
                <th scope="col"><?= __('Random Id') ?></th>
                <th scope="col"><?= __('Show Result') ?></th>
                <th scope="col"><?= __('Anonymous') ?></th>
                <th scope="col"><?= __('Subjects') ?></th>
                <th scope="col"><?= __('Classes') ?></th>
                <th scope="col"><?= __('Shared') ?></th>
                <th scope="col"><?= __('Is Approve') ?></th>
                <th scope="col"><?= __('Comment') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->quizzes as $quizzes): ?>
            <tr>
                <td><?= h($quizzes->id) ?></td>
                <td><?= h($quizzes->user_id) ?></td>
                <td><?= h($quizzes->name) ?></td>
                <td><?= h($quizzes->description) ?></td>
                <td><?= h($quizzes->created) ?></td>
                <td><?= h($quizzes->modified) ?></td>
                <td><?= h($quizzes->student_count) ?></td>
                <td><?= h($quizzes->status) ?></td>
                <td><?= h($quizzes->random_id) ?></td>
                <td><?= h($quizzes->show_result) ?></td>
                <td><?= h($quizzes->anonymous) ?></td>
                <td><?= h($quizzes->subjects) ?></td>
                <td><?= h($quizzes->classes) ?></td>
                <td><?= h($quizzes->shared) ?></td>
                <td><?= h($quizzes->is_approve) ?></td>
                <td><?= h($quizzes->comment) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Quizzes', 'action' => 'view', $quizzes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Quizzes', 'action' => 'edit', $quizzes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Quizzes', 'action' => 'delete', $quizzes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quizzes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Statistics') ?></h4>
        <?php if (!empty($user->statistics)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('User Id') ?></th>
                <th scope="col"><?= __('Type') ?></th>
                <th scope="col"><?= __('Created') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->statistics as $statistics): ?>
            <tr>
                <td><?= h($statistics->id) ?></td>
                <td><?= h($statistics->user_id) ?></td>
                <td><?= h($statistics->type) ?></td>
                <td><?= h($statistics->created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Statistics', 'action' => 'view', $statistics->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Statistics', 'action' => 'edit', $statistics->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Statistics', 'action' => 'delete', $statistics->id], ['confirm' => __('Are you sure you want to delete # {0}?', $statistics->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
