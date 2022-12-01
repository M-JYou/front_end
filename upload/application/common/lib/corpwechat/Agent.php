<?php

namespace app\common\lib\corpwechat;

use app\common\lib\corpwechat\promise\Corp;

class Agent extends Corp {
  const MENU_CREATE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/create';
  const MENU_GET = 'https://qyapi.weixin.qq.com/cgi-bin/menu/get';
  const MENU_DELETE = 'https://qyapi.weixin.qq.com/cgi-bin/menu/delete';
  const AGENT_GET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/get';
  const AGENT_SET = 'https://qyapi.weixin.qq.com/cgi-bin/agent/set';
  const AGENT_GET_LIST = 'https://qyapi.weixin.qq.com/cgi-bin/agent/list';

  public function MenuCreate($agentid, $menu) {
    return $this->callPost(self::MENU_CREATE . "&agentid={$agentid}", $menu);
  }

  public function MenuGet($agentid) {
    return $this->callGet(self::MENU_GET, array('agentid' => $agentid));
  }

  public function MenuDelete($agentid) {
    return $this->callGet(self::MENU_DELETE, array('agentid' => $agentid));
  }

  public function AgentGet($agentid) {
    self::_HttpCall(self::AGENT_GET, 'GET', array('agentid' => $agentid));
    return Agent::Array2Agent($this->rspJson);
  }

  public function AgentSet($agent) {
    Agent::CheckAgentSetArgs($agent);
    $args = Agent::Agent2Array($agent);
    self::_HttpCall(self::AGENT_SET, 'POST', $args);
  }

  public function AgentGetList() {
    self::_HttpCall(self::AGENT_GET_LIST, 'GET', array());
    return Agent::Array2AgentList($this->rspJson);
  }
}
