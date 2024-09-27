<?php

namespace King\Messages;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    public function sendMessage()
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'user', 'password', 'rmq_test');
        $channel = $connection->channel();

        // Declarar el exchange y la cola
        $channel->exchange_declare('musica', 'direct', false, true, false);
        $channel->queue_declare('artista', false, true, false, false);
        $channel->queue_bind('artista', 'musica', 'musica.bandas');

        // Crear el mensaje
        $data = json_encode([
            'Artista' => 'Lena Katina',
            'Albúm' => 'This is Who I Am',
            'Año' => 2014
        ],JSON_UNESCAPED_UNICODE);

        $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        // Publicar el mensaje
        $channel->basic_publish($msg, 'musica', 'musica.bandas');

        echo " [x] Mensaje enviado: $data\n";

        $channel->close();
        $connection->close();
    }
}