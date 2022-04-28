<?php
/**
 * Yii2 behavior for Http Client
 * @package kwazaro\yii2-httpclient-request-limit
 * @author kwazaro
 */
namespace kwazaro\yii2\httpclient;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\httpclient\Client;
use yii\redis\Connection;

class HttpClientRateLimitBehavior extends Behavior
{
    /** @var string name for redis key to manage limits*/
    public $redisKey;

    /** @var string id of redis component  */
    public $redis = 'redis';

    /** @var int maximum number of requests per second  */
    public $maxRequestsPerSecond = 10;

    /** @var int maximum number of requests per minute  */
    public $maxRequestsPerMinute = 100;

    /** @var Connection */
    protected $redisComponent;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->redisComponent = Instance::ensure($this->redis, Connection::class);
        if (empty($this->redisKey) || !is_string($this->redisKey)) {
            throw new InvalidConfigException('"redisKey" property must be set.');
        }
    }

    public function events(): array
    {
        return [
            Client::EVENT_BEFORE_SEND => 'beforeSend',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function beforeSend($event)
    {
        Instance::ensure($event->sender, Client::class);

        // Current timestamp
        $timestamp = time();

        // Check per second limits
        $dataSecond = $this->redisComponent->lrange($this->redisKey, ($this->maxRequestsPerSecond * -1), -1);
        if (count($dataSecond) == $this->maxRequestsPerSecond && $dataSecond[0] == $timestamp) {
            sleep(1);
        }

        // Check per minute limits
        $dataMinute = $this->redisComponent->lrange($this->redisKey, ($this->maxRequestsPerMinute * -1), -1);
        if (count($dataMinute) == $this->maxRequestsPerMinute && ($timestamp - $dataMinute[0]) <= 60) {
            $secondsLeft = 60 - ($timestamp - $dataMinute[0]);
            sleep($secondsLeft);
        }

        // Save timestamp
        $this->redisComponent->rpush($this->redisKey, $timestamp);
    }

    /**
     * Clear redis list
     * @return bool
     */
    public function clearLimits(): bool
    {
        return (bool)$this->redisComponent->del($this->redisKey);
    }
}
