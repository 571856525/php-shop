<?php
/**
*  代理商级别以及代理商用户
*/
class Service_Agent
{
    private $dAgent;
    private $dAgentment;

    public function __construct()
    {
        $this->dAgent = new Dao_Agent();
        $this->dAgentment = new Dao_Agentment();
    }

    
    /**
     * @return array
     */
    public function getList($userid)
    {
        return $this->dAgent->getList($userid);
    }

    /**
     * 获取未审核通过的
     * @return array
     */
    public function getAgentById($userid,$state)
    {
        return $this->dAgent->getAgentById($userid,$state);
    }


   /**
     * 获取未审核通过的
     * @return array
     */
    public function getOneById($userid)
    {
        return $this->dAgent->getOneById($userid);
    }
    


}
