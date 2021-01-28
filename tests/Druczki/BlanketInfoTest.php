<?php


namespace Tests\Druczki;

use Druczki\BlanketInfo;
use PHPUnit\Framework\TestCase;

class BlanketInfoTest extends TestCase
{
    public function testConstructor(): void
    {
        $blanketInfo = new BlanketInfo(
            'toInfo',
            'bankAccountNumber',
            'amount',
            'fromInfo',
            'title',
            'amountInWords'
        );

        self::assertEquals('title', $blanketInfo->getTitle());
    }
}