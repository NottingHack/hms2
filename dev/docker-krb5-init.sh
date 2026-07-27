#!/bin/sh

sleep 5

# Setup kerberos keytab
export KRB5_CONFIG=/shared/krb5.conf
cat > "$KRB5_CONFIG" <<EOF
[libdefaults]
 dns_lookup_realm = false
 ticket_lifetime = 24h
 renew_lifetime = 7d
 forwardable = true
 rdns = false
 default_realm = NOTTINGHACK

[realms]
 NOTTINGHACK = {
    kdc = hms-krb5
    admin_server = hms-krb5
 }
EOF

echo 'very-secure-passwords' | kinit admin/admin@NOTTINGHACK
klist

echo 'very-secure-passwords' | kadmin -q "addprinc -pw very-secure-passwords hms/admin@NOTTINGHACK"

rm -f /shared/hms.keytab
ktutil <<EOF
addent  -password -p hms/admin@NOTTINGHACK -k 1 -e aes128-cts-hmac-sha1-96
very-secure-passwords
wkt /shared/hms.keytab
EOF
chmod a+r /shared/hms.keytab

