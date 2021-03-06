<?php 
	if (!empty($student_result)){
		echo $this->Html->script(array('success'), array('inline' => false));
	}
	$otherQuestionType = array(6,7,8);
	$this->assign('title', __('SUCCESS')); 
?>
<div class="jumbotron">
  <h1><?= __('THANK_YOU'); ?></h1>
  <p><?= __('YOUR_ANSWERS_SUBMITTED'); ?></p>
</div>

<?php if (!empty($student_result)) : ?>
	<div class="row" id="result">
		<?php 
			$i = 1;
			$pending = 0;
			$result_html = '';
			foreach ($quiz->questions as $key => $question) { 
				$result_html = $result_html . '<div class="col-md-12">';
					if ($question->question_type_id == 6) { // for header type
						$result_html = $result_html . '<h3 class="header">' . $question->text . '</h3>';
					} elseif ($question->question_type_id == 7) { // for youtube type
						$result_html = $result_html . '<div class="row">
						    <div class="col-xs-12 col-md-6">
						        <iframe width="100%" height="315" src="' . $this->Quiz->getImageUtubeChoice($question->id) . '" frameborder="0" allowfullscreen></iframe>
						    </div>
						</div>';
					} elseif ($question->question_type_id == 8) { // for image type
						$result_html = $result_html . '<div class="row">
						    <div class="col-xs-12 col-md-6">
						        <img class="img-responsive" src="' . $this->Quiz->getImageUtubeChoice($question->id) . '" alt=""/>
						    </div>
						</div>';
					} elseif ($question->question_type_id == 9) { // for youtube type
						$result_html = $result_html . '<div class="row">
						    <div class="col-xs-12 col-md-6">
						        <p>'. $this->Quiz->getImageUtubeChoice($question->id) .'</p>
						    </div>
						</div>';
					} else { // for actual questions
						$result_html = $result_html . '<h3>' . $i . ') ' . $question->text . '</h3>';
					}
					foreach ($student_result->answers as $key => $answer) {
						if ($question->id == $answer->question_id) {
							if (empty($answer->text)) {
				                $result_html = $result_html . '<p class="text-danger">' . __('NOT_ANSWERED') .'</p>';
				            } else { 
				                if ($answer->score > 0) {
				                    $result_html = $result_html . '<p class="text-success">' . nl2br($answer->text) . ' <span class="score">' . ($answer->score+0) . '</span><br/>';
				                } elseif (is_null($answer->score)) {
				                	$pending++;
				                    $result_html = $result_html . '<p>' . nl2br($answer->text) . ' <span class="score">' . __('ON_HOLD') . '</span><br/>';
				                } elseif ($answer->score === 0) {
				                    $result_html = $result_html . '<p class="text-warning">' . nl2br($answer->text) . ' <span class="score">' . ($answer->score+0) . '</span><br/>';
				                } else {
				                    $result_html = $result_html . '<p class="text-danger">' . nl2br($answer->text) . ' <span class="score">' . ($answer->score+0) . '</span><br/>';
				                }    
				            } 
				        } 
				    }
				$result_html = $result_html . '</div>';
			 	if (!in_array($question->question_type_id, $otherQuestionType)) {
					$i++; // increment only for actual question type questions		
				} 
			} 
		?>
		<div class="col-md-12">
			<h3><?php echo __('YOUR_RESULTS'); ?></h3>
			<h2><?php echo __('TOTAL') . ': ' . ($student_result->ranking->score+0) . '/' . ($student_result->ranking->total+0); ?><?php echo !empty($pending) ? ' (' . $pending . ' ' . __('YOUR_ANSWER_WAITING_RATING') . ')' : ''; ?></h2>
		</div>
		<?php echo $result_html; ?>
	</div>
<?php endif; ?>

<style type="text/css">
span.score {
    border-radius: 50%;
    behavior: url(PIE.htc);
    width: 16px;
    height: 16px;
    padding: 1px 4px;
    background: #fff;
    border: 2px solid #666;
    color: #666;
    text-align: center;
    font: 14px Arial, sans-serif;
    font-weight: bold;
}
</style>
