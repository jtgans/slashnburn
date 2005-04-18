#!/bin/bash

_SVNROOT=svn+ssh://june@tank/var/lib/svn

build_export()
{
    BUILDDIR=$(pwd)
    TMPDIR=$(mktemp -d)

    echo "Exporting code..."
    svn export -q $_SVNROOT/slashnburn/head $TMPDIR/slashnburn
    svn export -q $_SVNROOT/htmlarea/head   $TMPDIR/slashnburn/assets/htmlarea
    svn export -q $_SVNROOT/jtlib/head      $TMPDIR/slashnburn/common/jtlib
    svn export -q $_SVNROOT/smarty/head     $TMPDIR/slashnburn/common/smarty
    chmod 777 $TMPDIR/slashnburn/common/compiled-tmpl

    echo "Exporting database..."
    mysqldump --allow-keywords --user=slashnburn --password=slashnburn --single-transaction slashnburn >$TMPDIR/slashnburn/database.sql

    echo "Tarring up build..."
    tar zcf $BUILDDIR/slashnburn.tar.gz -C $TMPDIR --exclude=build.sh slashnburn

    echo "Cleaning up..."
    rm -rf $TMPDIR

    echo "Build complete."
}

usage()
{
    echo Usage:
    echo     export  -  Export a copy of the entire SNB source archive into a tarball.
    echo
}

case $1 in
    export)
        build_export
        ;;
    *)
        usage
        ;;
esac
