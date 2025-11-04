# G3_ManuelReyes-MiguelValencia-TrishanMizhquiri


1. Topologia
2. Máquinas
   - ROUTER
   - BBDD
   - FTP
   - Web SRV
4. Clientes
   - PC1 - Ubuntu
   - PC2 - Windows

6. Comprobaciones
   - Servidor
   - FTP
   - Web
   - DNS
   - DHCP
   - Base de datos

8. ProofHub


## 1. Topologia

### Esquema de Red

| Dispositivo / Servidor | Dirección IP  |
| :--------------------- | :-----------  |
| **Router / DNS / DHCP**| 192.168.9.1   |
|                        | 192.168.26.1  |
|                        |               |
| **BBDD**               | 192.168.9.5   |
| **Web SRV**            | 192.168.26.10 |
| **FTP**                | 192.168.26.15 |
| **PC1 - Ubuntu**       | 192.168.9.25  |
| **PC2 - Windows**      | 192.168.9.30  |

## 2. Maquinas

### 1. Router

ITB15: 192.168.9.1/24

ITB15a: 192.168.26.1/24

enp1s0: NAT

enp2s0: intranet

enp3s0: DMZ

#### Configuració de xarxa:

<img width="768" height="434" alt="image" src="https://github.com/user-attachments/assets/08793265-804a-423e-9610-fdc75127bdd7" />

Configuració IP Forwarding:

<img width="781" height="166" alt="image" src="https://github.com/user-attachments/assets/288f3265-679a-4545-b57e-1379f0828778" />

Iptables NAT:

<img width="744" height="265" alt="image" src="https://github.com/user-attachments/assets/2e1b7ab3-47c4-4e8a-8588-d5cbb9a981b1" />
<img width="772" height="91" alt="image" src="https://github.com/user-attachments/assets/12e891a1-3914-4ba8-874a-e1f16e953c40" />

#### Configuració DNS:
instal·lació del bind9 per la configuració del DNS.

<img width="810" height="239" alt="image" src="https://github.com/user-attachments/assets/a3b03dc2-d673-4245-8f2e-e708cc98fcec" />

Comprovem que el servei funciona.

<img width="804" height="327" alt="image" src="https://github.com/user-attachments/assets/2222928f-451f-4aa8-83eb-ca7c1732378f" />

Configurarem una zona directa y dues zones inverses, l’idea es que resolgui amb el nom de domini grup3.com

#### *Configuració DHCP:*
Instalació DHCP 

<img width="812" height="159" alt="image" src="https://github.com/user-attachments/assets/d5e12fd6-8a61-431c-9469-9b5caef02c62" />

Configurem els rangs que li donarem a cada xarxa. Els dividim en dos, pero hem de tenir en compte que tant la Base de dades, com el servei web y també el FTP es mantindran fixes. 

<img width="532" height="305" alt="image" src="https://github.com/user-attachments/assets/8351d374-d9c8-4049-96fd-047c2a94b582" />

El posem en marxa, ara els ordinadors clients rebran ips i sortiran a internet. 

<img width="805" height="377" alt="image" src="https://github.com/user-attachments/assets/3db194fc-b5d9-4cbc-8d76-1dd1c9afc8c3" />

### *2. BBDD*

La Base de dades es desplegarà en el servidor amb l'adreça IP **192.168.9.5/24**.
La connectivitat a Internet es proporcionara a través d'una màquina router. Aquesta conexió externa està configurada mitjançant **DHCP**, de manera que l'adreça IP publica ( o de sortida) es gestiona de forma dinàmica pel router.

#### Configuración:
<img width="733" height="264" alt="image" src="https://github.com/user-attachments/assets/4ee83677-edb3-4637-b3f9-15e5aab02728" />
<img width="733" height="200" alt="image" src="https://github.com/user-attachments/assets/fb8942c2-a9a6-4863-8e35-98038777449c" />

Instalación de base de datos mysql

<img width="733" height="248" alt="image" src="https://github.com/user-attachments/assets/1f5afeb8-a75d-4492-b873-2ca640af2018" />

Ingrese a la base de datos y cree el usuario root para que cualquiera que esté con la ip 192.168.26.% pueda acceder a la base de datos

<img width="733" height="324" alt="image" src="https://github.com/user-attachments/assets/42a0aa20-8f1c-42fb-9726-2175bd620895" />
<img width="713" height="51" alt="image" src="https://github.com/user-attachments/assets/99053d43-de91-4424-89fe-915f65b74f16" />
<img width="520" height="335" alt="image" src="https://github.com/user-attachments/assets/c25a0cb6-1287-4a9a-8872-eb52c0f41ab5" />

Le doy privilegios

<img width="719" height="59" alt="image" src="https://github.com/user-attachments/assets/6f3f2202-97c7-435c-8c66-5e1ddad8e5fa" />
<img width="462" height="62" alt="image" src="https://github.com/user-attachments/assets/0668212c-cd51-4063-b966-f12bb878deef" />

Modifico el fitxero mysqld.cnf 

<img width="754" height="649" alt="image" src="https://github.com/user-attachments/assets/c6b44aba-06cb-4906-8fd9-00a1642fe55c" />

Reiniciamos el mysql

<img width="555" height="26" alt="image" src="https://github.com/user-attachments/assets/2ae99f9b-d3c8-4daa-a775-a50eca22e0cc" />

### *3. FTP*
El servidor FTP (192.168.26.15) que se ubica en la DMZ cumple la función de ser el punto de intercambio y gestión de archivos para los servidores de la zona.
Permite al webmng actualizar los archivos del servidor web, almacena los logs, backups y archivos multimedia. Como esta disponible desde el router, se puede acceder a su contenido sin comprometer la red interna (192.168.9.x).
En cuanto a sus funcionalidades de seguridad, separa el servicio de transferencia de archivos del servidor web. Si algún atacante intenta el FTP, el atacante no obtendrá acceso sidrecto ni a la web ni a la BBDD. 

#### Configuración:

<img width="719" height="270" alt="image" src="https://github.com/user-attachments/assets/29381b60-155a-4c6e-bcb5-5fb1fd2dc052" />

Instalamos FTP

<img width="745" height="193" alt="image" src="https://github.com/user-attachments/assets/5f58792a-7352-4a7c-bf90-e791a5f5d144" />

### *4. Web SRV*

El servidor web (192.168.26.10) es la pieza central del proyecto, su funcion principal es alojar la web y servir la pagina a los usuarios y clientes. Dspués de que el router dirija a los usuarios que buscan la pagina en internet a 192.168.26.10, el servidor apache recive la petición. Este procesa contenido estàtico (el index.html y el style.css) y contenido dináimco (el get_data.php) que es un sript que al ejecutarse muestra los datos de la BBDD. Una vez procesado, entregal lla respuesta al router.

#### Configuración:
Instalamos apache2

<img width="809" height="297" alt="image" src="https://github.com/user-attachments/assets/8bd29b96-6e4c-4fc8-8cfb-4ae78d9b50a6" />

Comprobamos que el servicio esté activo i escuchando el puerto 80

<img width="814" height="319" alt="image" src="https://github.com/user-attachments/assets/ec520a5a-8ff7-497d-bfaf-09d2dc3d54ab" />
<img width="814" height="135" alt="image" src="https://github.com/user-attachments/assets/6144d6f0-1925-415d-9a5a-ad8d16c0539b" />

Modificamos el archivo index.html para que muestre la web que deseamos

<img width="811" height="517" alt="image" src="https://github.com/user-attachments/assets/48c5a52f-e61d-4ce7-aa51-6e93dd52dabb" />

Creamos el archivo style.css para que se vea como queremos

<img width="811" height="540" alt="image" src="https://github.com/user-attachments/assets/f16fa002-e084-4566-a273-e29fb61444ba" />

Creamos el script get_data.php para recoger los datos del servidor de BBDD

<img width="834" height="598" alt="image" src="https://github.com/user-attachments/assets/09b5ce82-1447-42af-8032-a0542ee7e686" />

Solo queda resetear el servicio para aplicar los cambios:

<img width="837" height="284" alt="image" src="https://github.com/user-attachments/assets/6cdaae45-d212-470e-8c57-9e5ea484016b" />

Y comprobar la conectividad desde los clientes

<img width="837" height="502" alt="image" src="https://github.com/user-attachments/assets/36221e37-1062-4d9e-ba0e-d8815cfb5289" />









