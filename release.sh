#!/bin/bash

#moves to Amfphp repository root folder, and creates a release in dist folder
#for ex call  ./release.sh 2.2.1 to create amfphp-2.2.1 zip

version=$1
sourceFolder=/Users/arielsommeria-klein/Documents/workspaces/workspaceNetbeans/amfphp-2.0/
targetFolder=/Users/arielsommeria-klein/Documents/workspaces/amfphp_dist/amfphp-$version

rm -rf $targetFolder
rm -rf $targetFolder.zip

mkdir $targetFolder

cd $sourceFolder
cp -rf ./Amfphp $targetFolder

cp -rf ./BackOffice $targetFolder
rm $targetFolder/BackOffice/extraConfig.php
rm -r $targetFolder/BackOffice/ClientGenerator/Generated/* 

cp -rf ./Examples $targetFolder

cp -rf ./doc $targetFolder

cp -rf ./goodies $targetFolder

cp ./license.txt $targetFolder

cp ./changelog.txt $targetFolder

cp ./composer.json $targetFolder

cp ./read_me.html $targetFolder

cd $targetFolder/..

#remove all flex bin-debug and bin-release folders
find . -iname *bin-debug* -exec  rm -rf {} \; 
find . -iname *bin-release*  -exec rm -rf {} \;

#do the zip (igonre ds store files from osx explorer)
zip -r amfphp-$version.zip amfphp-$version -x "*.DS_Store"


