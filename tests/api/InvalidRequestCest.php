<?php


class InvalidRequestCest
{

  /*
  This set of tests coveres every valid API endpoint and sends invalid data expecting a 400 or 500 code response
  */

  private $accountId;

  public function _before(ApiTester $I)
  {
    $I->amHttpAuthenticated('autotest', 'SENG4800B');
    $this->accountId = 9;
  }

  public function _after(ApiTester $I)
  {
  }





  public function testSingleProvidersEndpoint(ApiTester $I)
  {
    $I->sendGET('/provider/132sd4567');
    $I->seeResponseCodeIs(400);
  }

  public function testAddComparisonEndpoint(ApiTester $I)
  {

    $I->haveHttpHeader('Content-Type', 'application/json');

    $data = '{
      43e":[85],
      "solutions":[2,3,4,5,6],
      "parameters":{ }
    }';
    $I->sendPOST('/comparison', $data);
    $I->seeResponseCodeIs(400);

  }


  public function testSingleComparisonEndpoint(ApiTester $I)
  {
    $I->sendGET('/comparison/345e46786');
    $I->seeResponseCodeIs(200);
    $parsedResponse = json_decode($I->grabResponse(),true);
    $I->assertTrue(isset($parsedResponse));
    $I->assertTrue($parsedResponse['status'] == 'not found');

  }


  // Test uploading new data
  public function testUploadingNewInput(ApiTester $I)
  {

    $I->haveHttpHeader('Content-Type', 'application/json');

    $data = '{
    }';
    $I->sendPOST('/upload', $data);
    $I->seeResponseCodeIs(400);

  }


  public function testSingleUploadEndpoint(ApiTester $I)
  {
    $I->sendGET('/upload/823adsfd5645');
    $I->seeResponseCodeIs(400);
  }

  public function testAddSolutionEndpoint(ApiTester $I)
  {

    $data = '
    {
tyuhijklm
    }';

    $I->sendPOST('/solution',$data);
    $I->dontSeeResponseCodeIs(200);

  }


  public function testSingleSolutionEndpoint(ApiTester $I)
  {
    $I->sendGET('/solution/1ret45rf1');
    $I->dontSeeResponseCodeIs(200);

  }



}
