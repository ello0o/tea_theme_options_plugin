<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="social" />
<h4><?php _e('Social networks', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][description]" class="code"><?php echo $description ?></textarea>
</label>

<label class="label-edit-content">
    <b><?php _e('Select all social networks you want to include', TTO_I18N) ?></b>
</label>

<div class="label-edit-image">
    <?php foreach ($socials as $key => $sc): ?>
        <label>
            <img src="<?php echo $url . $key ?>.png" alt="" />
            <span>
                <input type="checkbox" name="tea_add_contents[<?php echo $number ?>][default][<?php echo $key ?>]" id="tea_add_contents_<?php echo $number ?>_social_<?php echo $key ?>" value="<?php echo $key ?>" <?php echo isset($default[$key]) && $key == $default[$key] ? 'checked="checked"' : '' ?> />
                <?php echo ucfirst($key) ?>
            </span>
        </label>
    <?php endforeach ?>
    <div class="clearfix"></div>
</div>