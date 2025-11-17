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
