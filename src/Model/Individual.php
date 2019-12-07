<?php

namespace Hashbangcode\WebolutionDemo\Model;

use Hashbangcode\Webolution\IndividualInterface;
use Hashbangcode\Webolution\Evolution as WebolutionEvolution;

class Individual extends BaseModel {

  public function load($id)
  {
    $sql = "SELECT * FROM individual WHERE id = :individual_id";
    $stmt = $this->database->prepare($sql);
    $stmt->execute(
      [
        'individual_id' => $id,
      ]
    );
    $data = $stmt->fetch(\PDO::FETCH_ASSOC);
    return unserialize($data['individual']);
  }

  public function insert($evolutionId, $populationId, $individual)
  {
    $serializedIndividual = serialize($individual);

    $sql = 'INSERT INTO individual(evolution_id, population_id, individual) ';
    $sql .= 'VALUES (:evolution_id, :population_id, :individual)';
    $query = $this->database->prepare($sql);
    $query->execute(
      [
        'evolution_id' => $evolutionId,
        'population_id' => $populationId,
        'individual' => $serializedIndividual,
      ]
    );

    return $this->database->lastInsertId();
  }

  public function loadGeneration($evolutionId, WebolutionEvolution $evolution)
  {
    $individualSql = "SELECT * FROM individual ";
    $individualSql .= "WHERE evolution_id = :evolution_id AND population_id = :population_id";
    $individualStatement = $this->database->prepare($individualSql);

    $individualStatement->execute(
      [
        'evolution_id' => $evolutionId,
        'population_id' => $evolution->getGeneration(),
      ]
    );

    $individuals = [];

    // Load the individuals from the database and add them to the population.
    $individualData = $individualStatement->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($individualData as $key => $data) {
      $individual = unserialize($data['individual']);
      $individuals[$data['id']] = $individual;
    }

    $evolution->getCurrentPopulation()->setIndividuals($individuals);
  }

  /**
   *
   */
  public function insertFromPopulation($evolutionId, $populationId, $population)
  {
    $individuals = [];

    // Insert the individuals into the database.
    foreach ($population->getIndividuals() as $key => $individual) {
      $serializedIndividual = serialize($individual);

      $sql = 'INSERT INTO individual(evolution_id, population_id, individual) ';
      $sql .= 'VALUES (:evolution_id, :population_id, :individual)';
      $query = $this->database->prepare($sql);

      $query->execute(
        [
          'population_id' => $populationId,
          'evolution_id' => $evolutionId,
          'individual' => $serializedIndividual,
        ]
      );

      $newId = $this->database->lastInsertId();
      $individuals[$newId] = $individual;
    }

    $population->setIndividuals($individuals);
  }

}
