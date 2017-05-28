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
sed -i -e 's/^exit 0/\/home\/vagrant\/labelprinter.sh \&\n\nexit 0/' /etc/rc.local
/home/vagrant/labelprinter.sh &