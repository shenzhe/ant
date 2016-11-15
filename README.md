# Ant

由来
===

蚂蚁个体虽小，但有良好的服务分工和治理，使得整个蚂蚁世界井然有序

目标
===
服务化目前是各大公司的趋势，但php世界很明显缺少一个比较好的服务化治理框架, Ant致力于提供一个纯php的分布式服务化框架，提供高性能、透明的服务化治理解决方案

模块
===

| 模块           | 作用           | 一期目标           |
| ------------- |:-------------:|:-------------:|
| ant-register | 统一注册中心|基于mysql实现, 实现基础的配置分发|
| ant-monitor     | 统一监控中心    |实现调用方的性能监控（包含网络时间）<br/>服务提供方的性能监控（不包含网络时间）|
| ant-lib | 公用库      |提供一些公用包<br/>如： exception， 一些base类, socket handler|
| ant-rpc | 统一rpc协议模块      |只支持 自定义包头+json格式的协议|
| ant-config | 统一配置模块      |支持配置下发，同步

依赖
===
zphp            <https://github.com/shenzhe/zphp>

swoole          <https://github.com/swoole/swoole-src>
