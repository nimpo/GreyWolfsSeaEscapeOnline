#!/bin/bash

if sudo true
then
  echo "Sudo rights OK"
else
  echo "You need sudo rights for this script" >&2
  exit 1
fi

# Determine Apache user group (try $1 $2 then other local generic configs

AUSER=$(id -un "$1" 2>/dev/null || ( . /etc/apache2/envvars ; echo $APACHE_RUN_USER | xargs -i id -un '{}' ) || sudo apachectl -S 2>/dev/null |grep ^User: |sed -e 's/^User: name="\([^"]\{1,\}\)*".*/\1/' | xargs -i id -un '{}' || grep "^User[[:space:]]" /etc/httpd/conf/httpd.conf |tail -n1 |sed -e 's/^User[[:space:]*]\([^[:space:]]*\).*/\1/' | xargs -i id -un '{}' )
[ "$AUSER" = "" ] && echo "Cannot determine Apache User"
AGROUP=$( getent group "$2" |cut -d: -f1 |grep . || ( . /etc/apache2/envvars ; echo $APACHE_RUN_GROUP | xargs -i getent group '{}' |cut -d: -f1 |grep . ) || sudo apachectl -S 2>/dev/null |grep ^Group: |sed -e 's/^Group: name="\([^"]\{1,\}\)*".*/\1/' | xargs -i getent group '{}' |cut -d: -f1 |grep . || grep "^Group[[:space:]]" /etc/httpd/conf/httpd.conf |tail -n1 |sed -e 's/^Group[[:space:]*]\([^[:space:]]*\).*/\1/' | xargs -i getent group '{}' |cut -d: -f1 |grep . || id -gn $AUSER )
[ "$AGROUP" = "" ] && echo "Cannot determine Apache Group"

# Get Script location and determine coffeedir

THISSCRIPT=`readlink -f $0`
THISDIR=`dirname $THISSCRIPT`
PARENTDIR=`dirname $THISDIR`

# Confirm action
echo "About to chmod 1775 and chgrp some directories under PARENTDIR to $AGROUP"
echo $PARENTDIR/html/escape/audio
echo $PARENTDIR/coffee
echo $PARENTDIR/coffee/bounds
echo $PARENTDIR/coffee/users

echo -n "continue [y/n]? "
read check
echo "$check" |grep -vq '^[yY]$' && echo "Aborted" && exit 1

sudo chgrp $AGROUP $PARENTDIR/html/escape/audio $PARENTDIR/coffee $PARENTDIR/coffee/bounds $PARENTDIR/coffee/users
sudo chmod 1775 $PARENTDIR/html/escape/audio $PARENTDIR/coffee $PARENTDIR/coffee/bounds $PARENTDIR/coffee/users
