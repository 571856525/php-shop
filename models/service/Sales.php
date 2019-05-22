<?php
//月度总销量
class Service_Sales
{
    private $dSales;

    public function __construct()
    {
        $this->dSales = new Dao_Sales();
    }
    /**
     * 获取销量列表
     */
    public function getList($pn, $rn, $month,$year)
    {
        return $this->dSales->getList($pn, $rn, $month,$year);
    }
    /**
     * 获取销量总数
     */
    public function getCount($month,$year)
    {
        return $this->dSales->getCount($month,$year);
    }
    /**
     * 通过id获取销量信息
     * @return array
     */
    public function getById($id)
    {
        return $this->dSales->getById($id);
    }

    /** 
     * 通过ID,月份,年份获取销量信息
     */
    public function getByIdMonth($id,$month,$year)
    {
        return $this->dSales->getByIdMonth($id,$month,$year);
    }
    //获取下级会员月度奖励列表

    public function getListByIdMonth($id,$month,$year)
    {
        return $this->dSales->getListByIdMonth($id,$month,$year);
    }


}
