<?php

namespace Hashbangcode\WebolutionDemo\Model;

/**
 * Class BaseModel.
 *
 * @package Hashbangcode\WebolutionDemo\Model
 */
abstract class BaseModel implements BaseModelInterface {

  /**
   * The database.
   *
   * @var
   */
  protected $database;

  /**
   * BaseModel constructor.
   *
   * @param \PDO $database
   *   The database connection.
   */
  public function __construct($database) {
    $this->database = $database;
  }

  /**
   * Create the database.
   */
  public function createDatabase()
  {
    $sql = 'DROP TABLE IF EXISTS "evolution";
CREATE TABLE "evolution" (
  "id" integer NOT NULL,
  "current_generation" integer NOT NULL
);

DROP TABLE IF EXISTS "statistic";
CREATE TABLE "statistic" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "evolution_id" integer NOT NULL,
  "population_id" integer NOT NULL,
  "statistic" blob NOT NULL
);

DROP TABLE IF EXISTS "individual";
CREATE TABLE "individual" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "evolution_id" integer NOT NULL,
  "population_id" integer NOT NULL,
  "individual" blob NOT NULL
);

DROP TABLE IF EXISTS "population";
CREATE TABLE "population" (
  "id" integer NOT NULL PRIMARY KEY,
  "evolution_id" integer NOT NULL,
  "population_type" text NOT NULL
);';

    $this->database->exec($sql);
  }


  /**
   * Utility function to cleare out the database.
   */
  public function clearDatabase()
  {
    $tables = [
      'evolution',
      'individuals',
      'populations',
    ];

    foreach ($tables as $table) {
      $sql = 'DELETE FROM ' . $table;
      $this->database->exec($sql);
    }
  }

}
