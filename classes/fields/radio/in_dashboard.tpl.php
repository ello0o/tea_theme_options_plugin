<?php if (isset($id)): ?><span class="content-id"><?php echo $id ?></span><?php endif ?>
<input type="hidden" name="tea_add_contents[<?php echo $number ?>][type]" value="radio" />
<h4><?php _e('Radio', TTO_I18N) ?></h4>

<label class="label-edit-content">
    <span><?php _e('Title', TTO_I18N) ?></span>
    <input type="text" name="tea_add_contents[<?php echo $number ?>][title]" value="<?php echo $title ?>" class="code" />
</label>

<label class="label-edit-content">
    <span><?php _e('Description', TTO_I18N) ?></span>
    <textarea name="tea_add_contents[<?php echo $number ?>][description]" class="code"><?php echo $description ?></textarea>
</label>

<label class="label-edit-content">
    <b><?php _e('Options', TTO_I18N) ?></b>
</label>

<div class="label-edit-options model-multiple" style="display:block;">
    <label class="label-option">
        <?php _e('Default', TTO_I18N) ?>
    </label>
    <label class="label-first">
        <?php _e('Label', TTO_I18N) ?>
    </label>
    <div class="clearfix"></div>

    <div class="label-model">
        <label class="label-option">
            <input type="radio" name="tea_add_contents[<?php echo $number ?>][default]" value="__OPTNUM__" />
        </label>
        <label class="label-first">
            <input type="hidden" name="tea_add_contents[<?php echo $number ?>][options][__OPTNUM__][]" value="" />
            <input type="text" name="tea_add_contents[<?php echo $number ?>][options][__OPTNUM__][]" value="" class="code" placeholder="<?php _e('Your label option', TTO_I18N) ?>" />
        </label>
        <div class="clearfix"></div>
    </div>

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
            <label class="label-option">
                <input type="radio" name="tea_add_contents[<?php echo $number ?>][default]" value="<?php echo $optnum+1 ?>" <?php echo $std == $valvalue ? 'checked="checked"' : '' ?> />
            </label>
            <label class="label-first">
                <input type="hidden" name="tea_add_contents[<?php echo $number ?>][options][<?php echo $k ?>][]" value="<?php echo $valvalue ?>" />
                <input type="text" name="tea_add_contents[<?php echo $number ?>][options][<?php echo $k ?>][]" value="<?php echo $vallabel ?>" class="code" placeholder="<?php _e('Your label option', TTO_I18N) ?>" />
            </label>
            <div class="clearfix"></div>
            <?php $optnum++; ?>
        <?php endforeach ?>
    <?php endif ?>

    <input type="submit" name="add_option" class="button-secondary label-button" value="<?php _e('Add a new option', TTO_I18N) ?>" />
</div>