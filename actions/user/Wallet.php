<?php

/**
 * 我的钱包
 */

class Action_Wallet extends App_Action
{
    private  $sSales;
    private  $sRecord;
    private  $sUser;

    public function __prepare()
    {
        $this->hookNeedMsg = true;
        $this->sSales = new Service_Sales();
        $this->sRecord = new Service_Record();
        $this->sUser = new Service_User();
        $this->setView(Blue_Action::VIEW_JSON);
        $this->setView(Blue_Action::VIEW_SMARTY3);     
    }

    public function __execute()
    {
        $sess=$this->getSession(); 
        if ($this->getRequest()->isGet()) {
            $date =date('Y-m-d',time());
            //默认当下日期
            $dateSearch=!empty($_GET['date']) ? trim($_GET['date']) : $date ;

            //我的余额   
            $user= $this->sUser->getById($sess['id']);
            //获取我的月度总销量
            $month=intval(date('m',time()));
            $year=date('Y',time());
            //获取我的月度销量
            $sale =$this->sSales->getByIdMonth($sess['id'], $month,$year);
            //月度奖励金
            $lama = new App_Lama();
            //更新月度销量
            //获取订单下商品
            $amount=0;
            if($sale['num']>0)
            {
                $amount=$lama->getMonthBonus($sale['num']);
                //获取下级会员月度奖励总和
                $down_amount=0;
                $down_list=$this->sSales->getListByIdMonth($sess['id'],$month,$year);
                foreach($down_list as $v)
                {
                    $down_amount=$down_amount+ $lama->getMonthBonus($v['num']);
                }
                $amount=$amount-$down_amount;
            }
            //查询年份
            $yearSearch=substr($dateSearch,0,4);
            //查询月份
            $monthSearch=substr($dateSearch,5,2);
            $record=$this->sRecord->getListBydate($sess['id'],$monthSearch,$yearSearch);
            return array('amount' => $user['amount'],'yd_amount' => $amount,'record' => $record);
        } 
        else
        {

        }

    }
}
