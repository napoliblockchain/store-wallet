#!/bin/bash
echo Updating...
if [ ! -d "assets" ]; then
    mkdir assets
fi
if [ ! -d "protected/qrcodes" ]; then
    mkdir protected/qrcodes
fi
#la cartella uploads serve a caricare i file nel bug form
if [ ! -d "uploads" ]; then
    mkdir uploads
fi

git stash
git pull

chown -R www-data:www-data assets/
chown -R www-data:www-data uploads/
chown -R www-data:www-data protected/runtime/
chown -R www-data:www-data protected/qrcodes/
chown -R www-data:www-data protected/log/

chmod 755 protected/yiic
chmod 755 update.sh
chmod +x update.sh

echo Versioning...
git rev-parse HEAD>version.txt
echo Done!
