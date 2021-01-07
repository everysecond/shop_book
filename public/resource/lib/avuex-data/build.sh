#!/bin/bash 
echo "传输文件"
scp -r ./** pig@106.14.13.82:/data/avue/avuex-data
echo "部署成功"