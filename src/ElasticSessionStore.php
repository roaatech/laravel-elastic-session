<?php

namespace ItvisionSy\LaravelElasticSessionDriver;

use Carbon\Carbon;
use Illuminate\Config\Repository;
use ItvisionSy\EsMapper\TypeQuery;
use SessionHandlerInterface;

/**
 * Description of ElasticSessionStore
 *
 * @author muhannad
 * 
 * @property-read Repository $sessionConfig
 */
class ElasticSessionStore extends TypeQuery implements SessionHandlerInterface {

    protected $sessionConfig;
    protected $_cache = [];
    protected static $defaultConfig = [
        "url" => "http://localhost:9200/",
        "index" => "laravel-es-sessions",
        "type" => "session"
    ];

    public function __construct(array $sessionConfig) {
        $this->sessionConfig = $sessionConfig + static::$defaultConfig;
        parent::__construct([
            'hosts' => [
                $this->sessionConfig['url']
            ]
        ]);
    }

    public static function putMapping() {
        $config = config('session.elastic', static::$defaultConfig);
        $ttl = $this->getTTL();
        $client = new \Elasticsearch\Client(['hosts' => [$config['url']]]);
        $mappingParams = [
            'index' => $config['index'],
            'type' => $config['type'],
            'body' => [
                'properties' => [
                    'created' => ['type' => 'date'],
                    'updated' => ['type' => 'date'],
                    'data' => ['type' => 'string', 'index' => 'no'],
                ],
                '_ttl' => (!!$ttl ? ['enabled' => true, 'default' => $ttl? : '30m'] : ['enabled' => false])
            ]
        ];
        $client->indices()->putMapping($mappingParams);
    }

    public function open($savePath, $sessionName) {
        //not needed, leave empty
    }

    public function close() {
        //not needed, leave empty
    }

    public function gc($maxLifeTime) {
        
    }

    public function read($sessionId) {
        $model = @$this->find($sessionId);
        if (!$model) {
            return null;
        }
        $this->_cache[$sessionId] = $model;
        return $model->data;
    }

    public function write($sessionId, $sessionData) {
        $updatedTs = Carbon::now()->toIso8601String();
        $createdTs = array_key_exists($sessionId, $this->_cache) ? $this->_cache[$sessionId]->created : $updatedTs;
        $ttl = $this->getTTL();
        static::create(['data' => $sessionData, 'created' => $createdTs, 'updated' => $updatedTs, '_ttl' => $ttl], $sessionId, ["api" => "index"]);
    }

    public function destroy($sessionId) {
        static::delete($sessionId, []);
    }

    public function index() {
        return $this->sessionConfig['index'];
    }

    public function modelClassName() {
        return ElasticSessionModel::class;
    }

    public function type() {
        return $this->sessionConfig['type'];
    }

    protected function getTTL() {
        $ttl = config('session.lifetime', 30);
        $ttl.=$ttl ? 'm' : '';
        return $ttl;
    }

}
