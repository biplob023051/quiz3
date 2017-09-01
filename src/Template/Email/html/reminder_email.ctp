<h2><?= __('USERS_NOT_CONFIRMED') ?></h2>
<h1><?php echo __('NEW_USER'); ?></h1>
<p><?php echo __('ID') . ': ' . $data->id; ?></p>
<p><?php echo __('NAME') . ': ' . $data->name; ?></p>
<p><?php echo __('EMAIL') . ': ' . $data->email; ?></p>
<p><?php echo __('REGISTERED') . ': ' . $data->created; ?></p>
<p><?php echo $data->id; ?>;<?php echo $data->name; ?>;<?php echo $data->email; ?>;<?php echo date('Y-m-d', strtotime($data->created)); ?></p>