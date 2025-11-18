# G3_ManuelReyes-MiguelValencia-TrishanMizhquiri


1. Topologia
2. Máquinas
   - ROUTER
   - BBDD
   - FTP
   - Web SRV
3. Clientes
4. ProofHub


## 1.Topologia

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

Se ha desplegado una infraestructura de red multicapa, diseñada para alojar servicios web y de datos. La topología se divide en tres zonas de red diferentes, gestionadas por un router central (`R-N03`), para aislar los servicios y controlar el flujo de tráfico:

* **Router Central (R-N03):** Actúa como núcleo de la red, gestionando el enrutamiento, el cortafuegos y los servicios esenciales de red (DHCP y DNS).

* **Red Intranet (`192.168.9.0/24`):** Es la red interna y segura. Aloja las estaciones de trabajo de los clientes (`PC1-Ubuntu`, `PC2-Windows`) y el servidor de la base de datos (`B-N03`) para protegerlo del acceso directo.

* **Red DMZ (Zona Desmilitarizada) (`192.168.26.0/24`):** Es una red perimetral diseñada para alojar los servicios que necesitan ser accesibles desde otras redes, como el Servidor Web (`W-N03`) y el Servidor FTP (`F-N03`).

* **Conexión WAN (NAT):** El router proporciona salida a Internet a las máquinas de la Intranet y la DMZ mediante Traducción de Direcciones de Red (NAT), permitiendo actualizaciones de software mientras se protegen las direcciones IP internas.

Estos son los adaptadores que tenemos:

enp1s0: NAT

enp2s0: intranet

enp3s0: DMZ



### *2. BBDD*

La Base de dades es desplegarà en el servidor amb l'adreça IP **192.168.9.5/24**.
La connectivitat a Internet es proporcionara a través d'una màquina router. Aquesta conexió externa està configurada mitjançant **DHCP**, de manera que l'adreça IP publica ( o de sortida) es gestiona de forma dinàmica pel router.

### *3. FTP*

El servidor FTP (192.168.26.15) que se ubica en la DMZ cumple la función de ser el punto de intercambio y gestión de archivos para los servidores de la zona.
Permite al webmng actualizar los archivos del servidor web, almacena los logs, backups y archivos multimedia. Como esta disponible desde el router, se puede acceder a su contenido sin comprometer la red interna (192.168.9.x).
En cuanto a sus funcionalidades de seguridad, separa el servicio de transferencia de archivos del servidor web. Si algún atacante intenta el FTP, el atacante no obtendrá acceso sidrecto ni a la web ni a la BBDD. 

### *4. Web SRV*

El servidor web (192.168.26.10) es la pieza central del proyecto, su funcion principal es alojar la web y servir la pagina a los usuarios y clientes. Dspués de que el router dirija a los usuarios que buscan la pagina en internet a 192.168.26.10, el servidor apache recive la petición. Este procesa contenido estàtico (el index.html y el style.css) y contenido dináimco (el get_data.php) que es un sript que al ejecutarse muestra los datos de la BBDD. Una vez procesado, entregal lla respuesta al router.

## 3. Clientes








