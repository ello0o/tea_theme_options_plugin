<!-- Content multiselect <?php echo $id ?> -->
<div id="<?php echo $id ?>_content" class="stuffbox">
    <h3>
        <label for="<?php echo $id ?>"><?php echo $title ?></label>
    </h3>

    <div class="inside multiselect">
        <select name="<?php echo $id ?>[]" id="<?php echo $id ?>" multiple="true" size="5">
            <?php
                foreach ($options as $option):
                    if (empty($option[0]))
                    {
                        continue;
                    }

                    $selected = is_array($vals) && in_array($option[0], $vals) ? true : false;
                ?>
                <option value="<?php echo $option[0] ?>" <?php echo $selected ? 'selected="selected" ' : '' ?>><?php echo $option[1] ?></option>
            <?php endforeach ?>
        </select>

        <p>
            <?php echo __('Press the <code>CTRL</code> or <code>CMD</code> button to select more than one option.', TTO_I18N) . '<br/>' ?>
            <?php echo $description ?>
        </p>
    </div>
</div>
<!-- /Content multiselect <?php echo $id ?> -->