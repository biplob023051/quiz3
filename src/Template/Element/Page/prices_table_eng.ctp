<table class="table table-striped">
    <thead>
        <tr>
            <th>&nbsp;</th>
            <th><?= __('FREE'); ?></th>
            <th><?= __('49_EUR'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= __('USERS'); ?></td>
            <td><?= __('1'); ?></td>
            <td><?= __('1'); ?></td>
        </tr>
        <tr>
            <td><?= __('TESTS'); ?></td>
            <td><?= __('1'); ?></td>
            <td><?= __('UNLIMITED'); ?></td>
        </tr>
        <tr>
            <td><?= __('DAYS_TO_USE'); ?></td>
            <td><?= '30 ' . __('DAYS'); ?></td>
            <td><?= '365 ' . __('DAYS'); ?></td>
        </tr>
        <tr>
            <td><?= __('QUIZ_BANK'); ?></td>
            <td><?= __('LIMITED_ACCESS'); ?></td>
            <td><?= __('UNLIMITED'); ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><?= $this->Html->link(__('REGISTER_NOW'), '/users/create', array('class' => 'btn btn-success')); ?></td>
            <td><button type="button" id="buy-button" class="btn btn-success"><?= __('BUY'); ?></button></td>
        </tr>
    </tbody>
</table>