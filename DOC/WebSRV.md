# *4. Web SRV*

## Configuración:

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

Modificamos el archivo [index.html](/FILES/SRVWeb/index.html) para que muestre la web que deseamos

```bash
sudo nano /var/www/html/index.html
```

<img width="811" height="517" alt="image" src="https://github.com/user-attachments/assets/48c5a52f-e61d-4ce7-aa51-6e93dd52dabb" />

Creamos el archivo [style.css](/FILES/SRVWeb/style.css) para que se vea como queremos

```bash
sudo touch /var/www/html/style.css
sudo nano /var/www/html/style.css
```

<img width="811" height="540" alt="image" src="https://github.com/user-attachments/assets/f16fa002-e084-4566-a273-e29fb61444ba" />

Creamos el script [get_data.php](/FILES/SRVWeb/get_data.php) para recoger los datos del servidor de BBDD

```bash
sudo touch /var/www/html/get_data.php
sudo nano /var/www/html/get_data.php
```

<img width="834" height="598" alt="image" src="https://github.com/user-attachments/assets/09b5ce82-1447-42af-8032-a0542ee7e686" />

Instalamos ssh

```bash
sudo apt-get install openssh-service
sudo apt install php-mysqli
sudo apt install mysql-client
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

![alt text](/IMG/image.png)
