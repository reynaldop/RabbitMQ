# RabbitMQ Docker Setup

Cuando ya tengas clonado el proyecto, asegurate de poner los permisos necesarios a los scripts.

chmod +x setup.sh

chmod +x start.sh

Para leventar y ejectar RMQ únicamente hay que ejecutar 

./start.php

en el navegador visitar

http://localhost:15672/

acceder con las credencales ubicadas en el archivo messages/.env

para poder hacer pruebas de funcionamento solo es necesario ejecutar los archivos

messages/consumerMessage.php //Para publicar en RMQ

messages/consumerMessage.php //Para leer de RMQ
