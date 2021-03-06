<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Help'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="helps index large-9 medium-8 columns content">
    <h3><?= __('Helps') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('parent_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('slug') ?></th>
                <th scope="col"><?= $this->Paginator->sort('sub_title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('url') ?></th>
                <th scope="col"><?= $this->Paginator->sort('url_src') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('type') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($helps as $help): ?>
            <tr>
                <td><?= $this->Number->format($help->id) ?></td>
                <td><?= $help->has('user') ? $this->Html->link($help->user->name, ['controller' => 'Users', 'action' => 'view', $help->user->id]) : '' ?></td>
                <td><?= $help->has('parent_help') ? $this->Html->link($help->parent_help->title, ['controller' => 'Helps', 'action' => 'view', $help->parent_help->id]) : '' ?></td>
                <td><?= h($help->title) ?></td>
                <td><?= h($help->slug) ?></td>
                <td><?= h($help->sub_title) ?></td>
                <td><?= h($help->url) ?></td>
                <td><?= h($help->url_src) ?></td>
                <td><?= $this->Number->format($help->status) ?></td>
                <td><?= h($help->type) ?></td>
                <td><?= h($help->created) ?></td>
                <td><?= h($help->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $help->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $help->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $help->id], ['confirm' => __('Are you sure you want to delete # {0}?', $help->id)]) ?>
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
