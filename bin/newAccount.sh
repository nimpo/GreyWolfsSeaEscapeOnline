#!/bin/bash

# Sanitise input
GROUP="$*"
echo -n "$GROUP" |tr '\n' '\r' |grep -q '[^A-Za-z0-9_. -]' && echo "Invalid Group Name" >&2 && exit 1
GROUP=`echo $GROUP |sed -e 's/   */ /g'`
echo -n "$GROUP" |grep -qv '^.\{5,64\}' && echo "Group Name bad length" >&2 && exit 1 

# CamelCase group
USERNAME=`echo -n "$GROUP" |tr '_-' ' ' | sed -e 's/\(^\| \)\([a-z]\)/\U\2/g' -e 's/ //g'`

# Get Script location and determine coffeedir
THISSCRIPT=`readlink -f $0`
THISDIR=`dirname $THISSCRIPT`
PARENTDIR=`dirname $THISDIR`
COFFEEDIR="$PARENTDIR/coffee"

# Can haz apache user?
sudo -u www-data true || exit 1

# Confirm action
echo "Adding '$GROUP' leader account: '$USERNAME-leaders'"
echo "  to $COFFEEDIR/.htleaderspasswd"
echo "and corresponding team accounts"
echo "  to $COFFEEDIR/.htpasswd"
echo
echo -n "continue [y/n]? "
read check
echo "$check" |grep -vq '^[yY]$' && echo "Aborted" && exit 1

# Create if not exist
sudo -u www-data touch "$COFFEEDIR/.htleaderspasswd"
sudo -u www-data touch "$COFFEEDIR/.htpasswd"

# Generate random passwords
dd if=/dev/urandom bs=100 count=1 status=none | LC_CTYPE=C tr '\200-\377' '\000-\177' | tr -cd A-Za-z0-9 | cut -c -8 | sudo -u www-data -- awk -v U=$USERNAME -v P="$COFFEEDIR/.htleaderspasswd" '/./ {cmd="htpasswd -i "P" "U"-leaders 2>/dev/null"; print | cmd; close(cmd) ; print U"-leaders:"$0}'
dd if=/dev/urandom bs=100 count=1 status=none | LC_CTYPE=C tr '\200-\377' '\000-\177' | tr -cd A-Za-z0-9 | cut -c -5 | sudo -u www-data -- awk -v U=$USERNAME -v P="$COFFEEDIR/.htpasswd" '/./ {split("black yellow silver blue pink purple",a," "); for (t in a) {cmd="htpasswd -i "P" "U"-"a[t]" 2>/dev/null"; print | cmd; close(cmd) ; print U"-"a[t]":"$0}}'
