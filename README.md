## Prerequisites
You'll need sox, perl, bash, apache2.4, php, a domain and ssl certs to make this work.
You'll need to edit and place etc/seascouts.conf into your apache sites-enables area (if ubuntuish).
This setup is designed for me to attempt to gain some revenue to maintain the service. You'll need to set up somthing for yourselves.

## SeaEscape needs to be able to write to a few locations so leaders can manage their group's settings
```
sudo chmod 1775 coffee coffee/bounds coffee/users html/escape/audio
sudo chgrp www-data coffee coffee/bounds coffee/users html/escape/audio
```

## keys for integrations
For BMC you'll need to change the example coffee/coffeekey file

## keys for access
There are two .htaccess files in the coffee dir. These are writable by www-data and require either manual setting
```
$ htpasswd coffee/.htleaderspasswd <groupname>-leader
```

## upkeep
cron should be used to clear these out using the dates on files written into the coffee directory.

## FYI
Some videos used in the hints for 2 of the questions...

https://youtu.be/EG0c8jC3oV4

https://youtu.be/zuRyzHX-KLY
