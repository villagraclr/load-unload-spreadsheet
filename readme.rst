###################
Cargar archivos Excel en tablas temporales MySQL
###################

Flujo para realizar la carga de archivos Excel en tabla temporal

1.- Formulario para carga de archivo

2.- Carga archivo en directorio temporal Codeigniter

3.- Leer hojas y columnas de cada hoja Excel

4.- Traer primeras x filas de cada hoja

5.- Traer tablas temporales y columnas de cada una de las tablas

6.- Mostrar hojas Excel y preview con primeros x elementos

7.- Relacionar hoja Excel y tabla MySQL

8.- Relacionar columnas hoja Excel seleccionada y tabla MySQL seleccionada

9.- Iterar paso 8 para cada una de las hojas, descartando las tablas previamente seleccionada

10.- Ejecutar inserción según configuraciones realizadas

*******************
Archivos a modificar
*******************

Se debe modificar los siguientes archivos de configuración en Codeigniter

- config/config.php

- config/database.php

*******************
Listar tablas temporales
*******************

Las tablas temporales deben comenzar con el prefijo "tmp_", si no es así las ignorará
