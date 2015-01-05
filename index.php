<?php
 //define your token
 define("TOKEN", "chenxiang");//�ĳ��Լ���TOKEN
 define('APP_ID', '');//�ĳ��Լ���APPID
 define('APP_SECRET', '');//�ĳ��Լ���APPSECRET
 

$wechatObj = new wechatCallbackapiTest(APP_ID,APP_SECRET);
 $wechatObj->Run();
 

class wechatCallbackapiTest
 {
     private $fromUsername;
     private $toUsername;
     private $times;
     private $keyword;
     private $app_id;
     private $app_secret;
     
    
    public function __construct($appid,$appsecret)
     {
         # code...
         $this->app_id = $appid;
         $this->app_secret = $appsecret;
     }
 
    public function valid()
     {
         $echoStr = $_GET["echostr"];
         if($this->checkSignature()){
             echo $echoStr;
             exit;
         }
     }
 
    /**
      * ���г���
      * @param string $value [description]
      */
     public function Run()
     {
         $this->responseMsg();
         $arr[]= "���ã������Զ��ظ��������ڲ��ڣ����������ԣ��һᾡ��ظ����^_^";
         echo $this->make_xml("text",$arr);
     }
 
    public function responseMsg()
     {   
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];//���ػظ�����
         if (!empty($postStr)){
                 $access_token = $this->get_access_token();//��ȡaccess_token
                 $this->createmenu($access_token);//�����˵�
                 //$this->delmenu($access_token);//ɾ���˵�
                 $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                 $this->fromUsername = $postObj->FromUserName;//������Ϣ��ID
                 $this->toUsername = $postObj->ToUserName;//������Ϣ��ID
                 $this->keyword = trim($postObj->Content);//�û����͵���Ϣ
                 $this->times = time();//����ʱ��
                 $MsgType = $postObj->MsgType;//��Ϣ����
                 if($MsgType=='event'){
                     $MsgEvent = $postObj->Event;//��ȡ�¼�����
                     if ($MsgEvent=='subscribe') {//�����¼�
                         $arr[] = "��ã�����xxx�����������Ǻ��ѿ�![���][õ��]";
                         echo $this->make_xml("text",$arr);
                         exit;
                     }elseif ($MsgEvent=='CLICK') {//����¼�
                         $EventKey = $postObj->EventKey;//�˵����Զ����keyֵ�����Ը��ݴ�ֵ�ж��û������ʲô���ݣ��Ӷ����Ͳ�ͬ��Ϣ
                         $arr[] = $EventKey;
                         echo $this->make_xml("text",$arr);
                         exit;
                     }
                 }
         }else {
             echo "this a file for weixin API!";
             exit;
         }
     }
 
    /**
      * ��ȡaccess_token
      */
 
    private function get_access_token()
     {
         $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->app_id."&secret=".$this->app_secret;
         $data = json_decode(file_get_contents($url),true);
         if($data['access_token']){
             return $data['access_token'];
         }else{
 
            return "��ȡaccess_token����";
         }
     }
 
    /**
      * �����˵�
      * @param $access_token �ѻ�ȡ��ACCESS_TOKEN
      */
     public function createmenu($access_token)
     {
         $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
         $arr = array( 
            'button' =>array(
                 array(
                     'name'=>urlencode("�����ѯ"),
                     'sub_button'=>array(
                         array(
                             'name'=>urlencode("������ѯ"),
                             'type'=>'click',
                             'key'=>'VCX_WEATHER'
                         ),
                         array(
                             'name'=>urlencode("���֤��ѯ"),
                             'type'=>'click',
                             'key'=>'VCX_IDENT'
                         )
                     )
                 ),
                 array(
                     'name'=>urlencode("��������"),
                     'sub_button'=>array(
                         array(
                             'name'=>urlencode("�ι���"),
                             'type'=>'click',
                             'key'=>'VCX_GUAHAPPY'
                         ),
                         array(
                             'name'=>urlencode("(www.111cn.net)���˴�ת��"),
                             'type'=>'click',
                             'key'=>'VCX_LUCKPAN'
                         )
                     )
                 ),
                 array(
                     'name'=>urlencode("�ҵ���Ϣ"),
                     'sub_button'=>array(
                         array(
                             'name'=>urlencode("������"),
                             'type'=>'click',
                             'key'=>'VCX_ABOUTME'
                         ),
                         array(
                             'name'=>urlencode("������Ϣ"),
                             'type'=>'click',
                             'key'=>'VCX_JOBINFORMATION'
                         )
                     )
                 )
             )
         );
         $jsondata = urldecode(json_encode($arr));
         $ch = curl_init();
         curl_setopt($ch,CURLOPT_URL,$url);
         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt($ch,CURLOPT_POST,1);
         curl_setopt($ch,CURLOPT_POSTFIELDS,$jsondata);
         curl_exec($ch);
         curl_close($ch);
 
    }
 
    /**
      * ��ѯ�˵�
      * @param $access_token �ѻ�ȡ��ACCESS_TOKEN
      */
     
    private function getmenu($access_token)
     {
         # code...
         $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$access_token;
         $data = file_get_contents($url);
         return $data;
     }
 
    /**
      * ɾ���˵�
      * @param $access_token �ѻ�ȡ��ACCESS_TOKEN
      */
     
    private function delmenu($access_token)
     {
         # code...
         $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$access_token;
         $data = json_decode(file_get_contents($url),true);
         if ($data['errcode']==0) {
             # code...
             return true;
         }else{
             return false;
         }
 
    }
         
    /**
      *@param type: text �ı�����, news ͼ������
      *@param value_arr array(����),array(ID)
      *@param o_arr array(array(����,����,ͼƬ,������),...С��10��),array(����,ID)
      */
     
    private function make_xml($type,$value_arr,$o_arr=array(0)){
         //=================xml header============
         $con="<xml>
                     <ToUserName><![CDATA[{$this->fromUsername}]]></ToUserName>
                     <FromUserName><![CDATA[{$this->toUsername}]]></FromUserName>
                     <CreateTime>{$this->times}</CreateTime>
                     <MsgType><![CDATA[{$type}]]></MsgType>";
                     
          //=================type content============
         switch($type){
           
            case "text" : 
                $con.="<Content><![CDATA[{$value_arr[0]}]]></Content>
                     <FuncFlag>{$o_arr}</FuncFlag>";  
            break;
             
            case "news" : 
                $con.="<ArticleCount>{$o_arr[0]}</ArticleCount>
                      <Articles>";
                 foreach($value_arr as $id=>$v){
                     if($id>=$o_arr[0]) break; else null; //�ж�������������������
                     $con.="<item>
                          <Title><![CDATA[{$v[0]}]]></Title> 
                         <Description><![CDATA[{$v[1]}]]></Description>
                          <PicUrl><![CDATA[{$v[2]}]]></PicUrl>
                          <Url><![CDATA[{$v[3]}]]></Url>
                          </item>";
                 }
                 $con.="</Articles>
                      <FuncFlag>{$o_arr[1]}</FuncFlag>";  
            break;
             
        } //end switch
           
         //=================end return============
         $con.="</xml>";
          
        return $con;
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
 }
 
?>
