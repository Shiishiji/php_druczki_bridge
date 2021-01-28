<?php


namespace Druczki;


use PHPUnit\Framework\TestCase;

class BlanketPdfTest extends TestCase
{
    public function testConstructor(): void
    {
        $blanketPdf = new BlanketPdf([$this->getBlanketInfo()]);

        $this->expectNotToPerformAssertions();
        $blanketPdf->addHtmlLink('https://google.com', 'google');
    }

    private function getBlanketInfo(): BlanketInfo
    {
        return new BlanketInfo(
            'toInfo',
            'bankAccountNumber',
            'amount',
            'fromInfo',
            'title',
            'amountInWords'
        );
    }
}