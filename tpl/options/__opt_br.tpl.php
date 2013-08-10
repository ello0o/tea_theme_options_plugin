<?php
//Get vars
$val = isset($cont) ? $cont : array();
$num = isset($num) ? $num : '__NUM__';
?>
<input type="hidden" name="tea_add_contents[<?php echo $num ?>][type]" value="br" />
<h4><?php _e('Breakline', TTO_I18N) ?></h4>
<p><?php _e('This field has no options', TTO_I18N) ?></p>