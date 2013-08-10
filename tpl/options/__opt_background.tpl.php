<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="background" />
<h4><?php _e('Background', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][description]" class="code"><?php echo isset($val['description']) ? $val['description'] : '' ?></textarea>
</label>

<label class="label-edit-content">
    <input type="checkbox" name="tea_add_contents[<?php echo $num ?>][default]" value="1" <?php echo isset($val['default']) ? 'checked="checked"' : '' ?> />
    <?php _e('Display included Background images?', TTO_I18N) ?>
</label>

<label class="label-edit-content">
    <span><?php _e('Displayed image width', TTO_I18N) ?></span>
    <input type="number" name="tea_add_contents[<?php echo $num ?>][width]" value="<?php echo isset($val['width']) ? $val['width'] : '' ?>" class="code" min="0" max="300" step="1" />
</label>

<label class="label-edit-content">
    <span><?php _e('Displayed image height', TTO_I18N) ?></span>
    <input type="number" name="tea_add_contents[<?php echo $num ?>][height]" value="<?php echo isset($val['height']) ? $val['height'] : '' ?>" class="code" min="0" max="300" step="1" />
</label>

<div class="label-edit-options" style="display:block;">
    <label class="label-edit-content">
        <?php _e('Default values', TTO_I18N) ?>
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Custom image', TTO_I18N) ?></span>
        <input type="text" name="tea_add_contents[<?php echo $num ?>][std][image_custom]" value="<?php echo isset($val['std']['image_custom']) ? $val['std']['image_custom'] : '' ?>" class="code" />
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Color', TTO_I18N) ?></span>
        <input type="text" name="tea_add_contents[<?php echo $num ?>][std][color]" value="<?php echo isset($val['std']['color']) ? $val['std']['color'] : '' ?>" class="color-picker" maxlength="7" />
    </label>

    <div class="clearfix"></div>

    <label class="label-edit-content label-third">
        <span><?php _e('Background horizontal position', TTO_I18N) ?></span>
        <select name="tea_add_contents[<?php echo $num ?>][std][position][x]">
            <?php
                $pos_x = isset($val['std']['position']['x']) ? $val['std']['position']['x'] : '';
                foreach ($bgdetails['position']['x'] as $key => $posx):
                    $selected = $pos_x == $key ? 'selected="selected"' : '';
            ?>
                <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $posx ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Background vertical position', TTO_I18N) ?></span>
        <select name="tea_add_contents[<?php echo $num ?>][std][position][y]">
            <?php
                $pos_y = isset($val['std']['position']['y']) ? $val['std']['position']['y'] : '';
                foreach ($bgdetails['position']['y'] as $key => $posy):
                    $selected = $pos_y == $key ? 'selected="selected"' : '';
            ?>
                <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $posy ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Background repeat', TTO_I18N) ?></span>
        <select name="tea_add_contents[<?php echo $num ?>][std][repeat]">
            <?php
                $rep = isset($val['std']['repeat']) ? $val['std']['repeat'] : '';
                foreach ($bgdetails['repeat'] as $key => $repeat):
                    $selected = $rep == $key ? 'selected="selected"' : '';
            ?>
                <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $repeat ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <div class="clearfix"></div>
</div>