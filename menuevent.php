<?php

/* 
����Ȩ������ 
    �������Ʒ�İ�Ȩ�鷽�����������У��ܡ��л����񹲺͹���������������������֪ʶ��Ȩ���ɼ�������Լ������ı���������õ�ֻ�Ǳ������ʹ��Ȩ�� 
 
    ������: 
    * ��δ�õ���Ȩ�������ɾ�����޸ı����������������һ�й��ڰ�Ȩ����Ϣ�� 
    * ���ۡ�����������Ʒ���κβ��֣� 
    * ���������ֺ��������Ȩ����Ϊ�� 
 
    �����δ���ر��������һԼ����������������Ȩ������ֹ�������ִ�У���������������ֹʹ�ñ���������ٱ������Ʒ���κθ���������Ҫ��Ը��ֿ�����ʽ��Ч�� 
 
    ��ͬ��е�ʹ�ñ������Ʒ�ķ��գ������÷�����������Χ�ڣ��������������κ�����²�����ʹ�û���ʹ�ñ������Ʒ������������ġ�����ġ���ֱ�ӻ��ӵ���ʧ�е��⳥���Ρ���ʹ�����ȱ���֪���𺦷����Ŀ����ԡ� 
 
    ��ʹ�ñ��������ӵ��κ���Ϣ��������Ȩ���ף����������Ҳ��е��κ����Ρ� 
 
    ���������ҶԱ�����ӵ�����ս���Ȩ�� 
 
    CopyRight 2013  ����������  All Rights Reserved 
 
*/ 

define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            switch ($RX_TYPE)
            {
                case "text":
                    $resultStr = $this->receiveText($postObj);
                    break;
                case "event":
                    $resultStr = $this->receiveEvent($postObj);
                    break;
                default:
                    $resultStr = "";
                    break;
            }
            echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveText($object)
    {
        $funcFlag = 0;
        $contentStr = "�㷢�͵�����Ϊ��".$object->Content;
        $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
        return $resultStr;
    }
    
    private function receiveEvent($object)
    {
        $contentStr = "";
        switch ($object->Event)
        {
            case "subscribe":
                $contentStr = "��ӭ��ע����������";
            case "unsubscribe":
                break;
            case "CLICK":
                switch ($object->EventKey)
                {
                    case "company":
                        $contentStr[] = array("Title" =>"��˾���", 
                        "Description" =>"�����������ṩ�ƶ���������صĲ�Ʒ������", 
                        "PicUrl" =>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                        "Url" =>"weixin://addfriend/pondbaystudio");
                        break;
                    default:
                        $contentStr[] = array("Title" =>"Ĭ�ϲ˵��ظ�", 
                        "Description" =>"������ʹ�õ��Ƿ��������ҵ��Զ���˵����Խӿ�", 
                        "PicUrl" =>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                        "Url" =>"weixin://addfriend/pondbaystudio");
                        break;
                }
                break;
            default:
                break;      

        }
        if (is_array($contentStr)){
            $resultStr = $this->transmitNews($object, $contentStr);
        }else{
            $resultStr = $this->transmitText($object, $contentStr);
        }
        return $resultStr;
    }

    private function transmitText($object, $content, $funcFlag = 0)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
<FuncFlag>%d</FuncFlag>
</xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $funcFlag);
        return $resultStr;
    }

    private function transmitNews($object, $arr_item, $funcFlag = 0)
    {
        //��������28�֣���������39��
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
<FuncFlag>%s</FuncFlag>
</xml>";

        $resultStr = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item), $funcFlag);
        return $resultStr;
    }
}
?>