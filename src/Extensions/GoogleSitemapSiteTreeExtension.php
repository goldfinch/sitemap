<?php

namespace Goldfinch\Sitemap\Extensions;

use Wilr\GoogleSitemaps\Extensions\GoogleSitemapSiteTreeExtension as GoogleSitemapSiteTreeExtension_Origin;

class GoogleSitemapSiteTreeExtension extends GoogleSitemapSiteTreeExtension_Origin
{
    public function updateSettingsFields(&$fields)
    {
        parent::updateSettingsFields($fields);

        $googleSitemapTab = $fields->findTab('Root.Settings')->getChildren()->findTab('GoogleSitemap');

        $fields->removeByName([
            // 'GoogleSitemapIntro',
            'GoogleSitemap',
            'Settings',
        ]);

        $fields->insertAfter('MetaDescription', $googleSitemapTab);
    }
}
