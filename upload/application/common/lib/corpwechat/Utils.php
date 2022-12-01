<?php

namespace app\common\lib\corpwechat;

class Utils {
  public static function Xml2Array($xml) {
    //禁止引用外部xml实体
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
  }

  public static function Array2Xml($rootName, $arr) {
    $xml = "<" . $rootName . ">";
    foreach ($arr as $key => $val) {
      if (is_numeric($val)) {
        $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
      } else {
        $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
      }
    }
    $xml .= "</" . $rootName . ">";
    return $xml;
  }
}
