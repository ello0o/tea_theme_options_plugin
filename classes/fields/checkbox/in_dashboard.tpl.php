<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="checkbox" />
<h4><?php _e('Checkbox', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][description]" class="code"><?php echo $description ?></textarea>
</label>

<label class="label-edit-content">
    <span><?php _e('Default value', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][std]" value="<?php echo $std ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Options', TTO_I18N) ?></span>
</label>

<div class="label-edit-options" style="display:block;">
    <?php
        $optnum = 0;

        if (!empty($options)):
    ?>
        <?php
            foreach ($options as $k => $opts):
                if (empty($opts[0]))
                {
                    continue;
                }

                $vallabel = !empty($opts[1]) ? $opts[1] : $opts[0];
                $valvalue = $opts[0];
        ?>
            <label class="label-second">
                <input type="text" name="tea_add_contents[<?php echo $number ?>][options][<?php echo $k ?>][]" value="<?php echo $valvalue ?>" class="code" placeholder="<?php _e('Your value option', TTO_I18N) ?>" />
            </label>
            <label class="label-second">
                <input type="text" name="tea_add_contents[<?php echo $number ?>][options][<?php echo $k ?>][]" value="<?php echo $vallabel ?>" class="code" placeholder="<?php _e('Your label option', TTO_I18N) ?>" />
            </label>
            <div class="clearfix"></div>
            <?php $optnum++; ?>
        <?php endforeach ?>
    <?php endif ?>
    <label class="label-second">
        <input type="text" name="tea_add_contents[<?php echo $number ?>][options][<?php echo $optnum ?>][]" value="" class="code" placeholder="<?php _e('Your value option', TTO_I18N) ?>" />
    </label>
    <label class="label-second">
        <input type="text" name="tea_add_contents[<?php echo $number ?>][options][<?php echo $optnum ?>][]" value="" class="code" placeholder="<?php _e('Your label option', TTO_I18N) ?>" />
    </label>
    <div class="clearfix"></div>
</div>