{{#if relatedClass}}
    {{#if showQuestionText}}
        <tr id="q{{id}}" class="others_type header_type">
    {{else}}
        <tr id="q{{id}}" class="others_type">
    {{/if}}
{{else}}
    <tr id="q{{id}}">
{{/if}}
    <td>
        <div class="pull-right shorter-arrow">
            <i class="glyphicon glyphicon-resize-vertical"></i>
        </div>   
        <div class="row">
            <div class="col-xs-12 col-md-6">         
                <p>
                    {{#if relatedClass}}
                        {{#if showQuestionText}}
                            <span class="h4 {{relatedClass}}">{{text}}</span>
                            <br />
                        {{/if}}
                    {{else}}
                        <span class="h4"><span class="question_number">{{question_number}}</span>. {{text}}</span>
                        <br />
                    {{/if}}
                    <span class="text-muted">{{explanation}}</span>
                    {{#if max_allowed}}
                        <p>
                            <span class="text-muted">
                                <strong>
                                    <?php echo __('CHOOSE_MOST'); ?>
                                </strong>
                                {{max_allowed}}
                            </span>
                        </p>
                    {{/if}}
                    {{#if case_sensitive}}
                        <p>
                            <span class="text-muted">
                                <strong>
                                    <?php echo __('DEMAND_UPPER_LOWERCASE'); ?>
                                </strong>
                            </span>
                        </p>
                    {{/if}}
                </p>
                {{#if warn_message}}
                    <p class="alert alert-warning" style="margin-bottom: 0px;">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <strong><?php echo __('NOTICE'); ?></strong> <?php echo __("YOU_INSERTED_ZERO"); ?>
                    </p>
                {{/if}}
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="btn-group preview-btn">
                    <button type="button" class="btn btn-default btn-sm edit-question" id="edit-q{{id}}" title="<?php echo __('EDIT_QUESTION'); ?>">
                        <i class="glyphicon pencil"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm delete-question" id="delete-q{{id}}" title="<?php echo __('REMOVE_QUESTION'); ?>">
                        <i class="glyphicon trash"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm duplicate-question" id="duplicate-q{{id}}" title="<?php echo __('DUPLICATE_QUESTION'); ?>"><i class="glyphicon duplicate"></i></button>
                </div>
            </div>
        </div>
        <div class="choices">
            {{#choice Choice}}
            {{choice_tpl}}
            {{/choice}}
        </div>
    </td>
</tr>