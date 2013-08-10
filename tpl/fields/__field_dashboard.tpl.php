<!-- Content font style -->
<?php
    $linkstylesheet = '';
    $gfontstyle = '';

    foreach ($fonts as $ft):
        if (empty($ft[0]) || 'sansserif' == $ft[0])
        {
            continue;
        }
        $linkstylesheet .= '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' . $ft[0] . ':' . $ft[2] . '" />' . "\n";
        $gfontstyle .= '.gfont_' . str_replace(' ', '_', $ft[1]) . ' {font-family:\'' . $ft[1] . '\',sans-serif;}' . "\n";
    endforeach;
?>
<?php echo $linkstylesheet ?>
<style>
    <?php echo $gfontstyle ?>
</style>
<!-- /Content font style -->

<h2 class="tea-clear"><?php echo $title ?></h2>

<div class="inside-dashboard tea-nav-menus-frame nav-menus-php">

    <!-- Menu panel -->
    <div class="tea-menu-settings-column metabox-holder">
        <form action="admin.php?page=<?php echo $page ?>&updated=true" method="post" class="nav-menu-meta">
            <input type="hidden" name="tea_to_dashboard" id="tea_to_dashboard" value="1" />
            <input type="hidden" name="tea_add_page" id="tea_add_page" value="1" />

            <div class="accordion-container">
                <ul class="outer-border">

                    <!-- Page listing section -->
                    <li class="control-section accordion-section<?php if ($count): ?> open<?php endif ?>">
                        <h3 class="accordion-section-title hndle" tabindex="0"><?php _e('Custom pages', TTO_I18N) ?></h3>
                        <div class="accordion-section-content">
                            <div class="inside">

                                <ul class="dashboard-page-listing">
                                    <?php if ($count): ?>
                                        <?php $num = 0 ?>
                                        <?php
                                            foreach ($pages as $item):
                                                $class = 0 == $num ? 'active' : '';
                                        ?>
                                            <li class="<?php echo $class ?>">
                                                <a href="#<?php echo $item['slug'] ?>"><?php echo $item['title'] ?></a>
                                            </li>
                                            <?php $num++ ?>
                                        <?php endforeach ?>
                                    <?php else: ?>
                                        <li><?php _e('No page found.', TTO_I18N) ?></li>
                                    <?php endif ?>
                                </ul>

                            </div>
                        </div>
                    </li>
                    <!-- /Page listing section -->

                    <!-- Add page section -->
                    <li class="control-section accordion-section<?php if (!$count): ?> open<?php endif ?>">
                        <h3 class="accordion-section-title hndle" tabindex="0"><?php _e('Add custom page', TTO_I18N) ?></h3>
                        <div class="accordion-section-content">
                            <div class="inside">

                                <label for="tea_add_page_title" class="label-add-page">
                                    <span><?php _e('Page title', TTO_I18N) ?></span>
                                    <input type="text" name="tea_add_page_title" id="tea_add_page_title" value="" class="code menu-item-textbox" />
                                </label>

                                <label for="tea_add_page_description" class="label-add-page">
                                    <span><?php _e('Page description', TTO_I18N) ?></span>
                                    <textarea name="tea_add_page_description" id="tea_add_page_description" class="code menu-item-textbox"></textarea>
                                </label>

                                <label for="tea_add_page_submit" class="label-add-page">
                                    <span><?php _e('Display submit button?', TTO_I18N) ?></span>
                                    <select name="tea_add_page_submit" id="tea_add_page_submit" class="code menu-item-textbox">
                                        <option value="1"><?php _e('Yes', TTO_I18N) ?></option>
                                        <option value="0"><?php _e('No', TTO_I18N) ?></option>
                                    </select>
                                </label>

                                <input type="submit" name="add_page" id="add_page" class="button button-primary" value="<?php _e('Create page', TTO_I18N) ?>" />
                            </div>
                        </div>
                    </li>
                    <!-- /Add page section -->

                </ul>
            </div>

        </form>
    </div>
    <!-- /Menu panel -->

    <!-- Content panel -->
    <div class="tea-menu-management-liquid nav-menu-meta">
        <?php if ($count): ?>
            <?php $num = 0 ?>
            <?php
                foreach ($pages as $key => $item):
                    $class = 0 == $num ? 'open' : '';
            ?>
                <form action="admin.php?page=<?php echo $page ?>&updated=true" method="post" class="tea-dashoard-content <?php echo $item['slug'] . ' ' . $class ?>">
                    <input type="hidden" name="tea_to_dashboard" id="tea_to_dashboard" value="1" />
                    <input type="hidden" name="tea_add_content" id="tea_add_content" value="1" />
                    <input type="hidden" name="tea_page" id="tea_page" value="<?php echo $item['slug'] ?>" />

                    <div class="menu-edit tea-menu-edit">
                        <!-- Header -->
                        <div class="tea-nav-menu-header">
                            <div class="major-publishing-actions howto">
                                <span><?php echo $item['title'] ?></span>
                            </div>
                        </div>
                        <!-- /Header -->

                        <!-- Aside -->
                        <div class="tea-nav-aside">
                            <div class="tea-edit-screen">
                                <label class="label-add-content">
                                    <span><?php _e('Page title', TTO_I18N) ?></span>
                                    <input type="text" name="tea_edit_page_title" value="<?php echo $item['title'] ?>" class="code menu-item-textbox" />
                                </label>
                                <label class="label-add-content">
                                    <span><?php _e('Page description', TTO_I18N) ?></span>
                                    <textarea name="tea_edit_page_description" class="code menu-item-textbox"><?php echo $item['description'] ?></textarea>
                                </label>
                                <label class="label-add-content">
                                    <span><?php _e('Display submit button?', TTO_I18N) ?></span>
                                    <select name="tea_edit_page_submit" class="code menu-item-textbox">
                                        <option value="1" <?php if ($item['submit']): ?>selected="selected"<?php endif ?>><?php _e('Yes', TTO_I18N) ?></option>
                                        <option value="0" <?php if (!$item['submit']): ?>selected="selected"<?php endif ?>><?php _e('No', TTO_I18N) ?></option>
                                    </select>
                                </label>
                                <input type="submit" name="edit_page" class="button button-primary" value="<?php _e('Edit page', TTO_I18N) ?>" />
                            </div>
                            <div class="tea-edit-screen-link">
                                <div class="tea-contextual-help-link-wrap hide-if-no-js screen-meta-toggle">
                                    <a href="#" class="show-settings" data-target=".tea-edit-screen"><?php _e('Edit page', TTO_I18N) ?></a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <!-- /Aside -->

                        <!-- Body -->
                        <div class="tea-post-body">
                            <div class="tea-post-body-content">

                                <!-- Content list -->
                                <div class="dashboard-contents-all">
                                    <?php if (isset($contents[$key]) && !empty($contents[$key])): ?>
                                        <?php
                                            $num = 0;
                                            foreach ($contents[$key] as $cont):
                                        ?>
                                            <div class="dashboard-content">
                                                <a href="#" class="delete"><?php _e('Delete', TTO_I18N) ?></a>
                                                <?php if (isset($cont['id'])): ?><span class="content-id"><?php echo $cont['id'] ?></span><?php endif ?>
                                                <?php
                                                    //Register content file in variable
                                                    include(TTO_PATH . 'tpl/options/__opt_' . $cont['type'] . '.tpl.php');
                                                ?>
                                            </div>
                                            <?php $num++ ?>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                </div>
                                <!-- /Content list -->

                                <!-- Add content form -->
                                <div class="dashboard-add-content" data-ajax="<?php echo $ajax ?>" data-delete="<?php _e('Delete', TTO_I18N) ?>">
                                    <label for="tea_add_content_type" class="label-add-content">
                                        <span><?php _e('Choose content type:', TTO_I18N) ?></span>
                                        <input type="hidden" name="tea_action" class="tea_add_action" value="<?php echo $action ?>" />
                                        <input type="hidden" name="tea_nonce" class="tea_add_nonce" value="<?php echo $nonce ?>" />
                                        <select name="tea_add_content_type" class="tea_add_content_type">
                                            <option value="">---</option>
                                            <?php foreach ($typesgood as $key => $typ): ?>
                                                <optgroup label="<?php echo $key ?>">
                                                    <?php foreach ($typ as $k => $v): ?>
                                                        <option value="<?php echo $k ?>"><?php echo $v ?></option>
                                                    <?php endforeach ?>
                                                </optgroup>
                                            <?php endforeach ?>
                                        </select>
                                    </label>

                                    <input type="submit" name="add_content" class="button-secondary" value="<?php _e('Add', TTO_I18N) ?>" />
                                    <div class="clearfix"></div>
                                </div>
                                <!-- /Add content form -->

                            </div>
                        </div>
                        <!-- /Body -->

                        <!-- Footer -->
                        <div class="tea-nav-menu-footer">
                            <div class="major-publishing-actions">
                                <span class="delete-action">
                                    <input type="submit" name="delete_page" class="button-secondary" value="<?php _e('Delete page', TTO_I18N) ?>" />
                                </span>

                                <div class="publishing-action">
                                    <input type="submit" name="save_page" class="button button-primary" value="<?php _e('Save page', TTO_I18N) ?>" />
                                </div>
                            </div>
                        </div>
                        <!-- /Footer -->
                    </div>

                </form>
                <?php $num++ ?>
            <?php endforeach ?>
        <?php else: ?>
            <h3><?php _e('Use this menu to create your page settings.', TTO_I18N) ?></h3>
            <p><?php _e('As you can see, you only need to define your page title and if you want to display the submit button.<br/>Have fun :)', TTO_I18N) ?></p>
        <?php endif ?>
    </div>
    <!-- /Content panel -->

</div>