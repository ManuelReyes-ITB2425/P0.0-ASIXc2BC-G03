# *2. BBDD*
## Configuración del servidor de base de datos MySQL

```bash
ip a
ping 192.168.9.1
```
![img_1.png](../IMG/BBDD/image_(1).png)
<img width="733" height="200" alt="image" src="https://github.com/user-attachments/assets/fb8942c2-a9a6-4863-8e35-98038777449c" />

Instalación de base de datos mysql

```bash
sudo apt install mysql-server
```

![img_1.png](../IMG/BBDD/image_(2).png)

Para permitir conexiones desde otros equipos de la red local, se edita el archivo /etc/mysql/mysql.conf.d/mysqld.cnf y se modifica la siguiente línea: bind-address = 0.0.0.0

![img_1.png](../IMG/BBDD/image_(3).png)

Posteriormente, se reinicia el servicio: sudo systemctl restart mysql

![img_1.png](../IMG/BBDD/image_(4).png)

Verificamos el servicio:

![img_1.png](../IMG/BBDD/image_(5).png)

## Gestión de usuarios y privilegios

Se accede al cliente MySQL como root y se crea un usuario remoto con acceso restringido a la subred 192.168.26.0/24: i 192.168.9.0/24

![img_6.png](../IMG/BBDD/image_(6).png)

```bash
CREATE USER 'bchecker'@'192.168.9.%' IDENTIFIED BY 'bchecker121';
CREATE USER 'bchecker'@'192.168.26.%' IDENTIFIED BY 'bchecker121';
```

![img_7.png](../IMG/BBDD/image_(7).png)
![img_8.png](../IMG/BBDD/image_(8).png)

```bash
GRANT ALL PRIVILEGES ON *.* TO 'bchecker'@'192.168.9.%';
GRANT ALL PRIVILEGES ON *.* TO 'bchecker'@'192.168.26.%';
```
![img_9.png](../IMG/BBDD/image_(9).png)
![img_11.png](../IMG/BBDD/image_(11).png)

```bash
FLUSH PRIVILEGES;
```
![img_12.png](../IMG/BBDD/image_(12).png)

Se crean dos usuarios bchecker con el mismo nombre y contraseña, pero limitados a las subredes 192.168.9.0/24 (servidor de base de datos) y 192.168.26.0/24 (servidor FTP), para permitir que equipos de ambas redes se conecten al servidor MySQL. 
Se les otorgan todos los privilegios (*.*) para facilitar el acceso completo en el entorno del laboratorio. Finalmente, FLUSH PRIVILEGES aplica inmediatamente los cambios en la tabla de permisos del sistema.
Aquest usuari pot connectar-se des de qualsevol host de la subxarxa `192.168.9.0/24` i `192.168.26.0`.
Verificamos que los usuarios se hayan creado correctamente en MySQL mediante la consulta del registro de usuarios del sistema:

```bash
SELECT User, Host FROM mysql.user;
```
![img_13.png](../IMG/BBDD/image_(13).png)

### Creación de la base de datos y carga de datos
Primero, se crea la base de datos educacio_bcn: 
```bash
CREATE DATABASE educacio_bcn;
```
![img_14.png](../IMG/BBDD/image_(14).png)

A continuación, se define la tabla equipaments con 40 campos que incluyen:
- Identificadores y nombres de equipamientos e instituciones,
- Metadatos de fechas (created, modified),
- Direcciones estructuradas (roadtype, neighborhood, district, zip code, etc.),
- Filtros secundarios y valores semánticos,
- Coordenadas geográficas en EPSG:25831 (X, Y) y EPSG:4326 (Lat, Lon).
La sentencia CREATE TABLE se ejecuta con todos los tipos de datos adecuados (VARCHAR, TINYINT, DECIMAL, TEXT).

```bash
CREATE TABLE equipaments (
    register_id VARCHAR(50),
    name VARCHAR(255),
    institution_id VARCHAR(50),
    institution_name VARCHAR(255),
    created VARCHAR(50),
    modified VARCHAR(50),
    addresses_roadtype_id VARCHAR(20),
    addresses_roadtype_name VARCHAR(100),
    addresses_road_id VARCHAR(20),
    addresses_road_name VARCHAR(255),
    addresses_start_street_number VARCHAR(20),
    addresses_end_street_number VARCHAR(20),
    addresses_neighborhood_id VARCHAR(20),
    addresses_neighborhood_name VARCHAR(100),
    addresses_district_id VARCHAR(20),
    addresses_district_name VARCHAR(100),
    addresses_zip_code VARCHAR(20),
    addresses_town VARCHAR(100),
    addresses_main_address TINYINT(1),
    addresses_type VARCHAR(50),
    values_id VARCHAR(50),
    values_attribute_id VARCHAR(50),
    values_category VARCHAR(100),
    values_attribute_name VARCHAR(100),
    values_value VARCHAR(100),
    values_outstanding TINYINT(1),
    values_description TEXT,
    secondary_filters_id VARCHAR(50),
    secondary_filters_name VARCHAR(255),
    secondary_filters_fullpath TEXT,
    secondary_filters_tree VARCHAR(50),
    secondary_filters_asia_id VARCHAR(50),
    geo_epgs_25831_x DECIMAL(12,6),
    geo_epgs_25831_y DECIMAL(12,6),
    geo_epgs_4326_lat DECIMAL(12,8),
    geo_epgs_4326_lon DECIMAL(12,8),
    estimated_dates VARCHAR(50),
    start_date VARCHAR(50),
    end_date VARCHAR(50),
    timetable VARCHAR(255)
);
```

![img_15.png](../IMG/BBDD/image_(15).png)

Luego, se verifica que la tabla se haya creado correctamente: 
```bash
SHOW TABLES LIKE 'equipaments';
```
![img_16.png](../IMG/BBDD/image_(16).png)

Y se confirma la estructura de las columnas: 
``` bash
SHOW COLUMNS FROM equipaments;
```
![img_17.png](../IMG/BBDD/image_(17).png)

El archivo fuente opendatabcn_llista-equipaments_educacio.csv está codificado en UTF-16, por lo que no se puede cargar directamente en MySQL, que espera texto en UTF-8. Primero se convierte el archivo:
```bash
iconv -f UTF-16 -t UTF-8 /ruta/origen/opendatabcn_llista-equipaments_educacio.csv > /var/lib/mysql-files/opendatabcn_llista-equipaments_educacio-csv.csv
```

![img_18.png](../IMG/BBDD/image_(18).png)

Luego, se carga el archivo convertido desde el directorio seguro de MySQL:
```bash
LOAD DATA INFILE '/var/lib/mysql-files/equipaments_educacio_utf8.csv'
INTO TABLE equipaments
CHARACTER SET utf8
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;
```

![img_19.png](../IMG/BBDD/image_(19).png)

Finalmente, se verifica que los datos se hayan cargado correctamente y que sean accesibles desde la aplicación web que consulta la base de datos.
![img_20.png](../IMG/imagen.png)
