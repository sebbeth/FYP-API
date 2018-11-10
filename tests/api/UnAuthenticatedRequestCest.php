<?php


class UnAuthenticatedRequestCest
{

  /*
  This set of tests coveres every valid API endpoint and sends invalid data expecting a 400 or 500 code response
  */

  public function _before(ApiTester $I)
  {
  }

  public function _after(ApiTester $I)
  {
  }


  public function testCPUlistEndpoint(ApiTester $I)
  {
    $I->wantTo("Test the CPU lookup endpoint");
    $I->sendGET('/cpu');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }


  public function testGetAccountEndpoint(ApiTester $I)
  {
    $I->sendGET('/account');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }


  public function testAllProvidersEndpoint(ApiTester $I)
  {
    $I->sendGET('/provider');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }

  public function testSingleProvidersEndpoint(ApiTester $I)
  {
    $I->sendGET('/provider/1');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');

  }

  public function testAddComparisonEndpoint(ApiTester $I)
  {

    $I->haveHttpHeader('Content-Type', 'application/json');

    $data = '{
      "account":9,
      "inputs":[85],
      "solutions":[2,3,4,5,6],
      "parameters":{ }
    }';
    $I->sendPOST('/comparison', $data);
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');

  }


  public function testSingleComparisonEndpoint(ApiTester $I)
  {
    $I->wantTo("Get a single comparison for this account");
    $I->sendGET('/comparison/114');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');

  }


  public function testAllComparisonsEndpoint(ApiTester $I)
  {
    $I->sendGET('/comparison');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }

  // Test uploading new data
  public function testUploadingNewInput(ApiTester $I)
  {

    $I->haveHttpHeader('Content-Type', 'application/json');

    $data = '{}';
    $I->wantTo('upload an input set to API');
    $I->sendPOST('/upload', $data);
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');


    // Now delete the data set
    $I->wantTo('delete the new input set from API');
    $I->sendDELETE("/upload/99");
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');

  }


  public function testUploadEndpoint(ApiTester $I)
  {

    $I->sendGET('/upload');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }

  public function testSingleUploadEndpoint(ApiTester $I)
  {
    $I->sendGET('/upload/85');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }

  public function testAddSolutionEndpoint(ApiTester $I)
  {

    $data = '{}';

    $I->wantTo("Add a new custom solution and verfy that it has been added");
    $I->sendPOST('/solution',$data);
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');


    // Now delete the data set
    $I->wantTo('delete the new custom solution from API');
    $I->sendDELETE("/solution/99");
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');

  }

  public function testAllSolutionsEndpoint(ApiTester $I)
  {
    $I->sendGET('/solution');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }

  public function testSingleSolutionEndpoint(ApiTester $I)
  {
    $I->sendGET('/solution/11');
    $I->seeResponseCodeIs(401);
    $I->assertTrue($I->grabResponse() == '');
  }


}
