$TTL    604800
@       IN      SOA     ns1.mmt.com. root.mmt.com. (
                        2025110401 ; Serial
                        604800     ; Refresh
                        86400      ; Retry
                        2419200    ; Expire
                        604800 )   ; Negative Cache TTL

        IN      NS      ns1.mmt.com.
@       IN  A   192.168.26.1 ; mmt.com
; === REGISTROS DEL DOMINIO mmt.com ===
ns1             IN      A       192.168.26.1
Router-DMZ      IN      A       192.168.26.1
Router-Intranet IN      A       192.168.9.1
WebServer       IN      A       192.168.26.10
BBDD            IN      A       192.168.9.5
FTP             IN      A       192.168.26.15
PC1-Ubuntu      IN      A       192.168.9.20
PC2-Windows     IN      A       192.168.9.30
