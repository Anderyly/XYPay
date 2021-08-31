<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://vclove.cn/
 * @copyright Copyright (c) 2018
 */

namespace ay\lib;

class Str
{

    public static function instance()
    {
        return new self();
    }

    /**
     * 截取摘要
     * @param string $content 内容
     * @param int $count 字数
     * @return string
     */
    public function summary($content, $count)
    {
        $content = preg_replace("@<script(.*?)</script>@is", "", $content);
        $content = preg_replace("@<iframe(.*?)</iframe>@is", "", $content);
        $content = preg_replace("@<style(.*?)</style>@is", "", $content);
        $content = preg_replace("@<(.*?)>@is", "", $content);
        $content = str_replace(PHP_EOL, '', $content);
        $space = array(" ", "　", "  ", " ", " ");
        $go_away = array("", "", "", "", "");
        $content = str_replace($space, $go_away, $content);
        $res = mb_substr($content, 0, $count, 'UTF-8');
        if (mb_strlen($content, 'UTF-8') > $count) {
            $res = $res . "...";
        }
        return $res;
    }

    /**
     * 转义危险字符 并 判断违禁词
     * @param string $data 内容
     * @param array $filter 危险字符数组
     * @return string
     */
    public function clean($data, $filter)
    {
        if (!get_magic_quotes_gpc()) {
            $data = addslashes($data);
        }
        $data = strtolower($data);
        $data = str_replace("_", "\_", $data);
        $data = str_replace("%", "\%", $data);
        $data = str_replace("*", "\*", $data);
        $data = str_replace("select", "\select", $data);
        $data = str_replace("insert", "\insert", $data);
        $data = str_replace("delete", "\delete", $data);
        $data = str_replace("update", "\update", $data);
        $data = nl2br($data);
        $data = htmlspecialchars($data);

        $arr = explode('|', $filter);
        foreach ($arr as $item) {
            if (strstr($data, $item)) \ay\lib\Json::msg(400, '含有违禁词');
        }
        return $data;

    }

    /**
     * gbk字符转utf8字符
     * @param void $data
     * @return void
     */
    public function gbkToUtf8($data)
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = gbkToUtf8($v);
            } else {
                $data[$k] = iconv('gbk', 'utf-8', $v);
            }
        }

        return $data;
    }

    /**
     * UTF8编码转GBK编码
     * @param $str
     *
     * @return string
     */
    public function utf8ToGbk($str)
    {
        $string = '';
        if ($str < 0x80) {
            $string .= $str;
        } elseif ($str < 0x800) {
            $string .= chr(0xC0 | $str >> 6);
            $string .= chr(0x80 | $str & 0x3F);
        } elseif ($str < 0x10000) {
            $string .= chr(0xE0 | $str >> 12);
            $string .= chr(0x80 | $str >> 6 & 0x3F);
            $string .= chr(0x80 | $str & 0x3F);
        } elseif ($str < 0x200000) {
            $string .= chr(0xF0 | $str >> 18);
            $string .= chr(0x80 | $str >> 12 & 0x3F);
            $string .= chr(0x80 | $str >> 6 & 0x3F);
            $string .= chr(0x80 | $str & 0x3F);
        }
        return @iconv('UTF-8', 'GB2312//IGNORE', $string);
    }

    /**
     * 将全角字符转为半角字符 如】转为]
     * @param $str
     * @return string
     */
    public function convert($str)
    {
        $arr = ['０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
            '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
            'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
            'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
            'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
            'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
            'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z',
            '（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
            '】' => ']', '〖' => '[', '〗' => ']', '“' => '[', '”' => ']',
            '‘' => '[', '’' => ']', '｛' => '{', '｝' => '}', '《' => '<',
            '》' => '>',
            '％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
            '：' => ':', '。' => '.', '、' => ',', '，' => ',', '、' => '.',
            '；' => ';', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
            '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"',
            '　' => ' '];

        return strtr($str, $arr);
    }

    /**
     * 汉字转拼音
     */
    public function pinyin($_String, $charset = null)
    {
        $charset = is_null($charset) ? preg_replace("/utf-8|utf8/i", "UTF-8", C("CHARSET")) : $charset;
        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha" .
            "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|" .
            "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er" .
            "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui" .
            "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang" .
            "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang" .
            "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue" .
            "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne" .
            "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen" .
            "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang" .
            "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|" .
            "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|" .
            "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu" .
            "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you" .
            "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|" .
            "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990" .
            "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725" .
            "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263" .
            "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003" .
            "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697" .
            "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211" .
            "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922" .
            "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468" .
            "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664" .
            "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407" .
            "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959" .
            "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652" .
            "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369" .
            "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128" .
            "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914" .
            "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645" .
            "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149" .
            "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087" .
            "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658" .
            "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340" .
            "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888" .
            "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585" .
            "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847" .
            "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055" .
            "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780" .
            "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274" .
            "|-10270|-10262|-10260|-10256|-10254";
        $_TDataKey = explode('|', $_DataKey);
        $_TDataValue = explode('|', $_DataValue);
        $_Data = array_combine($_TDataKey, $_TDataValue);
        arsort($_Data);
        reset($_Data);

        /**
         *    假如编码不是gb2312,则启用utf-8
         */
        if ($charset != 'gb2312') {
            $_String = self::utf8ToGbk($_String);
        }
        $_Res = '';
        for ($i = 0; $i < strlen($_String); $i++) {
            $_P = ord(substr($_String, $i, 1));
            /**
             *
             */
            if ($_P > 160) {
                $_Q = ord(substr($_String, ++$i, 1));
                $_P = $_P * 256 + $_Q - 65536;
            }
            $_Res .= self::getPinyin($_P, $_Data);
        }
        return preg_replace("/[^a-z0-9]*/", '', $_Res);
    }

    /**
     * 获得拼音字符串
     */
    private function getPinyin($_Num, $_Data)
    {
        ;
        if ($_Num > 0 && $_Num < 160) {
            return chr($_Num);
        } elseif ($_Num < -20319 || $_Num > -10247) {
            return '';
        } else {
            foreach ($_Data as $k => $v) {
                if ($v <= $_Num) {
                    break;
                }
            }
            return $k;
        }
    }

    /**
     * 去除标点符号
     * @param $string
     * @return string|string[]|null
     */
    public function remove($string)
    {
        $string = self::convert($string);
        $preg = "/[,.!;:<>\?'\"@#$%^&*\(\)\-_\+=\|\\\{\}\[\]\/`]/i";
        return preg_replace($preg, "", $string);
    }

    /**
     * 生成订单号
     * @return string
     */
    public function order($pix = '')
    {
        $order_id_main = date('YmdHis') . rand(10000000, 99999999);
        $order_id_len = strlen($order_id_main);
        $order_id_sum = 0;
        for ($i = 0; $i < $order_id_len; $i++):
            $order_id_sum += (int) (substr($order_id_main, $i, 1));
        endfor;
        $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
        return $pix . $order_id;
    }

    /**
     * 随机数
     * @param int $len
     * @return string
     */
    public function random($len = 12)
    {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        $strLen = strlen($str);
        $randStr = "";
        for ($i = 0; $i < $len; $i++) {
            $randStr .= $str[mt_rand(0, $strLen - 1)];
        }
        return $randStr;
    }

    /**
     * 生成uuid
     * @param string var
     * @return string
     */
    public function guid($var = '')
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            if (!empty($var)) {
                $uuid = chr(123)
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12)
                . chr(125);
            } else {
                $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
            }
            return $uuid;
        }
    }

    public function unHtml($content)
    {
        $content = htmlspecialchars($content);
        $content = str_replace(chr(13), "<br>", $content);
        $content = str_replace(chr(32), " ", $content);
        $content = str_replace("[_[", "<", $content);
        $content = str_replace("]_]", ">", $content);
        $content = str_replace("|_|", " ", $content);
        return trim($content);
    }

}
