#!/bin/bash

# Sanitise input
GROUP="$*"
echo -n "$GROUP" |tr '\n' '\r' |grep -q '[^A-Za-z0-9_. -]' && echo "Invalid Group Name" >&2 && exit 1
echo -n "$GROUP" |grep -qv '^.\{,64\}$' && "Group Name too long" >&2 && exit 1
SGROUP=`echo -n "$GROUP" |sed -e 's/   */ /g'`
echo -n "$SGROUP" |grep -qv '.....' && echo "Group Name too short" >&2 && exit 1 
[ "$SGROUP" = "" ] && echo "Please specify a Group Name " >&2 && exit 1

# CamelCase group
USERNAME=`echo -n "$SGROUP" |tr '_-' ' ' | sed -e 's/\(^\| \)\([a-z]\)/\U\2/g' -e 's/ //g'`

# Base64 of Group
B64GROUP=`echo -n "$GROUP" |base64 -w0`

# Get Script location and determine coffeedir
THISSCRIPT=`readlink -f $0`
THISDIR=`dirname $THISSCRIPT`
PARENTDIR=`dirname $THISDIR`
COFFEEDIR="$PARENTDIR/coffee"

# Determine apache user here
# assume that setup has happened use file attr and guess best owner for file
APACHEGRP=$(stat -c "%G" "$COFFEEDIR")
APACHEUSER=$(id -un "$APACHEGRP" || getent group "$APACHEGRP" | cut -d: -f4 | cut -d, -f1)
[ "$APACHEUSER" = "" ] && echo "Couldn't determin apache user" && exit 1

# Can haz apache user?
sudo -u "$APACHEUSER" true || exit 1

# Confirm action
echo "Adding '$GROUP' leader account: '$USERNAME-leaders'"
echo "  to $COFFEEDIR/.htleaderspasswd"
echo "and corresponding team accounts"
echo "  to $COFFEEDIR/.htpasswd"
echo "Adding Registration account to $COFFEEDIR/$B64GROUP"
echo "Adding User to account name mapping file to $COFFEEDIR/users/$USERNAME"
echo
echo -n "continue [y/n]? "
read check
echo "$check" |grep -vq '^[yY]$' && echo "Aborted" && exit 1

# Create if not exist
sudo -u "$APACHEUSER" touch "$COFFEEDIR/.htleaderspasswd"
sudo -u "$APACHEUSER" touch "$COFFEEDIR/.htpasswd"

# Generate random passwords
dd if=/dev/urandom bs=100 count=1 status=none | LC_CTYPE=C tr '\200-\377' '\000-\177' | tr -cd A-Za-z0-9 | cut -c -8 | sudo -u "$APACHEUSER" -- awk -v U=$USERNAME -v P="$COFFEEDIR/.htleaderspasswd" '/./ {cmd="htpasswd -i "P" "U"-leaders 2>/dev/null"; print | cmd; close(cmd) ; print U"-leaders:"$0}'
dd if=/dev/urandom bs=100 count=1 status=none | LC_CTYPE=C tr '\200-\377' '\000-\177' | tr -cd A-Za-z0-9 | cut -c -5 | sudo -u "$APACHEUSER" -- awk -v U=$USERNAME -v P="$COFFEEDIR/.htpasswd" '/./ {split("black yellow silver blue pink purple",a," "); for (t in a) {cmd="htpasswd -i "P" "U"-"a[t]" 2>/dev/null"; print | cmd; close(cmd) ; print U"-"a[t]":"$0}}'

# Put starting files in place
echo "Locally Set on `date` by $USER" | sudo -u "$APACHEUSER" tee $COFFEEDIR/$B64GROUP >/dev/null
echo -n "$B64GROUP" | sudo -u "$APACHEUSER" tee "$COFFEEDIR/users/$USERNAME" >/dev/null
