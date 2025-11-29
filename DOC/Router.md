# 1. Router

ITB15: 192.168.9.1/24

ITB15a: 192.168.26.1/24

enp1s0: NAT

enp2s0: intranet

enp3s0: DMZ

## Configuració de xarxa:

Para comenzar, procederemos a configurar nuestra red. La interfaz enp1s0 nos proporcionará la salida a Internet, mientras que en la enp2s0 se ubicará la intranet, donde se conectarán nuestros clientes.

También implementaremos la base de datos (BBDD). Posteriormente, ubicaremos en la DMZ los servicios que nos interesa exponer a Internet: el servidor FTP y el servidor web.
```bash
sudo cat /etc/netplan/00-installer-config.yaml
```

<img width="768" height="434" alt="image" src="https://github.com/user-attachments/assets/08793265-804a-423e-9610-fdc75127bdd7" />

- **Interfaz WAN (`enp1s0`):** Se ha configurado con `dhcp4: true` para que obtenga su dirección IP de la red externa.
- **Interfaces Internas (`enp2s0`, `enp3s0`):** Se han configurado con direcciones IP **estáticas**. Esto es **crítico y obligatorio**. Estas direcciones son las puertas de enlace (`gateway`) para sus respectivas redes. Deben ser fijas y predecibles para que los clientes y servidores sepan siempre a quién enviar el tráfico destinado a otras redes.

aplicamos la configuración:

```bash
sudo netplan apply
```
Verificación:

![img_1.png](../IMG/img_1.png)

Configuración IP Forwarding:

Para continuar, debemos habilitar el reenvío de IP; esto permite que el tráfico pase a través del router y que actúe como router.

```bash
sudo nano /etc/sysctl.conf
```

<img width="781" height="166" alt="image" src="https://github.com/user-attachments/assets/288f3265-679a-4545-b57e-1379f0828778" />

aplicamos el cambio:
```bash
sudo systctl -p
```

Iptables NAT:

El servicio `iptables` de Linux se utiliza para controlar todo el tráfico que fluye a través del router, actuando como cortafuegos principal y motor de NAT.

```bash
sudo iptables -t nat -A POSTROUTING -O enp1s0 -j MASQUERADE
sudo iptables -t nat -L -v -n
```

<img width="744" height="265" alt="image" src="https://github.com/user-attachments/assets/2e1b7ab3-47c4-4e8a-8588-d5cbb9a981b1" />

La tabla `nat` se encarga de proporcionar acceso a Internet a las dos redes internas. Mediante una única regla `MASQUERADE` en la cadena `POSTROUTING`, el router traduce dinámicamente las direcciones IP privadas a su propia dirección pública de la interfaz WAN. Esta configuración permite que todos los equipos internos naveguen y reciban actualizaciones de forma segura, ocultando la estructura de la red interna del exterior y optimizando el uso de direcciones IP.

```bash
sudo iptables -A FORWARD -i enp2s0 -o enp3s0 -j ACCEPT
sudo iptables -A FORWARD -i enp2s0 -o enp2s0 -m state --state RELATED,ESTABLISHED -j ACCEPT
sudo /usr/sbin/netfilter-persistent save
```

<img width="772" height="91" alt="image" src="https://github.com/user-attachments/assets/12e891a1-3914-4ba8-874a-e1f16e953c40" />

Además de estos comandos, si vamos a `/etc/iptables/rules.v4` veremos la configuración completa donde controlamos el tráfico y los accesos a las redes.

![img.png](../IMG/img.png)

La tabla `filter` gestiona el flujo de tráfico entre las interfaces: permite la comunicación desde la Intranet hacia la DMZ e Internet, pero bloquea por defecto las conexiones iniciadas en sentido inverso. La regla más crítica implementa el principio de mínimo privilegio, creando una excepción altamente específica que permite al Servidor Web comunicarse con la Base de Datos a través del puerto de MySQL.

## Configuración DNS:
instalación del bind9 para la configuración del DNS.

```bash
sudo apt-get install bind9 bind9utils bind9-doc
```

<img width="810" height="239" alt="image" src="https://github.com/user-attachments/assets/a3b03dc2-d673-4245-8f2e-e708cc98fcec" />

Comprobamos que el servicio funciona.

```bash
sudo systemctl status named.service
```

<img width="804" height="327" alt="image" src="https://github.com/user-attachments/assets/2222928f-451f-4aa8-83eb-ca7c1732378f" />

Configuraremos una zona directa y dos zonas inversas; la idea es que resuelva con el nombre de dominio mmt.com.

```bash
sudo nano /etc/bind/named.conf.local
```

![img_2.png](../IMG/img_2.png)

Zona directa:

```bash
sudo cp /etc/bind/db.local /etc/bind/db.mmt.com
```
<img width="803" height="43" alt="image" src="https://github.com/user-attachments/assets/668d23d4-ceb7-4bcc-a78c-1afee53bc0b1" />

![img_3.png](../IMG/img_3.png)

Zona inversa:

Aquí tenemos la primera zona inversa, correspondiente a la DMZ.

```bash
sudo cp /etc/bind/db.local /etc/bind/db.192.168.26
```
```bash
sudo nano /etc/bind/db.192.168.26
```
<img width="808" height="39" alt="image" src="https://github.com/user-attachments/assets/89ce76f4-f9e5-40fa-b41c-73797a999bc0" />

![img_4.png](../IMG/img_4.png)

Segunda zona inversa, esta es la intranet. 

```bash
sudo nano /etc/bind/db.192.168.9
```
![img_5.png](../IMG/img_5.png)

Una vez configurado, podemos verificar la sintaxis y que todo esté correcto con:
```bash
sudo named-checkzone 26.168.192.in-addr.arpa /etc/bind/db.192.168.26
```
<img width="812" height="112" alt="image" src="https://github.com/user-attachments/assets/e893fcf6-2aa9-41ce-a12a-d933cbdaa2ef" />

## *Configuració DHCP:*
Instalación DHCP

```bash
sudo apt-get install isc-dhcp-server
```

<img width="812" height="159" alt="image" src="https://github.com/user-attachments/assets/d5e12fd6-8a61-431c-9469-9b5caef02c62" />

Configuramos los rangos que asignaremos a cada red. Los dividimos en dos, pero debemos tener en cuenta que tanto la Base de Datos, como el servicio web y el FTP se mantendrán fijos. El archivo de configuración es el siguiente:

```bash
/etc/dhcp/dhcpd.conf
```
![img_6.png](../IMG/img_6.png)
![img_7.png](../IMG/img_7.png)

Lo ponemos en marcha; ahora los ordenadores clientes recibirán IPs y saldrán a Internet.

```bash
sudo systemctl restart isc-dhcp-server
sudo systemctl enable isc-dhcp-server
sudo systemctl status isc-dhcp-server
```

<img width="805" height="377" alt="image" src="https://github.com/user-attachments/assets/3db194fc-b5d9-4cbc-8d76-1dd1c9afc8c3" />
