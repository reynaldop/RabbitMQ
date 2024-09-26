#!/bin/bash

sleep 5

# Crear un directorio temporal para rabbitmqadmin
mkdir -p /tmp/rabbitmqadmin
cd /tmp/rabbitmqadmin

# Descargar rabbitmqadmin desde la interfaz de administración de RabbitMQ
curl -O http://localhost:15672/cli/rabbitmqadmin
chmod +x rabbitmqadmin

# Esperar a que RabbitMQ esté completamente levantado
echo "Esperando a que RabbitMQ esté listo..."
while true; do
  if curl -u user:password http://localhost:15672/api/overview; then
    break
  fi
  echo "Esperando a RabbitMQ..."
  sleep 5
done

# Crear el exchange 'musica' de tipo 'direct'
./rabbitmqadmin declare exchange name=musica type=direct -u user -p password -V rmq_test

# Crear la queue 'artista'
./rabbitmqadmin declare queue name=artista -u user -p password -V rmq_test

# Crear el binding entre el exchange 'musica' y la queue 'artista' con la routing key 'musica.bandas'
./rabbitmqadmin declare binding source=musica destination=artista routing_key=musica.bandas -u user -p password -V rmq_test

# Limpiar archivos temporales
rm -rf /tmp/rabbitmqadmin