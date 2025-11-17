### 1. Router

ITB15: 192.168.9.1/24

ITB15a: 192.168.26.1/24

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
