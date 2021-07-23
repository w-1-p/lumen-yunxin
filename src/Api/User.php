<?php

namespace W1p\LumenYunxin\Api;

use GuzzleHttp\Exception\GuzzleException;
use W1p\LumenYunxin\Exception\YunXinInnerException;
use W1p\LumenYunxin\Exception\YunXinNetworkException;
use W1p\LumenYunxin\Exception\YunXinBusinessException;

class User extends Base
{
    /**
     * 创建网易云通信ID
     *
     * @param string $accid 网易云通信ID，最大长度32字符，必须保证一个APP内唯一。
     * （只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理，请注意以此接口返回结果中的accid为准）
     * @param array $options 可选参数集合，支持如下：
     *
     * - name: string, 网易云通信ID昵称，最大长度64字符。
     *
     * - icon: string, 网易云通信ID头像URL，开发者可选填，最大长度1024
     *
     * - token: string,  网易云通信ID可以指定登录token值，最大长度128字符，并更新，如果未指定，会自动生成token，并在创建成功后返回
     *
     * - sign: string, 用户签名，最大长度256字符
     *
     * - email: string, 用户email，最大长度64字符
     *
     * - birth: string, 用户生日，最大长度16字符
     *
     * - mobile: string, 用户mobile，最大长度32字符，非中国大陆手机号码需要填写国家代码(如美国：+1-xxxxxxxxxx)或地区代码(如香港：+852-xxxxxxxx)
     *
     * - gender: int, 用户性别，0表示未知，1表示男，2女表示女，其它会报参数错误
     *
     * - ex: string, 用户名片扩展字段，最大长度1024字符，用户可自行扩展，建议封装成JSON字符串
     *
     * @return array|mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function create($accid, array $options)
    {
        return $this->sendRequest('user/create.action', array_merge($options, ['accid' => $accid]));
    }

    /**
     * 网易云通信ID基本信息更新
     *
     * @param string $accid 网易云通信ID，最大长度32字符，必须保证一个APP内唯一
     * @param string $token 网易云通信ID可以指定登录token值（即密码），最大长度128字符
     *
     * @return array|mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function update(string $accid, string $token)
    {
        return $this->sendRequest('user/update.action', ['accid' => $accid, 'token' => $token]);
    }

    /**
     * 更新并获取新token
     *
     * @param string $accid 网易云通信ID，最大长度32字符，必须保证一个APP内唯一
     *
     * @return array|mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function refreshToken(string $accid)
    {
        return $this->sendRequest('user/refreshToken.action', ['accid' => $accid]);
    }

    /**
     * 封禁网易云通信ID
     *
     * - 封禁网易云通信ID后，此ID将不能再次登录。若封禁时，该id处于登录状态，则当前登录不受影响，仍然可以收发消息。封禁效果会在下次登录时生效。
     *   因此建议，将needkick设置为true，让该账号同时被踢出登录。
     * - 封禁时踢出，会触发登出事件消息抄送。
     * - 出于安全目的，账号创建后只能封禁，不能删除；封禁后账号仍计入应用内账号总数。
     *
     * @param string $accid 网易云通信ID，最大长度32字符，必须保证一个APP内唯一
     * @param bool $needkick 是否踢掉被禁用户，true或false
     *
     * @return array|mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function block(string $accid, bool $needkick = true)
    {
        return $this->sendRequest('user/block.action', ['accid' => $accid, 'needkick' => $needkick]);
    }

    /**
     * 解禁网易云通信ID
     *
     * @param string $accid 网易云通信ID，最大长度32字符，必须保证一个APP内唯一
     *
     * @return array|mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function unblock(string $accid)
    {
        return $this->sendRequest('user/unblock.action', ['accid' => $accid]);
    }

    /**
     * 更新用户名片
     *
     * @param string $accid 用户帐号，最大长度32字符，必须保证一个APP内唯一
     * @param array $options 可选参数集合，支持参数如下：
     *
     * - name: string, 网易云通信ID昵称，最大长度64字符。
     *
     * - icon: string, 网易云通信ID头像URL，开发者可选填，最大长度1024
     *
     * - sign: string, 用户签名，最大长度256字符
     *
     * - email: string, 用户email，最大长度64字符
     *
     * - birth: string, 用户生日，最大长度16字符
     *
     * - mobile: string, 用户mobile，最大长度32字符，非中国大陆手机号码需要填写国家代码(如美国：+1-xxxxxxxxxx)或地区代码(如香港：+852-xxxxxxxx)
     *
     * - gender: int, 用户性别，0表示未知，1表示男，2女表示女，其它会报参数错误
     *
     * - ex: string, 用户名片扩展字段，最大长度1024字符，用户可自行扩展，建议封装成JSON字符串
     *
     * @return array|mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function updateUserInfo(string $accid, array $options)
    {
        return $this->sendRequest('user/updateUinfo.action', array_merge($options, ['accid' => $accid]));
    }

    /**
     * 获取用户名片，可以批量
     *
     * @param array $accids 用户帐号（一次查询最多为200）
     *
     * @return mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function getUserInfos(array $accids)
    {
        return $this->sendRequest('user/getUinfos.action', ['accids' => json_encode($accids),]);
    }

    /**
     * 账号全局禁言
     *
     * @param string $accid 用户帐号
     * @param bool $mute 是否全局禁言：true：全局禁言，false:取消全局禁言
     *
     * @return array|mixed
     * @throws GuzzleException
     * @throws YunXinBusinessException
     * @throws YunXinInnerException
     * @throws YunXinNetworkException
     */
    public function mute(string $accid, bool $mute)
    {
        return $this->sendRequest('user/mute.action', ['accid' => $accid, 'mute' => $mute]);
    }
}
