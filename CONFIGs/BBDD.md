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