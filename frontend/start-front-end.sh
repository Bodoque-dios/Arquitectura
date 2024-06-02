#!/bin/bash
# Source the NVM script
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm

cd /home/user/frontend/app
ng serve --host 0.0.0.0 --port 4200

