<?php
namespace King\Messages;

use Dotenv\Dotenv;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer
{
    public function __construct()
    {
        // Cargar las variables de entorno desde el archivo .env
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    public function consumeMessages()
    {
        try {
            $connection = new AMQPStreamConnection(
                $_ENV['RABBITMQ_HOST'],
                $_ENV['RABBITMQ_PORT'],
                $_ENV['RABBITMQ_USER'],
                $_ENV['RABBITMQ_PASSWORD'],
                $_ENV['RABBITMQ_VHOST']
            );
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

                // Asegúrate de que el ACK se envía siempre que recibas un mensaje válido
                try {
                    // Procesar el mensaje
                    $this->procesarMensaje($msg->body);

                    // Confirmar la recepción del mensaje
                    $channel->basic_ack($msg->delivery_info['delivery_tag']);
                    echo " [x] Mensaje confirmado (ACK enviado).\n";

                } catch (\Exception $e) {
                    // Manejo de errores si el procesamiento del mensaje falla
                    echo " [!] Error procesando el mensaje: " . $e->getMessage() . "\n";
                }

                // Reiniciar el contador de intentos vacíos
                $emptyCheckCount = 0;
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

        // Cerrar el canal y la conexión
        $channel->close();
        try {
            $connection->close();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            die();
        }
    }

    private function procesarMensaje($mensaje)
    {
        // Simular procesamiento del mensaje
        echo " [x] Procesando el mensaje: $mensaje\n";
        // Aquí es donde procesas el mensaje, como guardarlo en una base de datos o hacer otras acciones.
    }
}