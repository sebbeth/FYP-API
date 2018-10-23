<?php


class ValidJSONCest
{
    public function _before(ApiTester $I)
    {
      $I->amHttpAuthenticated('autotest', 'SENG4800B');
    }

    public function _after(ApiTester $I)
    {
    }

    // Test uploading new data
    public function testUploadingNewInput(ApiTester $I)
    {

      $I->haveHttpHeader('Content-Type', 'application/json');

      $data = '{
  "description": "SQL Server 2012 Jul-Aug 2018 V1.0",
  "start_date": "2018-07-21T00:00:00",
  "spec": [
    {
      "type": "C",
      "value": "Intel Xeon CPU E5-2670 @ 2.60GHz",
      "unit": "model",
      "notes": "https://ark.intel.com/products/64595/Intel-Xeon-Processor-E5-2670-20M-Cache-2-60-GHz-8-00-GT-s-Intel-QPI-"
    },
    {
      "type": "M",
      "value": "16",
      "unit": "gb"
    },
    {
      "type": "S",
      "value": "200",
      "unit": "gb"
    }
  ],
  "data": [
    {
      "C": 13.82917,
      "M": 13.29696,
      "S": 180.75684,
      "notes": "DataKey=2018_07_21_00"
    },
    {
      "C": 33.86231,
      "M": 13.32983,
      "S": 180.75684,
      "notes": "DataKey=2018_07_21_01"
    },
    {
      "C": 26.16121,
      "M": 13.38664,
      "S": 180.75684,
      "notes": "DataKey=2018_07_21_02"
    },
    {
      "C": 33.77816,
      "M": 13.46240,
      "S": 180.75684,
      "notes": "DataKey=2018_07_21_03"
    }
	]
}';
      $I->wantTo('upload an input set to API');
      $I->sendPOST('/upload', $data);

      $response = $I->grabResponse();
      $parsedResponse = json_decode($response,true);
      $I->assertTrue(isset($parsedResponse));
      $inputId = $parsedResponse['id']; // Get the ID from the response
      $I->assertTrue(isset($inputId));

      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();


      // Now delete the data set
      $I->wantTo('delete the new input set from API');
      $I->sendDELETE("/upload/$inputId");
      $I->seeResponseCodeIs(200); // assert that we get the right response,

      // now check that it really did delete.
      $I->sendGET("/upload/$inputId");
      $I->seeResponseCodeIs(400);


    }

    public function testAllComparisonsEndpoint(ApiTester $I)
    {
      $I->wantTo("Get all comparisons for this account");
      $I->sendGET('/comparison');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
      $response = $I->grabResponse();
      $I->assertTrue(isset($response));
    }


    public function testAllSolutionsEndpoint(ApiTester $I)
    {
      $I->wantTo("Get all custom solutions for this account");
      $I->sendGET('/solution');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
      $response = $I->grabResponse();
      $I->assertTrue(isset($response));
    }

    public function testSingleSolutionEndpoint(ApiTester $I)
    {
      $I->sendGET('/solution/11');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
      $response = $I->grabResponse();
      $I->assertTrue(isset($response));
    }

    public function testUploadEndpoint(ApiTester $I)
    {

      $I->sendGET('/upload');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }

    public function testSingleUploadEndpoint(ApiTester $I)
    {
      $I->sendGET('/upload/85');
      $I->seeResponseCodeIs(200);
      $I->seeResponseIsJson();
    }


    public function testComparisonEndpoint(ApiTester $I)
    {

      $I->haveHttpHeader('Content-Type', 'application/json');
      $I->wantTo('Execute a comparison using one input set and several solutions');

      $data = '{
	        "account":9,
	         "inputs":[85],
	          "solutions":[2,3,4,5,6],
	           "parameters":{ }
           }';
      $I->sendPOST('/comparison', $data);

      $I->seeResponseIsJson();
      $response = $I->grabResponse();
      $parsedResponse = json_decode($response,true);
      $I->assertTrue(isset($parsedResponse));
      $resultId = $parsedResponse['id']; // Get the ID from the response
      $I->assertTrue(isset($resultId));

      $I->seeResponseCodeIs(200);

      // Now get the result

      $I->sendGET("/comparison/$resultId");
      $I->seeResponseIsJson();
      $I->seeResponseCodeIs(200);

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
