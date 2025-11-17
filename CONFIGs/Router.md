### 1. Router

ITB15: 192.168.9.1/24

ITB15a: 192.168.26.1/24

enp1s0: NAT

enp2s0: intranet

enp3s0: DMZ

#### Configuració de xarxa:

```bash
sudo cat /etc/netplan/00-installer-config.yaml
```

<img width="768" height="434" alt="image" src="https://github.com/user-attachments/assets/08793265-804a-423e-9610-fdc75127bdd7" />

Configuració IP Forwarding:

<img width="781" height="166" alt="image" src="https://github.com/user-attachments/assets/288f3265-679a-4545-b57e-1379f0828778" />

Iptables NAT:

```bash
sudo iptables -t nat -A POSTROUTING -O enp1s0 -j MASQUERADE
sudo iptables -t nat -L -v -n
```

<img width="744" height="265" alt="image" src="https://github.com/user-attachments/assets/2e1b7ab3-47c4-4e8a-8588-d5cbb9a981b1" />

```bash
sudo iptables -A FORWARD -i enp2s0 -o enp3s0 -j ACCEPT
sudo iptables -A FORWARD -i enp2s0 -o enp2s0 -m state --state RELATED,ESTABLISHED -j ACCEPT
sudo /usr/sbin/netfilter-persistent save
```

<img width="772" height="91" alt="image" src="https://github.com/user-attachments/assets/12e891a1-3914-4ba8-874a-e1f16e953c40" />

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

<img width="778" height="484" alt="image" src="https://github.com/user-attachments/assets/3e34396c-f24a-464f-841b-a86568554ad0" />

```bash
sudo cp /etc/bind/db.local /etc/bind/db.mmt.com
```
<img width="803" height="43" alt="image" src="https://github.com/user-attachments/assets/668d23d4-ceb7-4bcc-a78c-1afee53bc0b1" />

<img width="806" height="646" alt="image" src="https://github.com/user-attachments/assets/b4d81110-e648-4ad2-9b7c-8568ed9dc85d" />

Zona inversa:

```bash
sudo cp /etc/bind/db.local /etc/bind/db.192.168.26
```

<img width="808" height="39" alt="image" src="https://github.com/user-attachments/assets/89ce76f4-f9e5-40fa-b41c-73797a999bc0" />

<img width="724" height="493" alt="image" src="https://github.com/user-attachments/assets/f4df9167-a902-4f28-aa94-3ab27f2f2f84" />

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

<img width="532" height="305" alt="image" src="https://github.com/user-attachments/assets/8351d374-d9c8-4049-96fd-047c2a94b582" />

El posem en marxa, ara els ordinadors clients rebran ips i sortiran a internet.

```bash
sudo systemctl restart isc-dhcp-server
sudo systemctl enable isc-dhcp-server
sudo systemctl status isc-dhcp-server
```

<img width="805" height="377" alt="image" src="https://github.com/user-attachments/assets/3db194fc-b5d9-4cbc-8d76-1dd1c9afc8c3" />
