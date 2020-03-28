<?php


namespace App\Http\Controllers;

/**
 * High level 对应的是上层应用
 */
class HighKafka
{
    /**
     * 一般使用这个方法来消费就可以
     */
    public function subscribe()
    {
        $conf = new \RdKafka\Conf();
        // 不指定 group.id 报 'segmentation fault' 错误
        $conf->set('group.id', 'group1');
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        $conf->set('auto.offset.reset', 'smallest');
        $conf->set('auto.commit.interval.ms', 100);
    
        // 搞明白这几个参数的意思
        //$conf->set('auto.commit.interval.ms', 1000);
        //$conf->set('enable.auto.commit', 'false');
        //$conf->set('enable.auto.offset.store', 'false');
    
        // subscribe 会触发 rebalance，除非有特殊理由，否则不需要人为干预 rebalance
        /*
        $conf->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            echo "触发 rebalance\n";
            echo $err . "\n";
            var_dump($partitions);
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    // application may load offets from arbitrary external
                    // storage here and update partitions
                    $kafka->assign($partitions);
                    break;
            
                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    $kafka->assign(NULL);
                    break;
            
                default:
                    handle_unlikely_error($err);
                    $kafka->assign(NULL); // sync state
                    break;
            }
        });
        */
    
        // bin/kafka-consumer-groups.sh --bootstrap-server localhost:9092 --group whoami --describe
        // 重置 offset
        // bin/kafka-consumer-groups.sh --bootstrap-server localhost:9092 --group group1 --reset-offsets --execute --to-earliest --topic hello
        $consumer = new \RdKafka\KafkaConsumer($conf);
        
        // 订阅 topic
        $consumer->subscribe(['hello']);
        
        // 消费
        while (true) {
            $message = $consumer->consume(120 * 1000);
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
    
        // $consumer->unsubscribe();
        // @todo 调用 close() 总是会报错
        //$consumer->close();
    }
    
    public function getSubscription()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('group.id', 'whoami');
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
    
        $consumer = new \RdKafka\KafkaConsumer($conf);
    
        // 如果这里不执行订阅操作，getSubscription() 返回为空
        // 根据测试，在调用 consume 前，不会建立到 kafka 的连接
        $consumer->subscribe(['hello']);
        
        var_dump($consumer->getSubscription());
    }
    
    /**
     * 参考 kafka assign vs subscribe
     *
     * assign will manually assign a list of partitions to this consumer.
     * and this method does not use the consumer's group management functionality (where no need of group.id)
     *
     * 因为不涉及 rebalance，所以响应速度很快
     */
    public function assign()
    {
        $conf = new \RdKafka\Conf();
        
        // 不指定 group.id 报 'segmentation fault' 错误，不明白为什么
        $conf->set('group.id', 'group1');
        
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        $conf->set('auto.offset.reset', 'smallest');
        
        $consumer = new \RdKafka\KafkaConsumer($conf);
        
        // 默认 offset 0，即从 beginning of partition 开始消费, 指定 -1001 则从尾部开始消费
        $topic_partition = new \RdKafka\TopicPartition("hello", 1, -1001);
        
        $consumer->assign([$topic_partition]);
    
        // 消费
        while (true) {
            $message = $consumer->consume(120 * 1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    var_dump($message);
                    //print_r(json_decode($message, true));
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
     * 人为提交 offset，同步方式；异步见 commitAsync()
     *
     * enable.auto.commit 这个参数应该设为false
     */
    public function commit()
    {
        // Commit offsets for the current assignment
        // 在未消费新消息的情况下，直接调用，会报错：Local: No offset stored
        //$kafkaConsumer->commit();
    
        // Commit offsets based on the message's topic, partition, and offset
        //$kafkaConsumer->commit($message);
    
        // Commit offsets by providing a list of TopicPartition
        /*
        $kafkaConsumer->commit([
            new RdKafka\TopicPartition($topic, $partition, $offset),
        ]);
        */
        
        $conf = new \RdKafka\Conf();
    
        // 不指定 group.id 报 'segmentation fault' 错误，不明白为什么
        $conf->set('group.id', 'group1');
    
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        $conf->set('auto.offset.reset', 'smallest');
        
        $conf->set('enable.auto.commit', 'false');
    
        $consumer = new \RdKafka\KafkaConsumer($conf);
    
        // 默认 offset 0，即从 beginning of partition 开始消费, 指定 -1001 则从尾部开始消费
        $topic_partition = new \RdKafka\TopicPartition("hello", 1, -1001);
    
        $consumer->assign([$topic_partition]);
    
        while (true) {
            $message = $consumer->consume(5 * 1000);
            if ($message->err == RD_KAFKA_RESP_ERR_NO_ERROR) {
                break;
            }
        }
    
        $consumer->commit();
    }
    
    /**
     * 测试各种 get 方法
     */
    public function getTest()
    {
        $conf = new \RdKafka\Conf();
        // 不指定 group.id 报 'segmentation fault' 错误
        $conf->set('group.id', 'group1');
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        $conf->set('auto.offset.reset', 'smallest');
        
        $consumer = new \RdKafka\KafkaConsumer($conf);
    
        var_dump($consumer->getMetadata(false, NULL, 10e3));
        var_dump($consumer->getCommittedOffsets([new \RdKafka\TopicPartition('hello', 0)], 10000));
        var_dump($consumer->getOffsetPositions([new \RdKafka\TopicPartition('hello', 0)]));
        
        $consumer->subscribe(['hello']);
    
        while (true) {
            $message = $consumer->consume(10 * 1000);
            if ($message->err == RD_KAFKA_RESP_ERR_NO_ERROR || $message->err == RD_KAFKA_RESP_ERR__TIMED_OUT) {
                break;
            }
        }
        
        // rebalance 后才能有值
        var_dump($consumer->getAssignment());
    }
    
    /**
     * 没明白 RdKafka\KafkaConsumerTopic::offsetStore 该怎么用
     */
    public function offsetStore()
    {
    
    }
    
}