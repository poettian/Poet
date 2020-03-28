<?php

namespace App\Http\Controllers;

/**
 * Low level 对应的是底层应用
 */
class LowKafka
{
    /**
     * 不更新 group 的 consume offset
     */
    public function consumeOnly()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        
        $consumer = new \RdKafka\Consumer($conf);
        
        $topic = $consumer->newTopic('hello');
    
        $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);
    
        while (true) {
            $message = $topic->consume(0, 10*1000);
            if ($message === null) {
                echo "Timed out\n";
                continue;
            }
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
    
    /**
     * 更新 group 的 consume offset
     */
    public function consumeUpdate()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
    
        // 正常并不需要，因为 low level consumer 并不加入 group，当然更不会更新 group 的 consume offset
        // 这里必须设置
        $conf->set('group.id', 'group1');
        
        $consumer = new \RdKafka\Consumer($conf);
    
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set('auto.commit.interval.ms', 100);
        // 这个是需要的，因为初始时没有 offset
        $topicConf->set('auto.offset.reset', 'smallest');
        // 这个也可以不设置，因为默认就是 broker
        // consumeStart 为 RD_KAFKA_OFFSET_STORED 时，才会更新 offset
        $topicConf->set('offset.store.method', 'broker');
        
        $topic = $consumer->newTopic('hello', $topicConf);
        
        $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
    
        while (true) {
            $message = $topic->consume(0, 10*1000);
            if ($message === null) {
                echo "Timed out\n";
                continue;
            }
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
    
    /**
     * 手动设置 group consumer offset
     */
    public function offsetStore()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        $conf->set('group.id', 'group1');
    
        $consumer = new \RdKafka\Consumer($conf);
    
        $topicConf = new \RdKafka\TopicConf();
        // 脚本结束时好像会自动commit
        $topicConf->set('auto.commit.interval.ms', 100);
        $topicConf->set('auto.offset.reset', 'smallest');
    
        $topic = $consumer->newTopic('hello', $topicConf);
    
        // 同样，这里也必须为 RD_KAFKA_OFFSET_STORED
        $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
        
        $message = $topic->consume(0, 10 * 1000);
    
        // 这里的 offset 必须大于当前 offset 才可以
        $topic->offsetStore($message->partition, $message->offset);
    }
    
    /**
     * 批量消费，同时更新 group 的 consume offset
     */
    public function consumeBatchUpdate()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        $conf->set('group.id', 'group1');
    
        $consumer = new \RdKafka\Consumer($conf);
    
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set('auto.commit.interval.ms', 100);
        $topicConf->set('auto.offset.reset', 'smallest');
    
        $topic = $consumer->newTopic('hello', $topicConf);
    
        $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
    
        $messages = $topic->consumeBatch(0, 10*1000, 2);
        
        var_dump($messages);
    }
    
    /**
     * 从多个 topic 和 partition 中消费
     */
    public function consumeQueueUpdate()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        $conf->set('group.id', 'group1');
    
        $consumer = new \RdKafka\Consumer($conf);
    
        $queue = $consumer->newQueue();
    
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set('auto.commit.interval.ms', 100);
        $topicConf->set('auto.offset.reset', 'smallest');
    
        $topic1 = $consumer->newTopic('hello', $topicConf);
        $topic1->consumeQueueStart(0, RD_KAFKA_OFFSET_STORED, $queue);
        $topic1->consumeQueueStart(1, RD_KAFKA_OFFSET_STORED, $queue);
    
        while (true) {
            $message = $topic1->consume(0, 10*1000);
            if ($message === null) {
                echo "Timed out\n";
                continue;
            }
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
}