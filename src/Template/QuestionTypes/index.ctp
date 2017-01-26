<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Question Type'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Questions'), ['controller' => 'Questions', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Question'), ['controller' => 'Questions', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="questionTypes index large-9 medium-8 columns content">
    <h3><?= __('Question Types') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('answer_field') ?></th>
                <th scope="col"><?= $this->Paginator->sort('multiple_choices') ?></th>
                <th scope="col"><?= $this->Paginator->sort('template_name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('manual_scoring') ?></th>
                <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questionTypes as $questionType): ?>
            <tr>
                <td><?= $this->Number->format($questionType->id) ?></td>
                <td><?= h($questionType->name) ?></td>
                <td><?= h($questionType->answer_field) ?></td>
                <td><?= h($questionType->multiple_choices) ?></td>
                <td><?= h($questionType->template_name) ?></td>
                <td><?= $this->Number->format($questionType->manual_scoring) ?></td>
                <td><?= h($questionType->type) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $questionType->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $questionType->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $questionType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $questionType->id)]) ?>
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
