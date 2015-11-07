<?php

namespace ItvisionSy\LaravelElasticSessionDriver;

use ItvisionSy\EsMapper\Model;

/**
 * Description of ElasticSessionModel
 *
 * @author muhannad
 */
class ElasticSessionModel extends Model {

    public static function make(array $array) {
        return new static($array);
    }

}
