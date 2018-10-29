<?php

namespace Hashbangcode\WebolutionDemo\Model;

class Population extends BaseModel {

  public function load($id, $evolutionId)
  {
    $populationSql = "SELECT * FROM population WHERE id = :id AND evolution_id = :evolution_id";
    $populationStatement = $this->database->prepare($populationSql);

    if (!$populationStatement) {
      print_r($this->database->errorInfo());
    }

    $populationStatement->execute(
      [
        'id' => $id,
        'evolution_id' => $evolutionId,
      ]
    );
    $populationData = $populationStatement->fetch(\PDO::FETCH_ASSOC);

    // @todo this step seems arbitary...?
    $populationType = '\\' . $populationData['population_type'];

    if (!class_exists($populationType)) {
      throw new \Exception('Population type class ' . $populationType . ' does not exist.');
    }
    return new $populationType;
  }

  public function insert($evolutionId, $populationId, $population)
  {
    $sql = 'INSERT INTO population(id, evolution_id, population_type) ';
    $sql .= ' VALUES (:population_id, :evolution_id, :population_type)';
    $query = $this->database->prepare($sql);
    $query->execute(
      [
        'population_id' => $populationId,
        'evolution_id' => $evolutionId,
        'population_type' => get_class($population),
      ]
    );
  }

}
