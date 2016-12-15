<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Help'), ['action' => 'edit', $help->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Help'), ['action' => 'delete', $help->id], ['confirm' => __('Are you sure you want to delete # {0}?', $help->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Helps'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Help'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Parent Helps'), ['controller' => 'Helps', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Parent Help'), ['controller' => 'Helps', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="helps view large-9 medium-8 columns content">
    <h3><?= h($help->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $help->has('user') ? $this->Html->link($help->user->name, ['controller' => 'Users', 'action' => 'view', $help->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Parent Help') ?></th>
            <td><?= $help->has('parent_help') ? $this->Html->link($help->parent_help->title, ['controller' => 'Helps', 'action' => 'view', $help->parent_help->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($help->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Slug') ?></th>
            <td><?= h($help->slug) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Sub Title') ?></th>
            <td><?= h($help->sub_title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Url') ?></th>
            <td><?= h($help->url) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Url Src') ?></th>
            <td><?= h($help->url_src) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Type') ?></th>
            <td><?= h($help->type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($help->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= $this->Number->format($help->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Lft') ?></th>
            <td><?= $this->Number->format($help->lft) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rght') ?></th>
            <td><?= $this->Number->format($help->rght) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($help->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($help->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Body') ?></h4>
        <?= $this->Text->autoParagraph(h($help->body)); ?>
    </div>
    <div class="row">
        <h4><?= __('Photo') ?></h4>
        <?= $this->Text->autoParagraph(h($help->photo)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Helps') ?></h4>
        <?php if (!empty($help->child_helps)): ?>
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
            <?php foreach ($help->child_helps as $childHelps): ?>
            <tr>
                <td><?= h($childHelps->id) ?></td>
                <td><?= h($childHelps->user_id) ?></td>
                <td><?= h($childHelps->parent_id) ?></td>
                <td><?= h($childHelps->title) ?></td>
                <td><?= h($childHelps->slug) ?></td>
                <td><?= h($childHelps->sub_title) ?></td>
                <td><?= h($childHelps->body) ?></td>
                <td><?= h($childHelps->url) ?></td>
                <td><?= h($childHelps->url_src) ?></td>
                <td><?= h($childHelps->photo) ?></td>
                <td><?= h($childHelps->status) ?></td>
                <td><?= h($childHelps->type) ?></td>
                <td><?= h($childHelps->lft) ?></td>
                <td><?= h($childHelps->rght) ?></td>
                <td><?= h($childHelps->created) ?></td>
                <td><?= h($childHelps->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Helps', 'action' => 'view', $childHelps->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'Helps', 'action' => 'edit', $childHelps->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Helps', 'action' => 'delete', $childHelps->id], ['confirm' => __('Are you sure you want to delete # {0}?', $childHelps->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
