#!/bin/sh
#PATH=/usr/local/php/bin:/opt/someApp/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
#cd /www/wwwroot/www.iredcap.cn/php think queue:listen --queue AutoSettlePaid --timeout 300


#当日结算定时任务  仅执行一次
#crontab -e
#55 23 * * * /www/wwwroot/www.iredcap.cn/data/crond/settle.sh
#*　　*　　*　　*　　*　　command
#分　时　日　月　周　命令
# 在23:50 - 00:00时间段内全部报系统维护中