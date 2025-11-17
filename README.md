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
S'ha desplegat una infraestructura de xarxa multicapa, dissenyada per allotjar serveis web i de dades. La topologia es divideix en tres zones de xarxa diferents, gestionades per un router central (`R-N03`), per aïllar els serveis i controlar el flux de trànsit:

-   **Router Central (R-N03):** Actua com a nucli de la xarxa, gestionant l'encaminament, el tallafocs i els serveis essencials de xarxa (DHCP i DNS).
-   **Xarxa Intranet (`192.168.9.0/24`):** És la xarxa interna i segura. Allotja les estacions de treball dels clients (`PC1-Ubuntu`, `PC2-Windows`) i el servidor de la base de dades (`B-N03`) per protegir-lo de l'accés directe.
-   **Xarxa DMZ (Zona Desmilitaritzada) (`192.168.26.0/24`):** És una xarxa perimetral dissenyada per allotjar els serveis que necessiten ser accessibles des d'altres xarxes, com el Servidor Web (`W-N03`) i el Servidor FTP (`F-N03`).
-   **Connexió WAN (NAT):** El router proporciona sortida a Internet a les màquines de la Intranet i la DMZ mitjançant Traducció d'Adreces de Xarxa (NAT), permetent actualitzacions de programari mentre es protegeixen les adreces IP internes.

Aquest són els adaptadors que tenim:

enp1s0: NAT

enp2s0: intranet

enp3s0: DMZ



### *2. BBDD*

La Base de dades es desplegarà en el servidor amb l'adreça IP **192.168.9.5/24**.
La connectivitat a Internet es proporcionara a través d'una màquina router. Aquesta conexió externa està configurada mitjançant **DHCP**, de manera que l'adreça IP publica ( o de sortida) es gestiona de forma dinàmica pel router.













