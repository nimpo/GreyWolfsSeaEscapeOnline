## Site is locked while in testing phase

## Hosting prerequisites
You'll need sox (with mp3 libs), perl, bash, apache2.4, php, a domain and ssl certs to make this work.
You'll need to edit and place etc/seascouts.conf into your apache sites-enables area (if ubuntuish) or equivilent.
This setup is designed for me to attempt to gain some revenue to maintain the service. You'll need to set up somthing for yourselves instead of the register pathway; note the bin/newAccount.sh script for manual account setup—usage: 
```
bin/newAccount.sh "group name"
```

## SeaEscape needs to be able to write to a few locations so leaders can manage their group's settings
```
sudo chmod 1775 coffee coffee/bounds coffee/users html/escape/audio
sudo chgrp www-data coffee coffee/bounds coffee/users html/escape/audio
```

See the [webtest.yml](https://github.com/nimpo/GreyWolfsSeaEscapeOnline/blob/main/.github/workflows/webtest.yml) in .github/workflows for a ubuntuish set-up. 

## keys for integrations
If doing this!
For BMC you'll need to change the example coffee/coffeekey file

## keys for access
There are two .htaccess files in the coffee dir. These are writable by www-data and require either manual setting
```
$ htpasswd coffee/.htleaderspasswd <groupname>-leader
```

## upkeep
cron should be used to clear these out using the dates on files written into the coffee directory.
DIY

## FYI
Some videos used in the hints for 2 of the questions...

https://youtu.be/EG0c8jC3oV4

https://youtu.be/zuRyzHX-KLY
