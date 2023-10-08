<?php

namespace Goldfinch\Sitemap\Controllers;

use SilverStripe\Core\Environment;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\HTTPResponse;
use Wilr\GoogleSitemaps\GoogleSitemap;
use Wilr\GoogleSitemaps\Control\GoogleSitemapController as Origin_GoogleSitemapController;

class GoogleSitemapController extends Origin_GoogleSitemapController
{
    private static $allowed_actions = [
        'sitemap',
    ];

    public function sitemap()
    {
        if (Environment::getEnv('APP_SITEMAP_ENCHANT'))
        {
            // - custom start
            $ID = $this->request->param('ID');
            $OtherID = $this->request->param('OtherID');

            $sitemaps = GoogleSitemap::inst()->getSitemaps();
            foreach ($sitemaps as $item)
            {
                $key = ss_env('APP_KEY');
                $sha = sha1($item->ClassName . substr($key, 8));

                if ($sha === $ID)
                {
                    $ID = $item->ClassName;
                    break;
                }
            }
            // - custom end

            $class = $this->unsanitiseClassName($ID);
            $page = intval($OtherID);

            if ($page) {
                if (!is_numeric($page)) {
                    return new HTTPResponse('Page not found', 404);
                }
            }

            if (
                GoogleSitemap::enabled()
                && $class
                && ($page > 0)
                && ($class == SiteTree::class || $class == 'GoogleSitemapRoute' || GoogleSitemap::is_registered($class))
            ) {
                $this->getResponse()->addHeader('Content-Type', 'application/xml; charset="utf-8"');
                $this->getResponse()->addHeader('X-Robots-Tag', 'noindex');

                $items = GoogleSitemap::inst()->getItems($class, $page);
                $this->extend('updateGoogleSitemapItems', $items, $class, $page);

                return array(
                    'Items' => $items
                );
            }

            return new HTTPResponse('Page not found', 404);
        }
        else
        {
            parent::sitemap();
        }
    }

    public function styleSheetIndex()
    {
        $customTemplate = Environment::getEnv('APP_SITEMAP_ENCHANT');

        $html = $this->renderWith($customTemplate ? 'xml-sitemapindex-custom' : 'xml-sitemapindex');

        $this->getResponse()->addHeader('Content-Type', 'text/xsl; charset="utf-8"');

        return $html;
    }

    public function styleSheet()
    {
        $customTemplate = Environment::getEnv('APP_SITEMAP_ENCHANT');

        $html = $this->renderWith($customTemplate ? 'xml-sitemap-custom' : 'xml-sitemap');

        $this->getResponse()->addHeader('Content-Type', 'text/xsl; charset="utf-8"');

        return $html;
    }
}
