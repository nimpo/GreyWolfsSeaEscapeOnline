#!/bin/bash

# Sanitise input
GROUP="$*"
echo -n "$GROUP" |tr '\n' '\r' |grep -q '[^A-Za-z0-9_. -]' && echo "Invalid Group Name" >&2 && exit 1
GROUP=`echo $GROUP |sed -e 's/   */ /g'`
echo -n "$GROUP" |grep -qv '^.\{5,64\}' && echo "Group Name bad length" >&2 && exit 1 

# CamelCase group
USERNAME=`echo -n "$GROUP" |tr '_-' ' ' | sed -e 's/\(^\| \)\([a-z]\)/\U\2/g' -e 's/ //g'`-leaders

# Get Script location and determine coffeedir
THISSCRIPT=`readlink -f $0`
THISDIR=`dirname $THISSCRIPT`
PARENTDIR=`dirname $THISDIR`
COFFEEDIR="$PARENTDIR/coffee"

# Can haz apache user?
sudo -u www-data true || exit 1

# Confirm action
echo "Adding '$GROUP' leader account '$USERNAME'"
echo "  to $COFFEEDIR/.htleaderspasswd"
echo -n "continue [y/n]? "
read check
echo "$check" |grep -vq '^[yY]$' && echo "Aborted" && exit 1

# Create if not exist, generate passwd and create
sudo -u www-data touch "$COFFEEDIR/.htleaderspasswd"
echo -n "$USERNAME:"
dd if=/dev/urandom bs=100 count=1 status=none | tr -cd A-Za-z0-9 | cut -c -8 | tee >(sudo -u www-data htpasswd -i "$COFFEEDIR/.htleaderspasswd" $USERNAME 2>/dev/null)
