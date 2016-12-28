<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Quiz'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Imported Quizzes'), ['controller' => 'ImportedQuizzes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Imported Quiz'), ['controller' => 'ImportedQuizzes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Rankings'), ['controller' => 'Rankings', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Ranking'), ['controller' => 'Rankings', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="quizzes index large-9 medium-8 columns content">
    <h3><?= __('Quizzes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('description') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('student_count') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('random_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('show_result') ?></th>
                <th scope="col"><?= $this->Paginator->sort('anonymous') ?></th>
                <th scope="col"><?= $this->Paginator->sort('shared') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_approve') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quizzes as $quiz): ?>
            <tr>
                <td><?= $this->Number->format($quiz->id) ?></td>
                <td><?= $quiz->has('user') ? $this->Html->link($quiz->user->name, ['controller' => 'Users', 'action' => 'view', $quiz->user->id]) : '' ?></td>
                <td><?= h($quiz->name) ?></td>
                <td><?= h($quiz->description) ?></td>
                <td><?= h($quiz->created) ?></td>
                <td><?= h($quiz->modified) ?></td>
                <td><?= $this->Number->format($quiz->student_count) ?></td>
                <td><?= $this->Number->format($quiz->status) ?></td>
                <td><?= $this->Number->format($quiz->random_id) ?></td>
                <td><?= h($quiz->show_result) ?></td>
                <td><?= h($quiz->anonymous) ?></td>
                <td><?= h($quiz->shared) ?></td>
                <td><?= $this->Number->format($quiz->is_approve) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $quiz->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $quiz->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $quiz->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quiz->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
