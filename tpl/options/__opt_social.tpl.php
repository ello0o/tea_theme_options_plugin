<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="social" />
<h4><?php _e('Social networks', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $num ?>][title]" value="<?php echo isset($val['title']) ? $val['title'] : '' ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $num ?>][description]" class="code"><?php echo isset($val['description']) ? $val['description'] : '' ?></textarea>
</label>

<label class="label-edit-content">
    <?php _e('Select all social networks you want to include', TTO_I18N) ?>
</label>

<div class="label-edit-image">
    <?php foreach ($socials as $key => $sc): ?>
        <label>
            <img src="<?php echo $urlsocial . $key ?>.png" alt="" />
            <span>
                <input type="checkbox" name="tea_add_contents[<?php echo $num ?>][default][<?php echo $key ?>]" id="tea_add_contents_<?php echo $num ?>_social_<?php echo $key ?>" value="<?php echo $key ?>" <?php echo isset($val['default'][$key]) ? 'checked="checked"' : '' ?> />
                <b><?php echo ucfirst($key) ?></b>
            </span>
        </label>
    <?php endforeach ?>
    <div class="clearfix"></div>
</div>