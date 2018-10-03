<?php


class UploadWithoutInputCest
{
    public function _before(ApiTester $I)
    {
      $I->amHttpAuthenticated('tester', 'apitest');
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function tryToTest(ApiTester $I)
    {
      $I->wantTo("Assert that the correct code is returned when no input is provided for /upload and /comparison endpoints");
      $I->sendPOST('/upload');
      $I->seeResponseCodeIs(400);

      $I->sendPOST('/comparison');
      $I->seeResponseCodeIs(400);

    }
}
