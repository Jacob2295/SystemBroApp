#!/bin/sh
sudo rm /data/db/local.ns
sudo service apache2 start
mongod --fork --logpath /var/log/mongodb.log

# hang out right here until the image is terminated
sleep infinity