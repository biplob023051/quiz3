<div id="hero-unit">
    <div class="container">
        <h2 class="text-center"><?php echo __('WEB_TEST_ENABLES_TEACHER'); ?></h2>
        <h3 class="text-center"><?php echo __('GIVE_QUIZ_QUICKLY_EASILY'); ?><br /><?php echo __('WITH_THEIR'); ?><span class="text-black"><?php echo __('MOBILE_PHONES'); ?></span></h3>
        <hr class="invisible" />
        <p class="text-center"><a href="<?php echo $this->request->base; ?>/users/create" class="btn btn-success btn-lg"><?php echo __('CREATE_ACCOUNT'); ?></a></p>
    </div>
</div>

<!-- How it works tabs content -->
<div class="container" id="body-content">
    <h1 class="text-center"><?php echo __('THIS_IS_HOW_IT_WORKS'); ?></h1>
    <div class="tabpanel">
        <ul class="nav nav-tabs nav-justified">
            <li>
                <h4 class="text-center"><?php echo __('1_CREATE_TEST'); ?></h4>
                <p class="text-center"><?php echo __('CREATE_TEST_WITH_QUESTIONS'); ?></p>
            </li>
            <li>
                <h4 class="text-center"><?php echo __('2_GIVE_TEST'); ?></h4>
                <p class="text-center"><?php echo __('LET_STUDENT_ATTEND'); ?></p>
            </li>
            <li>
                <h4 class="text-center"><?php echo __('3_CHECK_RESULT'); ?></h4>
                <p class="text-center"><?php echo __('CHECK_RESULTS_SOON_ATTENDED'); ?></p>
            </li>
        </ul>


        <div class="tab-content">
            <!-- Create test tab information goes here -->
            <div class="tab-pane active" id="create-test"></div>
            <!-- Create test tab information goes here -->
            <div class="tab-pane" id="give-test"></div>
            <!-- Create test tab information goes here -->
            <div class="tab-pane" id="check-result"></div>
        </div>
    </div>
</div>

<!-- Video -->
<?php if (!empty($home_video)) : ?>
    <div id="home-video">
        <a href="javascript:void(0)" id="play_video"><img src="<?php echo $this->Quiz->getHelpPicture($home_video, 'videos'); ?>" class="img-responsive"></a>
    </div>
    <?php echo $this->element('Page/video_modal', array('home_video' => $home_video)); ?>
<?php else: ?>
    <div id="bg-video"></div>
<?php endif; ?>

<script type="text/javascript">
<?php if (!empty($home_video)) : ?>
    var url_src = <?php echo json_encode($home_video['url_src']) ?>;
<?php else: ?>
    var url_src = '';
<?php endif; ?>
<?php if (!empty($this->request->query['play']) && ($this->request->query['play'] == 'video')) : ?>
    var click_video = true;
<?php else: ?>
    var click_video = false;
<?php endif; ?>
</script>

<?= $this->Html->script(['video'.$minify, 'index'.$minify], ['inline' => true]); ?>