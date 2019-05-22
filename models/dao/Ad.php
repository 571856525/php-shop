<?php
//广告位
class Dao_Ad extends Blue_Dao
{
    public function __construct()
    {
        parent::__construct('shop', 'shop', 'ad');
    }

    /**
     * 根据广告位ID获取广告
     */
    public function getList($cid)
    {
            return $this->select('status=1 and  cid='.$cid, '*','order by addtime desc' );
    }

    /**
     * 根据ID获取广告信息
     */
    public function getById($id)
    {
        return $this->selectOne(sprintf('id=%d', $id), '*');
    }
}
