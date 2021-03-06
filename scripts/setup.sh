#!/bin/ash
# from http://wiki.openwrt.org/doc/howto/owncloud
opkg update
opkg install php5 php5-cgi php5-fastcgi php5-mod-json php5-mod-session php5-mod-zip libsqlite3 zoneinfo-core php5-mod-pdo php5-mod-pdo-sqlite php5-mod-ctype php5-mod-mbstring php5-mod-gd sqlite3-cli php5-mod-sqlite3 php5-mod-curl curl php5-mod-xml php5-mod-simplexml php5-mod-hash php5-mod-dom php5-mod-iconv
opkg install php5-mod-mcrypt php5-mod-openssl php5-mod-fileinfo php5-mod-exif
#make php work
uci add_list uhttpd.main.interpreter=".php=/usr/bin/php-cgi"
uci set uhttpd.main.index_page="index.html index.htm default.html default.htm index.php"
uci commit uhttpd
sed -i 's,doc_root.*,doc_root = "",g' /etc/php.ini
sed -i 's,;short_open_tag = Off,short_open_tag = On,g' /etc/php.ini
/etc/init.d/uhttpd restart
#install openvpn
opkg install openvpn pptp
#php.ini needs to be updated as well as /etc/config/uhttpd
/etc/init.d/php5-fastcgi enable
/etc/init.d/php5-fastcgi start
#install privoxy
opkg install privoxy
#install syslog-nd3
opkg install syslog-ng3
/etc/init.d/syslog-ng enable
/etc/init.d/syslog-ng start
#PPTP support
opkg install ppp-mod-pptp
#L2TP support
opkg install openl2tp-full
# Lot o' stuff needs to be done http://wiki.openwrt.org/doc/howto/connect_by_l2tp
chmod 755 /etc/init.d/openl2tpd
