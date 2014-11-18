<?php
// source: /Applications/MAMP/htdocs/TIS/sandbox/app/templates/Homepage/default.latte

// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('4426133790', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb773ae44657_content')) { function _lb773ae44657_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;Nette\Bridges\FormsLatte\FormMacros::renderFormBegin($form = $_form = $_control["newLoginForm"], array()) ?>

    <div class="row">
        <div class="col-sm-6 col-md-4" style="margin: 0 auto;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Prihlásenie</h3>
                </div>
<?php $iterations = 0; foreach ($form->controls as $name => $field) { ?>                <div class="panel-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4 control-label"><?php echo Latte\Runtime\Filters::escapeHtml($field->getLabelPrototype()->class('col-sm-4 control-label'), ENT_NOQUOTES) ?></label>
                        <div class="col-sm-8">
                            <?php echo Latte\Runtime\Filters::escapeHtml($field->control, ENT_NOQUOTES) ?>

                            <!--<input type="email" class="form-control" id="inputEmail3" placeholder="Email">-->
                        </div>
                    </div>
                </div>
<?php $iterations++; } ?>
            </div>
        </div>
    </div>
<?php Nette\Bridges\FormsLatte\FormMacros::renderFormEnd($_form) ?>


<?php
}}

//
// block scripts
//
if (!function_exists($_b->blocks['scripts'][] = '_lbf305208dc2_scripts')) { function _lbf305208dc2_scripts($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;Latte\Macros\BlockMacros::callBlockParent($_b, 'scripts', get_defined_vars()) ?>

<?php
}}

//
// block head
//
if (!function_exists($_b->blocks['head'][] = '_lb87d5fb862b_head')) { function _lb87d5fb862b_head($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;
}}

//
// end of blocks
//

// template extending

$_l->extends = empty($_g->extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $_g->extended = TRUE;

if ($_l->extends) { ob_start();}

// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIMacros::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars())  ?>

<?php call_user_func(reset($_b->blocks['scripts']), $_b, get_defined_vars())  ?>



<?php call_user_func(reset($_b->blocks['head']), $_b, get_defined_vars()) ; 