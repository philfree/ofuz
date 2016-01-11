#!/usr/bin/env bash
mysql -u root -p < Dump20151222.sql
if ! mysql -u root -e "gishwhes_hunt"; then
    mysql -u root -e "create database gishwhes_hunt;";
fi
if ! mysql -u root -e "gishwhes_blog"; then
    mysql -u root -e "create database gishwhes_blog;";
fi
mysql -u root --execute="use gishwhes_hunt;";