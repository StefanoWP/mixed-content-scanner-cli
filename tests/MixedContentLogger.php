<?php

namespace Spatie\MixedContentScannerCli\Test;

use Spatie\Crawler\Url;
use PHPUnit\Framework\Assert;
use Spatie\MixedContentScanner\MixedContent;
use Spatie\MixedContentScanner\MixedContentObserver;

class MixedContentLogger extends MixedContentObserver
{
    protected $log = [];

    public function mixedContentFound(MixedContent $mixedContent)
    {
        $this->log[] = $mixedContent;
    }

    public function noMixedContentFound(Url $crawledUrl)
    {
        $this->log[] = $crawledUrl;
    }

    public function assertPageHasMixedContent(string $pageUrl)
    {
        $foundLogItems = collect($this->log)
            ->filter(function ($logItem) {
                return $logItem instanceof MixedContent;
            })
            ->filter(function (MixedContent $mixedContent) use ($pageUrl) {
                return $mixedContent->foundOnUrl->path === $pageUrl;
            });

        Assert::assertTrue(count($foundLogItems) > 0, "Failed asserting that `{$pageUrl}` contains mixed content");
    }

    public function assertPageHasNoMixedContent(string $pageUrl)
    {
        $foundLogItems = collect($this->log)
            ->filter(function ($logItem) {
                return $logItem instanceof Url;
            })
            ->filter(function (Url $url) use ($pageUrl) {
                return $url->path === $pageUrl;
            });

        Assert::assertTrue(count($foundLogItems) > 0, "Failed asserting that `{$pageUrl}` contains no mixed content. Or maybe that url might not have been crawled");
    }
}
