<?php


class AllUploadCest
{
    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
      $I->wantTo('Test all upload');
      $I->sendGET('/upload');
      $I->seeResponseIsJson();
      $I->seeResponseCodeIs(200);
    }
}