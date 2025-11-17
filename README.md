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

![Dibujo.png](IMG/Dibujo.png)

### Esquema de Red

| Dispositivo / Servidor | Dirección IP  |
| :--------------------- | :-----------  |
| **Router / DNS / DHCP**| 192.168.9.1   |
|                        | 192.168.26.1  |
| **BBDD**               | 192.168.9.5   |
| **Web SRV**            | 192.168.26.10 |
| **FTP**                | 192.168.26.15 |
| **PC1 - Ubuntu**       | 192.168.9.25  |
| **PC2 - Windows**      | 192.168.9.30  |

## 2. Maquinas

### 1. Router
S'ha desplegat una infraestructura de xarxa multicapa, dissenyada per allotjar serveis web i de dades. La topologia es divideix en tres zones de xarxa diferents, gestionades per un router central (`R-N03`), per aïllar els serveis i controlar el flux de trànsit:

-   **Router Central (R-NCC):** Actua com a nucli de la xarxa, gestionant l'encaminament, el tallafocs i els serveis essencials de xarxa (DHCP i DNS).
-   **Xarxa Intranet (`192.168.9.0/24`):** És la xarxa interna i segura. Allotja les estacions de treball dels clients (`PC1-Ubuntu`, `PC2-Windows`) i el servidor de la base de dades (`B-N03`) per protegir-lo de l'accés directe.
-   **Xarxa DMZ (Zona Desmilitaritzada) (`192.168.26.0/24`):** És una xarxa perimetral dissenyada per allotjar els serveis que necessiten ser accessibles des d'altres xarxes, com el Servidor Web (`W-N03`) i el Servidor FTP (`F-N03`).
-   **Connexió WAN (NAT):** El router proporciona sortida a Internet a les màquines de la Intranet i la DMZ mitjançant Traducció d'Adreces de Xarxa (NAT), permetent actualitzacions de programari mentre es protegeixen les adreces IP internes.

Aquest són els adaptadors que tenim:

enp1s0: NAT

enp2s0: intranet

enp3s0: DMZ

#### Configuració de xarxa:

Per començar, configurem la nostra xarxa, la enp1s0 ens donarà sortida a internet, el enp2s0 és on estarà la intranet, és on es conectaran els nostres clients. També posarem la BBDD, després a la DMZ, els serveis que ens interessa que estiguin exposats a internet, FTP i el servidor web. 
```bash
sudo cat /etc/netplan/00-installer-config.yaml
```

<img width="768" height="434" alt="image" src="https://github.com/user-attachments/assets/08793265-804a-423e-9610-fdc75127bdd7" />

-   **Interfície WAN (`enp1s0`):** S'ha configurat amb `dhcp4: true` perquè obtingui la seva adreça IP de la xarxa externa.
-   **Interfícies Internes (`enp2s0`, `enp3s0`):** S'han configurat amb adreces IP **estàtiques**. Això és **crític i obligatori**. Aquestes adreces són les portes d'enllaç (`gateway`) per a les seves respectives xarxes. Han de ser fixes i predictibles perquè els clients i servidors sàpiguen sempre a qui enviar el trànsit destinat a altres xarxes.

apliquem la configuració:

```bash
sudo netplan apply
```
Verificació:

![img_1.png](IMG/img_1.png)

Configuració IP Forwarding:

Per continuar, hem d'habilitar el reenvio d'IP, això permet que el tràfic passi a través del router i que actuï com a router.

```bash
sudo nano /etc/sysctl.conf
```

<img width="781" height="166" alt="image" src="https://github.com/user-attachments/assets/288f3265-679a-4545-b57e-1379f0828778" />

apliquem el canvi:
```bash
sudo systctl -p
```

Iptables NAT:

El servei `iptables` del nucli de Linux s'utilitza per controlar tot el trànsit que flueix a través del router, actuant com a tallafocs principal i motor de NAT.
```bash
sudo iptables -t nat -A POSTROUTING -O enp1s0 -j MASQUERADE
sudo iptables -t nat -L -v -n
```

<img width="744" height="265" alt="image" src="https://github.com/user-attachments/assets/2e1b7ab3-47c4-4e8a-8588-d5cbb9a981b1" />

La taula `nat` s'encarrega de proporcionar accés a Internet a les dues xarxes internes. Mitjançant una única regla `MASQUERADE` a la cadena `POSTROUTING`, el router tradueix dinàmicament les adreces IP privades a la seva pròpia adreça pública de la interfície WAN. Aquesta configuració permet que tots els equips interns naveguin i rebin actualitzacions de forma segura, ocultant l'estructura de la xarxa interna de l'exterior i optimitzant l'ús d'adreces IP.
```bash
sudo iptables -A FORWARD -i enp2s0 -o enp3s0 -j ACCEPT
sudo iptables -A FORWARD -i enp2s0 -o enp2s0 -m state --state RELATED,ESTABLISHED -j ACCEPT
sudo /usr/sbin/netfilter-persistent save
```

<img width="772" height="91" alt="image" src="https://github.com/user-attachments/assets/12e891a1-3914-4ba8-874a-e1f16e953c40" />

A més d'aquestes comandes, si anem a /etc/iptables/rules.v4 veurem la configuració completa on controlem el transit i accesos a les xarxes. 

![img.png](IMG/img.png)

La taula `filter` gestiona el flux de trànsit entre les interfícies: permet la comunicació des de la Intranet cap a la DMZ i Internet, però bloqueja per defecte les connexions iniciades en sentit invers. La regla més crítica implementa el principi de mínim privilegi, creant una excepció altament específica que permet al Servidor Web comunicar-se amb la Base de Dades a través del port de MySQL. 



#### Configuració DNS:
instal·lació del bind9 per la configuració del DNS.

```bash
sudo apt-get install bind9 bind9utils bind9-doc
```

<img width="810" height="239" alt="image" src="https://github.com/user-attachments/assets/a3b03dc2-d673-4245-8f2e-e708cc98fcec" />

Comprovem que el servei funciona.

```bash
sudo systemctl status named.service
```

<img width="804" height="327" alt="image" src="https://github.com/user-attachments/assets/2222928f-451f-4aa8-83eb-ca7c1732378f" />

Configurarem una zona directa y dues zones inverses, l’idea es que resolgui amb el nom de domini mmt.com

```bash
sudo nano /etc/bind/named.conf.local
```

![img_2.png](IMG/img_2.png)

Zona directa:

```bash
sudo cp /etc/bind/db.local /etc/bind/db.mmt.com
```
<img width="803" height="43" alt="image" src="https://github.com/user-attachments/assets/668d23d4-ceb7-4bcc-a78c-1afee53bc0b1" />

![img_3.png](IMG/img_3.png)

Zona inversa:
Aqui tenim la primera zona inversa, correspont a la DMZ. 
```bash
sudo cp /etc/bind/db.local /etc/bind/db.192.168.26
```
```bash
sudo nano /etc/bind/db.192.168.26
```
<img width="808" height="39" alt="image" src="https://github.com/user-attachments/assets/89ce76f4-f9e5-40fa-b41c-73797a999bc0" />

![img_4.png](IMG/img_4.png)

Segona zona inversa, aquesta es la intranet. 

```bash
sudo nano /etc/bind/db.192.168.9
```
![img_5.png](IMG/img_5.png)

Un cop configurat, podem verificar la sintaxis i que tot estigui correcte amb:
```bash
sudo named-checkzone 26.168.192.in-addr.arpa /etc/bind/db.192.168.26
```
<img width="812" height="112" alt="image" src="https://github.com/user-attachments/assets/e893fcf6-2aa9-41ce-a12a-d933cbdaa2ef" />

#### *Configuració DHCP:*
Instalació DHCP

```bash
sudo apt-get install isc-dhcp-server
```

<img width="812" height="159" alt="image" src="https://github.com/user-attachments/assets/d5e12fd6-8a61-431c-9469-9b5caef02c62" />

Configurem els rangs que li donarem a cada xarxa. Els dividim en dos, pero hem de tenir en compte que tant la Base de dades, com el servei web y també el FTP es mantindran fixes. 
l'arxiu de configuració es el seguent: 

```bash
/etc/dhcp/dhcpd.conf
```
![img_6.png](IMG/img_6.png)
![img_7.png](IMG/img_7.png)

El posem en marxa, ara els ordinadors clients rebran ips i sortiran a internet.

```bash
sudo systemctl restart isc-dhcp-server
sudo systemctl enable isc-dhcp-server
sudo systemctl status isc-dhcp-server
```

<img width="805" height="377" alt="image" src="https://github.com/user-attachments/assets/3db194fc-b5d9-4cbc-8d76-1dd1c9afc8c3" />

### *2. BBDD*

La Base de dades es desplegarà en el servidor amb l'adreça IP **192.168.9.5/24**.
La connectivitat a Internet es proporcionara a través d'una màquina router. Aquesta conexió externa està configurada mitjançant **DHCP**, de manera que l'adreça IP publica ( o de sortida) es gestiona de forma dinàmica pel router.

#### Configuración:

```bash
ip a
ping 192.168.9.1
```

<img width="733" height="264" alt="image" src="https://github.com/user-attachments/assets/4ee83677-edb3-4637-b3f9-15e5aab02728" />

<img width="733" height="200" alt="image" src="https://github.com/user-attachments/assets/fb8942c2-a9a6-4863-8e35-98038777449c" />

Instalación de base de datos mysql

```bash
sudo apt install mysql-server
```

<img width="733" height="248" alt="image" src="https://github.com/user-attachments/assets/1f5afeb8-a75d-4492-b873-2ca640af2018" />

```bash
sudo mysql -u root -p
```

Ingrese a la base de datos y cree el usuario root para que cualquiera que esté con la ip 192.168.26.% pueda acceder a la base de datos

<img width="733" height="324" alt="image" src="https://github.com/user-attachments/assets/42a0aa20-8f1c-42fb-9726-2175bd620895" />
<img width="713" height="51" alt="image" src="https://github.com/user-attachments/assets/99053d43-de91-4424-89fe-915f65b74f16" />
<img width="520" height="335" alt="image" src="https://github.com/user-attachments/assets/c25a0cb6-1287-4a9a-8872-eb52c0f41ab5" />

Le doy privilegios

<img width="719" height="59" alt="image" src="https://github.com/user-attachments/assets/6f3f2202-97c7-435c-8c66-5e1ddad8e5fa" />
<img width="462" height="62" alt="image" src="https://github.com/user-attachments/assets/0668212c-cd51-4063-b966-f12bb878deef" />

Modifico el fitxero mysqld.cnf 

```bash
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

<img width="754" height="649" alt="image" src="https://github.com/user-attachments/assets/c6b44aba-06cb-4906-8fd9-00a1642fe55c" />

Reiniciamos el mysql

```bash
sudo systemctl restart mysql
```

<img width="555" height="26" alt="image" src="https://github.com/user-attachments/assets/2ae99f9b-d3c8-4daa-a775-a50eca22e0cc" />

### *3. FTP*
El servidor FTP (192.168.26.15) que se ubica en la DMZ cumple la función de ser el punto de intercambio y gestión de archivos para los servidores de la zona.
Permite al webmng actualizar los archivos del servidor web, almacena los logs, backups y archivos multimedia. Como esta disponible desde el router, se puede acceder a su contenido sin comprometer la red interna (192.168.9.x).
En cuanto a sus funcionalidades de seguridad, separa el servicio de transferencia de archivos del servidor web. Si algún atacante intenta el FTP, el atacante no obtendrá acceso sidrecto ni a la web ni a la BBDD. 

#### Configuración:

<img width="719" height="270" alt="image" src="https://github.com/user-attachments/assets/29381b60-155a-4c6e-bcb5-5fb1fd2dc052" />

Instalamos FTP

```bash
sudo apt install proftpd
```

<img width="745" height="193" alt="image" src="https://github.com/user-attachments/assets/5f58792a-7352-4a7c-bf90-e791a5f5d144" />

### *4. Web SRV*

El servidor web (192.168.26.10) es la pieza central del proyecto, su funcion principal es alojar la web y servir la pagina a los usuarios y clientes. Dspués de que el router dirija a los usuarios que buscan la pagina en internet a 192.168.26.10, el servidor apache recive la petición. Este procesa contenido estàtico (el index.html y el style.css) y contenido dináimco (el get_data.php) que es un sript que al ejecutarse muestra los datos de la BBDD. Una vez procesado, entregal lla respuesta al router.

#### Configuración:
Instalamos apache2

```bash
sudo apt-get install apache2
```

<img width="809" height="297" alt="image" src="https://github.com/user-attachments/assets/8bd29b96-6e4c-4fc8-8cfb-4ae78d9b50a6" />

Comprobamos que el servicio esté activo i escuchando el puerto 80

```bash
sudo systemctl status apache2.service
sudo lsof -i :80
```

<img width="814" height="319" alt="image" src="https://github.com/user-attachments/assets/ec520a5a-8ff7-497d-bfaf-09d2dc3d54ab" />
<img width="814" height="135" alt="image" src="https://github.com/user-attachments/assets/6144d6f0-1925-415d-9a5a-ad8d16c0539b" />

Modificamos el archivo index.html para que muestre la web que deseamos

```bash
sudo nano /var/www/html/index.html
```

<img width="811" height="517" alt="image" src="https://github.com/user-attachments/assets/48c5a52f-e61d-4ce7-aa51-6e93dd52dabb" />

Creamos el archivo style.css para que se vea como queremos

```bash
sudo touch /var/www/html/style.css
sudo nano /var/www/html/style.css
```

<img width="811" height="540" alt="image" src="https://github.com/user-attachments/assets/f16fa002-e084-4566-a273-e29fb61444ba" />

Creamos el script get_data.php para recoger los datos del servidor de BBDD

```bash
sudo touch /var/www/html/get_data.php
sudo nano /var/www/html/get_data.php
```

<img width="834" height="598" alt="image" src="https://github.com/user-attachments/assets/09b5ce82-1447-42af-8032-a0542ee7e686" />

Instalamos ssh

```bash
sudo apt-get install openssh-service
sudo systemctl status ssh.service
```
<img width="850" height="154" alt="image" src="https://github.com/user-attachments/assets/6b09195c-90f9-41fc-b0e2-022e22840953" />

<img width="812" height="315" alt="image" src="https://github.com/user-attachments/assets/66fe16ff-0f37-4b47-8b6b-ab803884ddaa" />

Solo queda resetear los servicios para aplicar los cambios:

```bash
sudo systemctl restart apache2.service
sudo systemctl status apache2.service
```

<img width="837" height="284" alt="image" src="https://github.com/user-attachments/assets/6cdaae45-d212-470e-8c57-9e5ea484016b" />

Y comprobar la conectividad desde los clientes

<img width="837" height="502" alt="image" src="https://github.com/user-attachments/assets/36221e37-1062-4d9e-ba0e-d8815cfb5289" />

### *5. Clientes*

#### a. PC1 - Ubuntu
Actualizamos las librerias del apt-get y del apt

```bash
sudo apt-get update
sudo apt update
```

<img width="802" height="214" alt="image" src="https://github.com/user-attachments/assets/693c6746-d106-4e62-a5bc-3d2ad07b0100" />

<img width="807" height="214" alt="image" src="https://github.com/user-attachments/assets/7dc4ac15-66c9-4710-97ea-99bf46c77888" />

Instalamos el ssh

```bash
sudo apt-get install openssh-server
sudo systemctl status ssh.service
```

<img width="811" height="208" alt="image" src="https://github.com/user-attachments/assets/b978ca39-036e-4c68-89b9-82b231c73ff0" />

<img width="811" height="208" alt="image" src="https://github.com/user-attachments/assets/c272d8c3-433a-4df1-856c-75e6cb018a8a" />

#### a. PC2 - Windows








