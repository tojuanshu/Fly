```php
<?php
// +----------------------------------------------------------------------
// | Fly:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------
/*
         _____  __                             
        /  __/ / /        
       / /__  / /  ____  __                               
      /  __/ / /  /_  / / /                
     / /    / /_   / /_/ /           
    /_/    /___/  /__   /  
     ________________/ /           
    /_______________ _/ 
	
FLY:PHP命令行执行框架
目的：构建灵活，高效，敏捷的CLI应用
==========================================================
使用说明=>
1.Fly安装：
将框架命名为"Fly",放置于非根目录且无其它子文件下。
建议将php命令设为全局命令，执行<php Fly/fly.php>,或在window下双击<Fly/fly.bat>
接下来会
提示是否同意检测基本设置，输入“y”开始检测，输入其它或检测不通过会显示原因并退出安装
检测通过提示是否同意安装，输入“y”开始安装，安装成功会显示系统创建的相关目录
然后会给出简短的提示并停顿，阅读后按回车键继续，简单几下就会结束。


2.了解一下安装中创建的目录
bin:存放可执行命令文件，命令开发的主要工作目录
Fly:Fly的框架文件，勿动
Log:执行过程中产生的错误、日志等记录在其中
Boot:所有命令在启动之初，将读取的全局配置和函数，及相关个性化定制
App:命令执行时，可调用的配置，函数，类库，扩展
Public:命令在执行过程中可能会读取或生成的外部文件
flying.php:所有命令的入口，本身也是一个命令


3.开始创建第一个命令
执行flying.php:<php flying.php -c cmd>
提示输入命令名，如：mycmd
回车确认后，会创建命令bin/mycmd.php
可以尝试执行它<php bin/mycmd.php>,初次执行会显示框架logo,并提示执行的命令名
接下来就可以开始开发了。。。


打开bin/mycmd.php,从php代码开始：
第一行：负责命令初始化和文件回调————这一行是必须的且不得修改的！
接下来是一个与命令相关的类文件，
它继承的父类FlyCmd及Fly，会在执行前做相关处理：
读取该命令的函数(App/Common下)，配置(App/Conf),类库及扩展(App/Lib),然后解析参数，再开始执行
首先会执行这个类的_ini()方法(如果有)，可以在命令执行前做相关处理

然后是这个命令的入口文件，默入是run(),可以通过参数-r <cmd>来选择方法
最后会执行这个类的_over()方法(如果有)，可以在命令执行后做相关处理，如中间有非正常停止将执行不到这
一个命令可以很简单，也可以较复杂，这取决于对命令的编写方式及对扩展的应用！
详情请参阅：Fly/doc/* 
*/
