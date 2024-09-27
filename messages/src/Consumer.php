<?php
namespace King\Messages;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer
{
    public function consumeMessages()
    {
        try {
            $connection = new AMQPStreamConnection('localhost', 5672, 'user', 'password', 'rmq_test');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }
        $channel = $connection->channel();

        // Declarar la cola
        $channel->queue_declare('artista', false, true, false, false);

        echo " [*] Esperando mensajes. Para salir presiona CTRL+C\n";

        $maxEmptyChecks = 5; // Número de intentos sin mensajes antes de parar
        $emptyCheckCount = 0; // Contador de intentos sin mensajes
        $waitTime = 2; // Tiempo en segundos para esperar antes de volver a verificar la cola

        while (true) {
            // Intentar obtener un mensaje de la cola (esto es un "pull" de un solo mensaje)
            $msg = $channel->basic_get('artista');

            if ($msg) {
                // Si recibimos un mensaje, procesarlo
                echo ' [x] Recibido ', $msg->body, "\n";

                // Verificar si el mensaje tiene la propiedad delivery_tag antes de usarla
                if (isset($msg->delivery_tag)) {
                    $channel->basic_ack($msg->delivery_tag); // Aceptar el mensaje
                }

                $emptyCheckCount = 0; // Reiniciar el contador si encontramos un mensaje
            } else {
                // Si no hay mensajes en la cola, incrementar el contador de intentos vacíos
                $emptyCheckCount++;
                echo " [*] No hay mensajes en la cola. Intento $emptyCheckCount/$maxEmptyChecks...\n";

                // Si hemos alcanzado el número máximo de intentos vacíos, salir
                if ($emptyCheckCount >= $maxEmptyChecks) {
                    echo " [x] No se encontraron más mensajes. Cerrando el consumidor.\n";
                    break;
                }

                // Esperar antes de volver a verificar la cola
                sleep($waitTime);
            }
        }

        $channel->close();
        try {
            $connection->close();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }
    }
}