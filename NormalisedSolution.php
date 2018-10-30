<?php

class NormalisedSolution {

private $id;
private $provider;
private $account_id;
private $title;
private $type;
private $spec;
private $setup_costs;
private $usage_costs;
private $utilisation;

function __construct($solution)
{
  // Instantaite the member variables

  $this->id = $solution['id'];
  $this->provider = $solution['provider'];
  $this->account_id = $solution['account_id'];
  $data = json_decode($solution['data'],true);
  $this->title = $data['title'];
  $this->type = $data['type'];
  $this->spec = $data['spec'];
  $this->setup_costs = $data['setup_costs'];


  $this->spec = [
    'C' => 0,
    'M' => 0,
    'D' => 0,
    'S' => 0
  ];

  $this->utilisation = [];

  $this->usage_costs = [
    'C' => 0,
    'M' => 0,
    'D' => 0,
    'S' => 0,
    'any' => 0
  ];


  if (isset($data['spec'])) { // Store the spec values
    foreach ($data['spec'] as $item) {
      switch ($item['type']) {
        case 'C':
        $this->spec['C'] = $item['value'];
        break;
        case 'M':
        $this->spec['M'] = doubleval($item['value']);
        break;
        case 'D':
        $this->spec['D'] = doubleval($item['value']);
        break;
        case 'S':
          $this->spec['S'] = doubleval($item['value']);
        break;
        default:
        break;
      }
    }
  }

  // Now calculate new values for the uage_costs so as to abstract the model away from local and hosted solutions

  // Add these to the any cost
  if (isset($data['electical_cost_hourly'])) {
    $this->usage_costs['any'] = doubleval($this->usage_costs['any']) + doubleval($data['electical_cost_hourly']);
  }
  if (isset($data['labour_cost_hourly'])) {
    $this->usage_costs['any'] = doubleval($this->usage_costs['any']) + doubleval($data['labour_cost_hourly']);
  }
  if (isset($data['other_cost_hourly'])) {
    $this->usage_costs['any'] = doubleval($this->usage_costs['any']) + doubleval($data['other_cost_hourly']);
  }

  if (isset($data['usage_costs'])) { // Extract the any cost from usage costs stored in the DB
    foreach ($data['usage_costs'] as $cost) {

      switch ($cost['type']) {
        case 'C':
        //TODO
        break;
        case 'M':
          $this->usage_costs['M'] = doubleval($this->usage_costs['M']) + doubleval($cost['value']);
        break;
        case 'D':
          $this->usage_costs['D'] = doubleval($this->usage_costs['D']) + doubleval($cost['value']);
        break;
        case 'S':
          $this->usage_costs['S'] = doubleval($this->usage_costs['S']) + doubleval($cost['value']);
        break;
        case 'any':
          $this->usage_costs['any'] = doubleval($this->usage_costs['any']) + doubleval($cost['value']);
        break;
        default:
        break;
      }
    }
  }



}

public function getId() {
    return intval($this->id);
}

public function getProvider() {
  return intval($this->provider);
}
public function getAccountId() {
  return intval($this->accountId);
}
public function getTitle() {
  return $this->title;
}
public function getType() {
  return $this->type;
}
public function getSpec() {
  return $this->spec;
}
public function getUsageCosts() {
  return $this->usage_costs;
}
public function getSetupCosts() {
  return $this->setup_costs;
}
public function getUtilisation() {
  return $this->utilisation;
}
public function pushUtilisation($c = 0,$m = 0,$d = 0,$s = 0) {
  $record = [
    'C' => $c,
    'M' => $m,
    'D' => $d,
    'S' => $s
  ];
  array_push($this->utilisation,$record);
}

public function getAverageUtilisation() {
  $output = [
    'C' => 0,
    'M' => 0,
    'D' => 0,
    'S' => 0
  ];
  foreach ($output as $key => $value) { // Store the average of each utilistaion array
    $sum = 0;
    foreach ($this->utilisation as $utilisation) {
      $sum = $sum + doubleval($utilisation[$key]);
    }
    $output[$key] = round(($sum / count($this->utilisation)),2);
  }
  return $output;
}



public function hasSetupCosts() {
  if ( (!isset($this->setup_costs)) || (sizeof($this->setup_costs) == 0) ) {
    return false;
  }
  return true;
}

}

?>
