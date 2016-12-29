<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Ranking'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Quizzes'), ['controller' => 'Quizzes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Quiz'), ['controller' => 'Quizzes', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Students'), ['controller' => 'Students', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Student'), ['controller' => 'Students', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="rankings index large-9 medium-8 columns content">
    <h3><?= __('Rankings') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('quiz_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('student_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('score') ?></th>
                <th scope="col"><?= $this->Paginator->sort('total') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rankings as $ranking): ?>
            <tr>
                <td><?= $this->Number->format($ranking->id) ?></td>
                <td><?= $ranking->has('quiz') ? $this->Html->link($ranking->quiz->name, ['controller' => 'Quizzes', 'action' => 'view', $ranking->quiz->id]) : '' ?></td>
                <td><?= $ranking->has('student') ? $this->Html->link($ranking->student->id, ['controller' => 'Students', 'action' => 'view', $ranking->student->id]) : '' ?></td>
                <td><?= $this->Number->format($ranking->score) ?></td>
                <td><?= $this->Number->format($ranking->total) ?></td>
                <td><?= h($ranking->created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $ranking->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $ranking->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $ranking->id], ['confirm' => __('Are you sure you want to delete # {0}?', $ranking->id)]) ?>
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
