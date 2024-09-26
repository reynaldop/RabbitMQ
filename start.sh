#!/bin/bash

# Levantar Docker Compose en modo "detached"
echo "Levantando contenedores con docker-compose..."
docker-compose up -d

# Verificar si Docker Compose se levantó correctamente
if [ $? -eq 0 ]; then
    echo "Docker Compose se levantó correctamente."
else
    echo "Hubo un error al levantar Docker Compose."
    exit 1
fi

# Esperar unos segundos para asegurarse de que los contenedores están listos
echo "Esperando 5 segundos para que los contenedores se estabilicen..."
sleep 5

# Ejecutar el script setup.sh
echo "Ejecutando setup.sh..."
./setup.sh

# Verificar si el script setup.sh se ejecutó correctamente
if [ $? -eq 0 ]; then
    echo "El script setup.sh se ejecutó correctamente."
else
    echo "Hubo un error al ejecutar setup.sh."
    exit 1
fi


#MENSAJE DE PRUEBAS
echo "MENSAJE DE PRUEBAS"
curl -i -u user:password -H "content-type:application/json" \
   -X POST \
   -d'{
         "properties": {},
         "routing_key": "musica.bandas",
         "payload": "{\"Artista\": \"Lena Katina\", \"Albúm\": \"This is Who I Am\", \"Año\": 2014}",
         "payload_encoding": "string"
       }' \
   http://localhost:15672/api/exchanges/rmq_test/musica/publish