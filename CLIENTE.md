# Acceso para clientes
### 1. Acceso a la página web
Aquí encontraremos la manera de acceder a la página web creada para consultar los datos del listado de equipamiento educativo de Barcelona.

En primer lugar, se ha acceder a tu buscador de confianza y buscar 
```bash
webserver.mmt.com
```

Lo que deberia salir es la pagina principal:

![alt text](/IMG/imagen.png)

Una vez dentro, podemos buscar lo que queramos de la base de datos siempre y cuando tengamos un ID, nombre, email...
### 2. Acceso al servidor FTP
También, se puede acceder al servicio FTP mediante el terminal:

Nos conectamos mediante la comanda

```bash
ftp 192.168.26.15
```

 ![alt text](/IMG/Img4.png)
 
Cuando te pida credenciales:
- Usuario: anonymous
- Contraseña: anonymous

Una vez dentro, puedes usar estos comandos:
- ls: Lista los archivos disponibles
  ```bash
  ls
  ```
  
  ![alt text](/IMG/Img1.png)
  
- get nombre_archivo: Descarga un archivo
  ```bash
  get nombre_archivo
  ```

  ![alt text](/IMG/Img3.png)
  
- bye: Cierra la conexión
  
   ```bash
   bye
  ```
   ![alt text](/IMG/Img2.png)
