<?php
/**
 * Created by PhpStorm.
 * User: tangmusen
 * Date: 2017/8/30
 * Time: 11:49
 */
return [
    'view_replace_str'       => [
        '__CSS__' =>'/static/api/css',
        '__JS__' =>'/static/api/js',
        '__IMG__' =>'/static/api/img',
    ],

    'alipay'=>[
        //应用ID,您的APPID。
        'app_id' => "2018022502269862",

        //商户私钥, 请把生成的私钥文件中字符串拷贝在此
        'merchant_private_key' => "MIIEpQIBAAKCAQEAyEtyRhmrWX/CpQqbhhTi/5jvz+C84QahDvHJ5mj4WetvHgG7gQquEexNExdpRs3P8P8UMTMfnh61Kjmn0kzhFUTUVb+1KKrMLMctgZaFsUNN3bHtNFJBQm2Y5ACy7kqGQs1Cfcn9Tpy17r+HgII4NUXRT5d+IUSxyVg7e77QkpzqsPRfy5qrIoIK6U1XQpj4xH2XsKMOcHc4GNbVPecbf6leno+vYVtIV9+uuL0x6fPbmKXMoSnWA3XdDpIcp1vvtfBUw3BBLYl8C7AEd1MV63Vfs6m4bkXz2+MBO4El14ZEdWcSr1yrWsWwGxhqo2aDr5nUPYzWHV2i6sXIRNP26wIDAQABAoIBAQCATLO6a57zCX+pMI5G6QIvL8wNoKvciN6KMB9gVEUhdEoNMpblJe4y1ObaH9jz3cohWYOZsGHEa28oyR3S/CQB98D73H0yu5Vl6YAgzkZ9Mdui8uI67aWX45RYGIqFUX+HMuwwnz5/KKrxcAZgLwnKNhrJnQTSUqp/iuEvdx8riewXG/dXZ/ILfu5InEtyMm+jYfw1puWXlYM0yuyatvXrkcgF3rJQxXpW+LASMfCxPKvhF5oq7JCIIjbbQSaHYT65VxKy1rjP05dXo+8j7pUsw/XVO3kH0DWBnC9SlZgZqbFwEYR3UXKeqA+Lj25Kr5TucoTqvDbgmb7v/8+XkD7BAoGBAP/fXlc+SVqk1So//AQ8MpQt5ZksBldsoTLIPriciGz3xuzsr9Xq+4is55ESzoL+JBlf1vJ7B0et3f35Ed9SZTAbySEC8Z4+/2yRarsKCTuozTRfHAe+uHi286yfnm9Dn7WxJexW4Yen20TpMsjNSdZ1yJwgpYkBVmUEb8bKh2ZpAoGBAMhk/XFlJb2Nsf06VxSjVIz+4u5AGVEWc/hS7EH3IwN/w34QYQgH8ztJYWZkxH2tdLHFQ3o/EmlTmce7OVgJ3EtIWpjY86ZUyl1FLCVA6GwCi9uoKcnvuNGOWWh40/B3QLfVPPN1J64/f4Rez1m4EsaOrq7lpb+gq9h9PGujVBAzAoGBAIsHueE5zO3dxp3QaoV6mBj/31OdIkz6j5RiTgJNu6tJ7uLpsJlRtx8KpEClsWRn5wGKm+bhhpEiHg9T+KwZvzWmw2CWkubjYDKDyTScPWwXSnj8fOSQcvfUoQ7ZJGNcoTk+albC1oS5ZmFJPPjy4v4OvdRnwMpy2ZjGEl43yd6RAoGAI2Sq2rdJpiP7lsUSEkQSr+Boxjmt/wNfMjG7Jp92oKyVI5PS/28gB42bVVFg3u2e2bMEivfO0amBVKR9qU38iZNa5PoUdoEtSLHp7R58rm74srANyWu8kc55fhkxHZyCYrWX0UwU2RK/++oe5zK6pjUXJy7KSoEfONpNrJkpRL8CgYEA5JWJDIK0jpE2g9Dwq6Wcxdkc6F30c1nmd83Bugtv+U9moLdpCE6vnUdyQFndZhctFjN8Oty+LN5+KI4m4rf38V5+t9nz15/fOMa4RZIGtK+nFZ7tBntSpab8xxKH8O9j8maARlLJ4Brl9nM9wLqJSMB/5VPkqZm+BZCkrsjAugU=",

        //异步通知地址
        'notify_url' => "http://47.98.148.110/zcjy/public/api/alipay/notify",

        //同步跳转
        'return_url' => "",

        //编码格式
        'charset' => "UTF-8",

        //签名方式
        'sign_type'=>"RSA2",

        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuCnJ4jtecfocD2XVBiWDLHEaVNxiei4UZUMZBxitm9y91PC9NC9QWZlLD9YMQz0YgOgaY9UJ8whlK2sfSC6EN6LIBV0vyup1MGtZlEpJtHggNiIDhp6xcZkQ5RL03aLVrRQq8WcCKyDt2aYTNAmX57HZvN4sM4mf30TSSKNrrI+mxzlcRdUiTKsNNnkoJfdbGgLS/2pFOVs3b8w/vvG2Er0OstgiALxJ9kT7e22sqAcZjuhEVL9As18oFvCtav+dobdXrXKlXT1EAczbREXnJl+MetoevzIcE7grPHUiiMuwF9vrZLOuS3ixUnnGW7+5gpswQN2Ntsg+0P35sWBsUQIDAQAB",
    ],
];