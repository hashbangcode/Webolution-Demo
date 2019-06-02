<?php

namespace Hashbangcode\WebolutionDemo\Model;

use Hashbangcode\Webolution\Evolution\Evolution as WebolutionEvolution;

/**
 * Class Evolution.
 *
 * @package Hashbangcode\WebolutionDemo\Model
 */
class Evolution extends BaseModel {

  public function getNextId()
  {
    $sql = "SELECT MAX(evolution_id) + 1 AS max_evolution_id FROM evolution";
    $maxEvolutionId = $this->database->query($sql)->fetchColumn();

    if (is_null($maxEvolutionId)) {
      $maxEvolutionId = 1;
    }

    return $maxEvolutionId;
  }

  /**
   * Get the evolution setup.
   *
   * @param int $id
   *   The evolution id.
   *
   * @return \Hashbangcode\Webolution\Evolution\Evolution
   *   The evolution object.
   */
  public function load($id)
  {
    $sql = "SELECT * FROM evolution WHERE id = :evolution_id";
    $stmt = $this->database->prepare($sql);

    if (!$stmt) {
      print_r($this->database->errorInfo());
    }

    $stmt->execute(
      [
        'evolution_id' => $id,
      ]
    );
    $data = $stmt->fetch(\PDO::FETCH_ASSOC);

    $evolution = new WebolutionEvolution();

    if ($data) {
      $evolution->setGeneration($data['current_generation']);
    }

    return $evolution;
  }

  /**
   * Create the evolution.
   *
   * @param \Hashbangcode\Webolution\Evolution\Evolution $evolution
   *   The evolution ID to create.
   *
   * @return int
   *   The new evolution id.
   */
  public function insert($id, WebolutionEvolution $evolution)
  {
    $sql = "INSERT INTO evolution(id, current_generation) VALUES(:id, :current_generation)";
    $query = $this->database->prepare($sql);
    $data = [
      'id' => $id,
      'current_generation' => $evolution->getGeneration(),
    ];
    $query->execute($data);
    return $this->database->lastInsertId();
  }

  /**
   * Create the evolution.
   *
   * @param \Hashbangcode\Webolution\Evolution\Evolution $evolution_id
   *   The evolution ID to create.
   */
  public function update($id, WebolutionEvolution $evolution)
  {
    $sql = "UPDATE evolution SET current_generation = :current_generation WHERE id = :id";
    $query = $this->database->prepare($sql);
    $data = [
      'current_generation' => $evolution->getGeneration(),
      'id' => $id,
    ];

    if (!$query) {
      print_r($this->database->errorInfo());
    }

    $query->execute($data);
  }

  /**
   * Delete the evolution model from the database.
   *
   * @param int $id
   *   The evolution id.
   */
  public function delete($id) {
    $sql = "DELETE FROM evolution WHERE id = :id";
    $query = $this->database->prepare($sql);
    $data = [
      'id' => $id,
    ];

    if (!$query) {
      print_r($this->database->errorInfo());
    }

    $query->execute($data);

    $sql = "DELETE FROM population WHERE evolution_id = :id";
    $query = $this->database->prepare($sql);
    $data = [
      'id' => $id,
    ];

    if (!$query) {
      print_r($this->database->errorInfo());
    }

    $query->execute($data);

    $sql = "DELETE FROM population WHERE evolution_id = :id";
    $query = $this->database->prepare($sql);
    $data = [
      'id' => $id,
    ];

    if (!$query) {
      print_r($this->database->errorInfo());
    }

    $query->execute($data);

    $sql = "DELETE FROM individual WHERE evolution_id = :id";
    $query = $this->database->prepare($sql);
    $data = [
      'id' => $id,
    ];

    if (!$query) {
      print_r($this->database->errorInfo());
    }

    $query->execute($data);
  }

}
