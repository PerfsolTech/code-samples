#!/bin/bash

echo 'deploy setup start'

gpg --keyserver hkp://keys.gnupg.net --recv-keys 409B6B1796C275462A1703113804BB82D39DC0E3
\curl -sSL https://get.rvm.io | bash

source /etc/profile.d/rvm.sh
rvm install 2.3.0
rvm use --ruby-version --create 2.3.0@amurapi
gem install bundler
bundle install
echo "[[ -s \"$HOME/.rvm/scripts/rvm\" ]] && source \"$HOME/.rvm/scripts/rvm\"" >> $HOME/.bashrc