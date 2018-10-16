<?php


class ValidJSONCest
{
    public function _before(ApiTester $I)
    {
      $I->amHttpAuthenticated('tester', 'apitest');
    }

    public function _after(ApiTester $I)
    {
    }

    // tests
    public function testUploadEndpoint(ApiTester $I)
    {

      $I->sendGET('/upload');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }

    public function testSingleUploadEndpoint(ApiTester $I)
    {
      $I->sendGET('/upload/49');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }

    public function testComparisonEndpoint(ApiTester $I)
    {
      $I->sendGET('/comparison');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }

    public function testSingleComparisonEndpoint(ApiTester $I)
    {
      $I->sendGET('/comparison/20');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }

    public function testAllProvidersEndpoint(ApiTester $I)
    {
      $I->sendGET('/provider');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }

    public function testSingleProvidersEndpoint(ApiTester $I)
    {
      $I->sendGET('/provider/1');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }
}
