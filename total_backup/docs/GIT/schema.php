//--- install
sudo apt-get install libcurl4-gnutls-dev libexpat1-dev gettext libz-dev libssl-dev git-core git-gui lighttpd

//------------------------------------------
// 0. Configuration
//------------------------------------------

//--- config
git config --global user.name "krudysz"
git config --global user.email krudysz@gmail.com
git config --global core.editor geany
git config --global merge.tool meld

//------------------------------------------
// 1.  PRODUCTION: Repo (itsdev6.vip.gatech.edu)
//------------------------------------------
cd /var/www/ITS-GT
git init
git add *
git commit -a -m 'version 208'
cd /var/www
git clone --bare ITS-GT ITS-GT.git
scp -r ITS-GT.git root@itsdev6.vip.gatech.edu:/opt/git

//------------------------------------------
// 2.  PRODUCTION: Publish
//------------------------------------------
cd /var/www
sudo git clone /opt/git/ITS-GT.git html 

//------------------------------------------
// 3.  DEV: Repo (itsdev3.vip.gatech.edu)
//------------------------------------------
cd /opt/git
sudo git clone &lt;username&gt;@itsdev6.vip.gatech.edu:/opt/git/ITS-GT.git html

//------------------------------------------
// 4.  DEV: Publish
//------------------------------------------
cd /var/www
sudo git clone /opt/git/ITS-GT.git html 

//------------------------------------------
// 5.  DEV: Remote Repo
//------------------------------------------
cd /home/&lt;username&gt;
sudo git clone /opt/git/ITS-GT.git html
cd html
git branch dev

//------------------------------------------
// 6.  LOCAL: Repo
//------------------------------------------
cd /var/www
git clone &lt;username&gt;@itsdev3.vip.gatech.edu:/home/&lt;username&gt;/html
cd html

//------------------------------------------
// 7.  LOCAL: Update
//------------------------------------------
git add *
git commit -am "first message"

//------------------------------------------
// 8.  LOCAL: Push to Remote
//------------------------------------------
git push origin --delete dev
git push origin HEAD:dev

//------------------------------------------
// 9.  DEV: Push to Repo
//------------------------------------------
git push origin --delete dev
git push origin HEAD:dev

//------------------------------------------
// 10.  DEV: Pull to Publish
//------------------------------------------
cd /var/www/html
git pull

//------------------------------------------
// 11.  DEV: Push to Repo
//------------------------------------------
git push origin --delete dev
git push origin HEAD:dev

//------------------------------------------
// 12.  PRODUCTION: Pull to Publish
//------------------------------------------
cd /var/www/html
git pull



