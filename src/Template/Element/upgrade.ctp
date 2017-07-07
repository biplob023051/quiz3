<?php 
    if (empty($userPermissions['upgraded'])) { 
        echo '<div class="row" id="notice-section">';
        echo '<div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">';
        echo '<div class="form-group">';
        if (empty($userPermissions['request_sent'])) {
            if (!empty($userPermissions['canCreateQuiz'])) {
                echo $this->element('Invoice/invoice_button', array('btn_text' => __('UPGRADE_ACCOUNT')));
            } else {
                echo $this->element('Invoice/invoice_button', array('btn_text' => __('UPGRADE_CREATE_QUIZZ')));
            }
        } else {
            echo '<button class="btn btn-primary btn-block" disabled="true"  id="upgrade_account"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>' . __('UPGRADE_PENDING') . '</button>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else {
        if (($userPermissions['days_left'] < '31') && ($authUser['account_level'] == 1)) { // if expire date soon for previous paid users
            echo '<div class="row">';
            echo '<div class="col-md-5 col-md-offset-3 col-xs-12 col-sm-12">';
            echo '<div class="form-group">';
            echo $this->element('Invoice/invoice_button', array('btn_text' => __('UPGRADE_ACCOUNT')));
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }
?>