<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="background" />
<h4><?php _e('Background', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][description]" class="code"><?php echo $description ?></textarea>
</label>

<label class="label-edit-content">
    <input type="checkbox" name="tea_add_contents[<?php echo $number ?>][default]" value="1" <?php echo $default ? 'checked="checked"' : '' ?> />
    <?php _e('Display included Background images?', TTO_I18N) ?>
</label>

<label class="label-edit-content">
    <span><?php _e('Displayed image width', TTO_I18N) ?></span>
    <input type="number" name="tea_add_contents[<?php echo $number ?>][width]" value="<?php echo $width ?>" class="code" min="0" max="300" step="1" />
</label>

<label class="label-edit-content">
    <span><?php _e('Displayed image height', TTO_I18N) ?></span>
    <input type="number" name="tea_add_contents[<?php echo $number ?>][height]" value="<?php echo $height ?>" class="code" min="0" max="300" step="1" />
</label>

<div class="label-edit-options" style="display:block;">
    <label class="label-edit-content">
        <b><?php _e('Default values', TTO_I18N) ?></b>
    </label>

    <label class="label-edit-content label-first">
        <span><?php _e('Custom image', TTO_I18N) ?></span>
        <input type="text" name="tea_add_contents[<?php echo $number ?>][std][image_custom]" value="<?php echo $std['image_custom'] ?>" class="code" />
    </label>

    <label class="label-edit-content label-first">
        <span><?php _e('Color', TTO_I18N) ?></span>
        <input type="text" name="tea_add_contents[<?php echo $number ?>][std][color]" value="<?php echo $std['color'] ?>" class="color-picker" maxlength="7" />
    </label>

    <div class="clearfix"></div>

    <label class="label-edit-content label-third">
        <span><?php _e('Background horizontal position', TTO_I18N) ?></span>
        <select name="tea_add_contents[<?php echo $number ?>][std][position][x]">
            <?php
                $pos_x = $std['position']['x'];
                foreach ($bgdetails['position']['x'] as $key => $posx):
                    $selected = $pos_x == $key ? 'selected="selected"' : '';
            ?>
                <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $posx ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Background vertical position', TTO_I18N) ?></span>
        <select name="tea_add_contents[<?php echo $number ?>][std][position][y]">
            <?php
                $pos_y = $std['position']['y'];
                foreach ($bgdetails['position']['y'] as $key => $posy):
                    $selected = $pos_y == $key ? 'selected="selected"' : '';
            ?>
                <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $posy ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <label class="label-edit-content label-third">
        <span><?php _e('Background repeat', TTO_I18N) ?></span>
        <select name="tea_add_contents[<?php echo $number ?>][std][repeat]">
            <?php
                $rep = $std['repeat'];
                foreach ($bgdetails['repeat'] as $key => $repeat):
                    $selected = $rep == $key ? 'selected="selected"' : '';
            ?>
                <option value="<?php echo $key ?>" <?php echo $selected ?>><?php echo $repeat ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <div class="clearfix"></div>
</div>