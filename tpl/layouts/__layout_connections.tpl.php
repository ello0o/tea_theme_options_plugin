<?php
//Build data
$titles = array(
    'title' => __('Connections', TTO_I18N),
    'name' => __('Connections', TTO_I18N),
    'slug' => '_connections',
    'submit' => false
);
$details = array(
    array(
        'type' => 'heading',
        'title' => __('Simple, use these boxes to fully integrate your Tea Theme Options for Wordpress with your favorites social networks.', TTO_I18N)
    ),
    array(
        'type' => 'twitter',
        'title' => __('Twitter.', TTO_I18N),
        'description' => __('Login to your Twitter profile to get all your photos.', TTO_I18N)
    ),
    array(
        'type' => 'instagram',
        'title' => __('Instagram.', TTO_I18N),
        'description' => __('Login to your Instagram profile to get all your photos.', TTO_I18N)
    ),
    array(
        'type' => 'flickr',
        'title' => __('FlickR.', TTO_I18N),
        'description' => __('Login to your FlickR profile to get all your photos.', TTO_I18N)
    )
);