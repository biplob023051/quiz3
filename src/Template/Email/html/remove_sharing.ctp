<h1><?php echo __('Sharing removed!'); ?></h1>
<h2><?php echo __('User Info:') ?></h2>
<p><?php echo __('ID') . ': ' . $data['User']['id']; ?></p>
<p><?php echo __('NAME') . ': ' . $data['User']['name']; ?></p>
<p><?php echo __('EMAIL') . ': ' . $data['User']['email']; ?></p>
<p><?php echo __('To view, please clik the quiz name or copy paste the bellow link'); ?></p>
<a href="<?php echo Router::url('/', true) . 'quizzes/view/' . $data['Quiz']['random_id']; ?>"><?php echo $data['Quiz']['name']; ?></a>

<?php echo Router::url('/', true) . 'quizzes/view/' . $data['Quiz']['random_id']; ?>