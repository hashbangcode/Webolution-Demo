<?php

namespace Hashbangcode\WebolutionDemo;

use Hashbangcode\Webolution\Evolution\Individual\ColorIndividual;
use Hashbangcode\Webolution\Evolution\Population\ColorPopulation;
use Hashbangcode\WebolutionDemo\Model\Evolution as EvolutionModel;
use Hashbangcode\WebolutionDemo\Model\Population as PopulationModel;
use Hashbangcode\WebolutionDemo\Model\Individual as IndividualModel;
use Hashbangcode\WebolutionDemo\Model\Statistics as StatisticsModel;

class DashboardManager {

  protected $database;

  protected $evolutionMapper;

  protected $populationMapper;

  protected $individualMapper;

  protected $statisticMapper;

  public function __construct($database)
  {
    $this->database = $database;

    $this->evolutionMapper = new EvolutionModel($this->database);
    $this->populationMapper = new PopulationModel($this->database);
    $this->individualMapper = new IndividualModel($this->database);
    $this->statisticMapper = new StatisticsModel($this->database);
  }

  public function loadEvolution($evolutionId, $numberOfIndividuals) {
    $evolution = $this->evolutionMapper->load($evolutionId);
    $evolution->setIndividualsPerGeneration($numberOfIndividuals);
    $evolution->setGlobalMutationAmount(50);
    //$evolution->setReplicationType(Evolution::REPLICATION_TYPE_CLONE);
    return $evolution;
  }

  public function setupEvolution($evolutionId, $evolution, $numberOfIndividuals, $type) {
    $populationType = '\\Hashbangcode\\Webolution\\Evolution\\Population\\' . $type . 'Population';
    $population = new $populationType();
    $population->setDefaultRenderType('html');

    $individualType = '\\Hashbangcode\\Webolution\\Evolution\\Individual\\' . $type . 'Individual';

    for ($i = 0; $i < $numberOfIndividuals; $i++) {
      $population->addIndividual();
    }

    $evolution->setPopulation($population);
    $this->evolutionMapper->insert($evolutionId, $evolution);
    $this->populationMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
    $this->individualMapper->insertFromPopulation($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
    $this->statisticMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation()->getStatistics());
  }

  public function loadPopulation($evolutionId, $evolution) {
    $population = $this->populationMapper->load($evolution->getGeneration(), $evolutionId);
    $evolution->setPopulation($population);
    $this->individualMapper->loadGeneration($evolutionId, $evolution);
    $population->setStatistics($this->statisticMapper->load($evolutionId, $evolution->getGeneration()));
    return $population;
  }

  public function saveEvolution($evolutionId, $evolution) {
    $this->evolutionMapper->update($evolutionId, $evolution);
    $this->populationMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
    $this->individualMapper->insertFromPopulation($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation());
    $this->statisticMapper->insert($evolutionId, $evolution->getGeneration(), $evolution->getCurrentPopulation()->getStatistics());
  }

  public function selectForIndividual($luckyIndividualId, $evolution) {
    $newPopulation = $evolution->getCurrentPopulation()->getIndividuals();
    foreach ($newPopulation as $individualId => $individual) {
      if ($individualId != $luckyIndividualId) {
        unset($newPopulation[$individualId]);
      }
    }

    $evolution->getCurrentPopulation()->setIndividuals($newPopulation);
  }

}