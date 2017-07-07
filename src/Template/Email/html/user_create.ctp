<h1><?php echo __('New User'); ?></h1>
<p><?php echo __('ID') . ': ' . $data->id; ?></p>
<p><?php echo __('NAME') . ': ' . $data->name; ?></p>
<p><?php echo __('EMAIL') . ': ' . $data->email; ?></p>
<p><?php echo __('Registered: ') . date('d-m-Y', strtotime($data->created)); ?></p>
<p><?php echo $data->id; ?>;<?php echo $data->name; ?>;<?php echo $data->email; ?>;<?php echo date('Y-m-d', strtotime($data->created)); ?></p>