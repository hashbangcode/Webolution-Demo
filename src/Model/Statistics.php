<?php

namespace Hashbangcode\WebolutionDemo\Model;

use Hashbangcode\Webolution\Evolution\Statistics\Statistics as WebolutionStatistics;

class Statistics extends BaseModel {

  public function load($evolutionId, $populationId)
  {
    $sql = "SELECT * FROM statistic WHERE evolution_id = :evolution_id AND population_id = :population_id";
    $stmt = $this->database->prepare($sql);
    $stmt->execute(
      [
        'evolution_id' => $evolutionId,
        'population_id' => $populationId,
      ]
    );

    $data = $stmt->fetch(\PDO::FETCH_ASSOC);

    return unserialize($data['statistic']);
  }

  public function insert($evolutionId, $populationId, $statistic)
  {
    $serializedStatistic = serialize($statistic);

    $sql = 'INSERT INTO statistic(evolution_id, population_id, statistic) ';
    $sql .= 'VALUES (:evolution_id, :population_id, :statistic)';
    $query = $this->database->prepare($sql);
    $query->execute(
      [
        'evolution_id' => $evolutionId,
        'population_id' => $populationId,
        'statistic' => $serializedStatistic,
      ]
    );

    return $this->database->lastInsertId();
  }

}
