<?php

$dir = dirname(__FILE__);
require_once($dir.'/../consts/WxConfig.php');
require_once($dir.'/../consts/MsgType.php');

class BaiduMusic
{

    public static function get_curl_contents($url, $header = 0, $nobody = 0, $ipopen = 1)
    {
        if (!function_exists('curl_init')) die('php.ini未开启php_curl.dll');
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_HEADER, $header);
        curl_setopt($c, CURLOPT_NOBODY, $nobody);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $ipopen == 0 && curl_setopt($c, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $_SERVER["REMOTE_ADDR"], 'CLIENT-IP:' . $_SERVER["REMOTE_ADDR"]));
        $content = curl_exec($c);

        curl_close($c);
        return $content;
    }

    //根据关键字，页码搜索歌曲
    public static function getSong($keyword, $page_num = 1)
    {
        $html = "/\<[\/\!]*?[^\<\>]*?\>/is";
        $songs = array();
        $kw = urlencode($keyword);
        $url = 'http://tingapi.ting.baidu.com/v1/restserver/ting?from=webapp_music&method=baidu.ting.search.common&format=json&query=' . $kw . '&page_no=' . $page_num . '&page_size=20' . '&_=' . time();
        $content = json_decode(self::get_curl_contents($url), true);
        //header("Content-type: text/html; charset=utf-8");print_r($content);exit;

        $list = $content['song_list'];
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                $songs[$k]['id'] = $v['song_id'];
                $songs[$k]['name'] = preg_replace($html, "", $v['title']);
                $songs[$k]['singer'] = preg_replace($html, "", $v['author']);
                $songs[$k]['album'] = preg_replace($html, "", $v['album_title']);

                $songInfo = self::getInfo($v['song_id']);
                $songs[$k]['link'] = $songInfo[0];
                $songs[$k]['pic'] = $songInfo[1];
                $songs[$k]['jsondata'] = $songInfo[2];
            }
            return $songs;
        }
    }

    //根据歌曲id获得播放链接+小图片
    public static function getInfo($song_id)
    {
        $url = 'http://music.baidu.com/data/music/fmlink?songIds=' . $song_id . '&type=mp3';
        do {
            $content = self::get_curl_contents($url);
        } while (empty($content));
        $play[2] = $content;

        $content = json_decode($content, true);
        $temp = $content['data']['songList'][0]['songLink'];
        $arr = explode('?', $temp);
        $play[0] = $arr[0];
        $play[1] = $content['data']['songList'][0]['songPicBig'];
        if (strpos($play[0], 'yinyueshiting')) {
            $play[0] = str_replace('yinyueshiting', 'musicdata', $play[0]);
        } elseif (strpos($play[0], 'file.qianqian')) {
            $play[0] = str_replace('file.qianqian.com/', 'musicdata.baidu.com', $play[0]);
        }
        return $play;
    }
}