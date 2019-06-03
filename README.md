# Laravel使用说明

## 使用方法
```php
$limit = cache()->rate($key, $maxNum, $time);
```
## 参数说明
`$key` 键名称，`$time` 有效期，单位s，`$maxNum` 指定时间内最大次数

## 事件

`Lin\RedisLimit\Events\RateStartEvent` 触发前事件
`Lin\RedisLimit\Events\RateEndEvent` 触发后事件
## 备注

当 `$limit` 的值为`0` 表示超出次数，否则返回已请求次数。