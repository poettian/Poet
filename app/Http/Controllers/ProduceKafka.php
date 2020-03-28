<?php


namespace App\Http\Controllers;


class ProduceKafka
{
    // 生产消息
    public function produceMsg()
    {
        $conf = new \RdKafka\Conf();
        $conf->set('metadata.broker.list', '192.168.10.2:9092,192.168.10.2:9093,192.168.10.2:9094');
        
        $producer = new \RdKafka\Producer($conf);
        
        // bin/kafka-topics.sh --bootstrap-server localhost:9092 --create --partitions 2 --replication-factor 2 --topic hello
        // bin/kafka-topics.sh --bootstrap-server localhost:9092 --describe --topic hello
        $topic = $producer->newTopic('hello');
        
        // 生产消息
        for ($i = 2;$i < 11;$i++) {
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, json_encode(['id' => $i, 'message' => "第{$i}个消息"]));
            $producer->poll(0);
        }
        
        $start_ts = microtime(true);
        for ($flushRetries = 0; $flushRetries < 3; $flushRetries++) {
            $result = $producer->flush(1000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }
        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost!');
        }
        $end_ts = microtime(true);
        $duration = $end_ts - $start_ts;
        echo "Time duration: {$duration}\n";
        
        /*
        while (($len = $producer->getOutQLen()) > 0) {
            $producer->poll(5);
        }
        */
    }
}