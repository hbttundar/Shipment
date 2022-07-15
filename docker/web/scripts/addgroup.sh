#!/bin/bash
set -e
groupadd --force -g $WWWGROUP shipment
useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 shipment
usermod -a -G root shipment && usermod -a -G $WWWGROUP shipment
