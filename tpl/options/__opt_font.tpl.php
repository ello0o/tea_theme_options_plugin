<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="font" />
<h4><?php _e('Google Fonts', TTO_I18N) ?></h4>

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
    <?php _e('Display included Google Fonts?', TTO_I18N) ?>
</label>

<label class="label-edit-content">
    <?php _e('Select the Google Font you want to select by default', TTO_I18N) ?>
</label>

<div class="label-edit-image">
    <?php foreach ($fonts as $ft): ?>
        <?php
            $selected = isset($val['std']) && $ft[0] == $val['std'] ? true : false;
        ?>
        <label for="tea_add_contents_<?php echo $num ?>_font_<?php echo $ft[0] ?>" class="gfont_<?php echo str_replace(' ', '_', $ft[1]) ?> <?php echo $selected ? 'selected' : '' ?>">
            <span>
                <input type="radio" name="tea_add_contents[<?php echo $num ?>][std]" id="tea_add_contents_<?php echo $num ?>_font_<?php echo $ft[0] ?>" value="<?php echo $ft[0] ?>" <?php echo $selected ? 'checked="checked" ' : '' ?> />
                <b><?php echo $ft[0] ?></b>
            </span>
        </label>
    <?php endforeach ?>
    <div class="clearfix"></div>
</div>

<label class="label-edit-content">
    <span><?php _e('Custom Google Font', TTO_I18N) ?></span>
</label>

<div class="label-edit-options" style="display:block;">
    <?php
        $optnum = 0;

        if (isset($val['options']) && !empty($val['options'])):
    ?>
        <?php
            foreach ($val['options'] as $k => $opts):
                if (empty($opts[0]))
                {
                    continue;
                }
        ?>
            <label class="label-third">
                <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $k ?>][]" value="<?php echo isset($opts[0]) ? $opts[0] : '' ?>" class="code" placeholder="<?php _e('Google name, ex: &quot;PT+Sans&quot;', TTO_I18N) ?>" />
            </label>
            <label class="label-third">
                <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $k ?>][]" value="<?php echo isset($opts[1]) ? $opts[1] : '' ?>" class="code" placeholder="<?php _e('CSS Name, ex: &quot;PT Sans&quot;', TTO_I18N) ?>" />
            </label>
            <label class="label-third">
                <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $k ?>][]" value="<?php echo isset($opts[2]) ? $opts[2] : '' ?>" class="code" placeholder="<?php _e('Size(s), ex: &quot;400,700&quot;', TTO_I18N) ?>" />
            </label>
            <div class="clearfix"></div>
            <?php $optnum++; ?>
        <?php endforeach ?>
    <?php endif ?>
    <label class="label-third">
        <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $optnum ?>][]" value="" class="code" placeholder="<?php _e('Google name, ex: &quot;PT+Sans&quot;', TTO_I18N) ?>" />
    </label>
    <label class="label-third">
        <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $optnum ?>][]" value="" class="code" placeholder="<?php _e('CSS Name, ex: &quot;PT Sans&quot;', TTO_I18N) ?>" />
    </label>
    <label class="label-third">
        <input type="text" name="tea_add_contents[<?php echo $num ?>][options][<?php echo $optnum ?>][]" value="" class="code" placeholder="<?php _e('Size(s), ex: &quot;400,700&quot;', TTO_I18N) ?>" />
    </label>
    <div class="clearfix"></div>
</div>