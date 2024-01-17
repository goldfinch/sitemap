<?php

namespace Goldfinch\Sitemap\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Environment;

class SitemapControllerExtension extends Extension
{
    public function updateGoogleSitemaps(&$sitemaps)
    {
        if (Environment::getEnv('APP_SITEMAP_ENCHANT')) {
            foreach ($sitemaps as $item) {
                $key = Environment::getEnv('APP_KEY');
                $sha = sha1($item->ClassName . substr($key, 8));
                $item->setField('EncryptedClassName', $sha);
            }
        }
    }
}
