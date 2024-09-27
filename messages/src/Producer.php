<?php

namespace King\Messages;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Dotenv\Dotenv; // Para usar la librería phpdotenv

class Producer
{
    public function __construct()
    {
        // Cargar las variables de entorno desde el archivo .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    public function sendMessage()
    {
        try {
            $connection = new AMQPStreamConnection(
                $_ENV['RABBITMQ_HOST'],
                $_ENV['RABBITMQ_PORT'],
                $_ENV['RABBITMQ_USER'],
                $_ENV['RABBITMQ_PASSWORD'],
                $_ENV['RABBITMQ_VHOST']);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }
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
        try {
            $connection->close();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }
    }
}