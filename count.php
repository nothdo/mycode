<?php
namespace Admin\Controller;
use Think\Controller;

class CountController extends Controller {

    public function daily_data(){
        ini_set('max_execution_time',600);
        $user = M('user');
        $trade = M('trade');
        $gold_log = M('gold_log');
        $s1=date('Ymd',strtotime('-1 day'));
        //$s1=20140616;
        $s2='000000';$s3='235959';
        $map1['user.create_time'] = array(array('gt',$s1.$s2),array('lt',$s1.$s3)) ;
        $map2['login_time'] = array(array('gt',$s1.$s2),array('lt',$s1.$s3)) ;
        $map3['time'] = array(array('gt',$s1.$s2),array('lt',$s1.$s3));
        $map4['trade.create_time'] = array(array('gt',$s1.$s2),array('lt',$s1.$s3)) ;
        $map4['trade.status'] = array('neq',0);
        $map5['create_time'] = array(array('gt',$s1.$s2),array('lt',$s1.$s3)) ;
        $data['log_date'] = $s1;
        $data['new_user'] = $user->where($map1)->count();
        ////$data[new_gold_user] = $trade->join('left join user ON trade.user_id = user.user_id ')->where($map1)->where($map4)->distinct(true)->field('user.user_id')->count();
        $lins=$gold_log->query("SELECT count(distinct gold_log.user_id) FROM `gold_log` left join user ON gold_log.user_id = user.user_id WHERE ( (user.create_time > ' $s1$s2')
         AND (user.create_time < '$s1$s3') ) AND ( (gold_log.time > $s1$s2) AND (gold_log.time < $s1$s3) )");
        $data['new_gold_user'] = $lins[0]['count(distinct gold_log.user_id)'];
        $data['active_user'] = $user->where($map2)->count();
        $lin=$gold_log->query("SELECT count(distinct gold_log.user_id) FROM `gold_log` left join user ON gold_log.user_id = user.user_id WHERE
                             ( (gold_log.time > $s1$s2) AND (gold_log.time < $s1$s3) )");
        $data['gold_user'] = $lin[0]['count(distinct gold_log.user_id)'];
       //// $data[gold_user] = $trade->join('trade ON trade.user_id = user.user_id ')->distinct(true)->field('user_id')->where($map1)->count();
        $data['task_no'] = $gold_log->where($map3)->count();
        $data['task_gold'] =(int) $gold_log->where($map3)->sum('amount');
        $data['gold_income'] =(float) $trade->where($map5)->where('status!=0')->sum('total_gold_price');
        $data['gold_cost'] = (float)$trade->join('goods ON goods.goods_id = trade.goods_id ')->where($map4)->where('trade.total_gold_price > 0')->sum('cost_price');
        $data['alipay_income'] =(float) $trade->where($map5)->where('status!=0')->sum('total_money_price');
       $data['alipay_cost'] =(float) $trade->join('goods ON goods.goods_id = trade.goods_id ')->where($map4)->where('trade.total_money_price>0')->sum('cost_price');
               $daily = M('daily_data');
              $daily->create($data);
              if($daily->add()){
                  echo '插入成功';
                  dump($data);
              }else{
                 echo '数据操作异常！';
              }

              $go = M('task_data');
              $data1['log_date'] = $s1;

              $data1 = $gold_log->where($map3)->field('source,count(source) as task_no,count(distinct(user_id)) as task_user,sum(amount) as task_gold,avg(amount) as task_price')->group('source')->select();
              //dump($data1);
              foreach($data1 as $vo){
                  $na[] = $vo['source'];
                  $go->create($vo);
                  $go->log_date = $s1;
                  $go->add();
              }
          //    dump($na);
              $name = M('task')->field('name')->order('name')->select();
              foreach($name as $n){
                  if(!in_array($n['name'],$na)){
                      $in['log_date'] = $s1;
                      $in['source'] = $n['name'];
                      $go->add($in);
                  }
              }
    }

    public function send_email(){
        $contents = file_get_contents("http://koala.shequan.com/admin.php/Count/web");
        $mail  = new \Think\Mail();
        $mail->setServer('smtp.exmail.qq.com', 'no-reply@buding.cn', 'buding16021');
        $mail->setFrom('no-reply@buding.cn');
        $mail->setReceiver('qimai@buding.cn');
        $mail->setReceiver('caiwu@buding.cn');
        $s1=date('Y-m-d',strtotime('-1 day'));
        $mail->setMailInfo("[".$s1."]流量加油站统计数据",$contents);
        $mail->sendMail();
    }

    public function web(){
        $daily = M('daily_data');
        $task = M('task_data');
        $s=date('Y-m-d',strtotime('-1 day'));
        $s1=date('Ymd',strtotime('-1 day'));
        $s4=date('Ymd',strtotime('-15 day'));
        $s2 = date('Ym');
        $s3 = date('Ymd');
        $map['log_date'] = $s1;
        $map['log_date'] = $s1;
        $map1['log_date'] = array(array('gt',$s2.'00'),array('lt',$s3)) ;
        $map4['log_date'] = array('gt',$s4) ;
        $num = $task->where($map1)->field('sum(task_gold)')->group('source')->select();
        $list = $daily->where($map)->find();
        $list1 = $task->where($map)->order('source')->select();

        $total = $task->where($map)->field('sum(task_no),sum(task_user),sum(task_gold),avg(task_price)')->find();
        $total['total'] = $task->where($map1)->sum('task_gold');
        foreach($list1 as $k=>$v){
            $list1[$k][] = $num[$k];
        }
        $data = $daily->limit(14)->order('log_date desc')->select();
        $data1 = $daily->where($map4)->field('avg(active_user),avg(gold_user),avg(new_user),avg(new_gold_user),avg(task_gold)')->find();
        $this->assign("date",$s);
        $this->assign("total",$total);
        $this->assign("list",$list);
        $this->assign("list1",$list1);
        $this->assign("data",$data);
        $this->assign("data1",$data1);
        //dump($num);
        $this->display();
    }

    public function broaden(){
        $time = date('Y-m-d',strtotime('-1 day'));
        $start_time = !empty($_REQUEST['start_time'])?$_REQUEST['start_time'].' 00:00:00':$time.' 00:00:00';
        $end_time = !empty($_REQUEST['end_time'])?$_REQUEST['end_time'].' 23:59:59':$time.' 23:59:59';
        $app_name = !empty($_REQUEST['app_name'])?$_REQUEST['app_name']:'633568292';
        $Model =  M('goldwall_log');
        $mkq =  M('gold_log');
        if($start_time&&$end_time){
            $map['down_time'] = array(array('gt',$start_time),array('lt',$end_time));
        }
        if($app_name){
            $map['app'] = $app_name;
        }
        $union_data = $mkq->join('goldwall_log on goldwall_log.user_id=gold_log.user_id')->field('sum(gold_log.amount) as task_income,goldwall_log.source as goldwall_source,count(gold_log.log_id) as task_number')->cache(true,60,'memcache')->where($map)->where("gold_log.source!='bonus2014'")->group('goldwall_log.source')->select();
        $with_draw = $Model->join('trade on trade.user_id=goldwall_log.user_id')->field('sum(trade.total_gold_price) as price,goldwall_log.source as goldwall_source')->cache(true,60,'memcache')->where($map)->group('goldwall_source')->select();
        $list = $Model->field('source')->where($map)->group('source')->select();
        $status =array();
        foreach($list as $k=>$vo){
            $map['source'] = $vo['source'];
            $status[$map['source']] = $Model->field('count(*),status')->cache(true,60,'memcache')->where($map)->group('status')->select();
        }
        $get_data = array();
        foreach($status as $k=>$v){
            $data_vi = array();
            foreach($v as $vi){
                $data_vi['status'.$vi['status']] = $vi['count(*)'];
            }
            $get_data[$k] = $data_vi;
        }

        foreach($union_data as $vo1){
            $data1[ $vo1['goldwall_source']] = $vo1;
        }

        foreach($get_data as $k2=>$vo2){
            $c = array();
            foreach($data1 as $k1=>$vo1){
                if($k1==$k2){
                    $c = array_merge($vo2,$vo1);
                }
            }
            if($c) $n[] = $c;
        }
        switch($app_name){
            case 888206295:
                $app = "流量加油站";break;
        }
        foreach($with_draw as $vo){
            $no = array();
            foreach($n as $v){
                if($vo['goldwall_source']==$v['goldwall_source']){
                    $no = array_merge($vo,$v);
                }
            }
            $note[] = $no;
        }
        $this->assign('app_name',$app);
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);
        $this->assign('list',$note);
         $this->display();
    }

    public function gaoyang_goods(){
        $file = 'http://hf.19ego.com/esales2/prodquery/directProduct.do?agentid=buding&source=esales&verifystring=625c70c01339252ffc4690ad6d4e6005';
        $xml = simplexml_load_file($file);
        if($xml){
            $list = $xml->products;
            $data = array();
            foreach($list as $value){
                $arr = array();
                foreach($value->product as $v){
                    $k = (string)$v['name'];
                    $arr[$k]=urldecode((string)$v['value']);
                };
                $data[] = $arr;
            }
            $gaoyang_goods = M('gaoyang_goods');
            foreach($data as $vo){
                $pro = $vo['prodId'];
                $isset[] = $pro;
                $db = $gaoyang_goods->where("prodId = $pro")->find();
                if(!$db){
                    $new[] = $vo;
                }else{
                    $isset[] = $db;
                    if($vo['prodPrice']!=$db['prodPrice']){
                        $array[] = $db;
                    }
                }

            }
            $db = $gaoyang_goods->select();
            foreach($db as $v){
                if(!in_array($v['prodId'],$isset))
                $lib[] = $v;
            }
            if($new||$lib||$array){
                $gaoyang_goods->query('truncate table `gaoyang_goods`');
                    foreach($data as $vol){
                        $gaoyang_goods->add($vol);
                    }
                }


            $this->assign('new',$new);
            $this->assign('array',$array);
            $this->assign('lib',$lib);
           $this->display();

        }
    }

    public function change_send_email(){
        $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, "http://koala.shequan.com/admin.php/Count/gaoyang_goods");//设置选项，包括URL
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_HEADER, 0);
       //执行并获取HTML文档内容
       $output = curl_exec($ch);
       //释放curl句柄
       curl_close($ch);
       //打印获得的数据
       //print_r($output);
        $mail  = new \Think\Mail();
        $mail->setServer('smtp.exmail.qq.com', 'no-reply@buding.cn', 'buding16021');
        $mail->setFrom('no-reply@buding.cn');
        $mail->setReceiver('wangdong@buding.cn');
        $mail->setReceiver('liudongliang@buding.cn');
        $mail->setReceiver('weiqiang@buding.cn');
        //$s1=date('Y-m-d',strtotime('-1 day'));
        $mail->setMailInfo("接口数据变化",$output);
        $mail->sendMail();
    }

    public function black(){
        set_time_limit(1800);
        $user = M('user');
        $trade = M('trade');
        $blackphone = M('blackphone');
        $addtime = date('Y-m-d H:i:s',strtotime('-1 month'));
        $other = "同一个手机号对应的账号数量>=10";
        $black_user=$trade->query("select phone from `trade` where create_time >'$addtime' group by phone having count(distinct user_id)>=10");
       // echo $trade->_sql();
        //dump($black_user);
        foreach($black_user as $v){
            $phone=$map['phone']=$v['phone'];
/*           $max = $trade->where($map)->max('create_time');
            $min = $trade->where($map)->min('create_time');
            if($max-$min<30*24*60*60){
                $blackphone->add($data);
            }*/
            $blackphone->query("insert ignore into `blackphone`(phone,addtime,other) values ( $phone ,now(),$other) ");
            $user_id = $trade->field('distinct user_id')->where($map)->select();
            //dump($user_id);
            foreach($user_id as $vo){
                $map1['user_id'] = $vo['user_id'];
                $map2['status'] = array('in','1,3,5,6');
                $user->where($map1)->setField('level','-1');
                $trade->where($map1)->where($map2)->setField('status',10);
               // dump($vo['user_id']);
                usleep(1000);
            }
            usleep(1000);
        }
        //dump($black_user);
    }

    public function img_save(){

        $re = M('weixin')->where('other=0')->select();
        foreach($re as $v){
            $img = file_get_contents($v['content']);
            file_put_contents("./upload/weixin/".date("YmdHis")."_".$v['id'].".jpg",$img);
            $st = M('weixin')->where('id='.$v['id'])->setField('other',1);
        }
    }

}
