#!/usr/bin/env bash

echo " "
echo "LABELPRINTER"
echo " "

cat <<\EOF > /home/vagrant/labelprinter.sh
#!/bin/bash
while [ 1 ]; do
nc -l -p 9100 >> /vagrant/storage/logs/labelprinter.log;
done
EOF

chown vagrant:vagrant /home/vagrant/labelprinter.sh
chmod +x /home/vagrant/labelprinter.sh

cat <<\EOF > /etc/rc.local
#!/bin/sh -e

/home/vagrant/labelprinter.sh

exit 0
EOF

chmod +x /etc/rc.local

/home/vagrant/labelprinter.sh &