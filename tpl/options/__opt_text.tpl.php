<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="text" />
<h4><?php _e('Text', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][description]" class="code"><?php echo isset($val['description']) ? $val['description'] : '' ?></textarea>
</label>

<label class="label-edit-content">
    <span><?php _e('Placeholder', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][placeholder]" value="<?php echo isset($val['placeholder']) ? $val['placeholder'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Default value', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][std]" value="<?php echo isset($val['std']) ? $val['std'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Max field length', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][maxlength]" value="<?php echo isset($val['maxlength']) ? $val['maxlength'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Content', TTO_I18N) ?></span>
    <select name="tea_add_contents[<?php echo $num ?>][options][type]" class="code select-options">
        <?php
            $txt = isset($val['options']['type']) ? $val['options']['type'] : '';
            foreach ($texts as $key => $itm):
                $selected = $key == $txt ? 'selected="selected"' : '';
                $class = 'number' == $key || 'range' == $key ? 'class="display-options"' : '';
        ?>
            <option value="<?php echo $key ?>" <?php echo $class ?> <?php echo $selected ?>><?php echo $itm ?></option>
        <?php endforeach ?>
    </select>
</label>

<div class="label-edit-options">
    <label class="label-edit-content">
        <?php _e('Options', TTO_I18N) ?>
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Min value', TTO_I18N) ?></span>
        <input type="number" name="tea_add_contents[<?php echo $num ?>][options][min]" value="<?php echo isset($val['options']['min']) ? $val['options']['min'] : '' ?>" class="code" />
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Max value', TTO_I18N) ?></span>
        <input type="number" name="tea_add_contents[<?php echo $num ?>][options][max]" value="<?php echo isset($val['options']['max']) ? $val['options']['max'] : '' ?>" class="code" />
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Step', TTO_I18N) ?></span>
        <input type="number" name="tea_add_contents[<?php echo $num ?>][options][step]" value="<?php echo isset($val['options']['step']) ? $val['options']['step'] : '' ?>" class="code" />
    </label>
    <div class="clearfix"></div>
</div>