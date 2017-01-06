<?php 
	use Cake\Routing\Router;
?>
<h1><?php echo __('Quiz Shared'); ?></h1>
<h2><?php echo __('User Info : ') ?></h2>
<p><?php echo __('Id: ') . $data->user->id; ?></p>
<p><?php echo __('Name: ') . $data->user->name; ?></p>
<p><?php echo __('Email: ') . $data->user->email; ?></p>
<p><?php echo __('To view, approve or decline sharing, please clik the quiz name or copy paste the bellow link'); ?></p>
<a href="<?php echo Router::url('/', true) . 'admin/quizzes/preview/' . $data['Quiz']['id']; ?>"><?php echo $data->name; ?></a>
<br/>

<?php echo Router::url('/', true) . 'admin/quizzes/preview/' . $data->id; ?>