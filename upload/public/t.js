[
  { // 建立连接后 立即发送
    controller: "Connect",
    action: "index",
    args: {
      token: "imToken",
    },
  },
  { // 回执
    controller: "SendReturnReceipt",
    action: "one",
    args: {
      token: "imToken",
      messageid: "messageid",
    },
  },
  { // 回执
    controller: "SendReturnReceipt",
    action: "all",
    args: {
      token: "imToken",
      chatid: "imChatid",
    },
  },
  {
    controller: "SendMobile",
    action: "agree",
    args: {
      token: "imToken",
      messageid: "messageid",
    },
  },
  {
    controller: "SendMobile",
    action: "refuse",
    args: {
      token: "imToken",
      messageid: "messageid",
    },
  },
  {
    controller: "SendMobile",
    action: "apply",
    args: {
      token: "imToken",
      chatid: "imChatid",
    },
  },
  {
    controller: "SendWechat",
    action: "apply",
    args: {
      token: "imToken",
      chatid: "imChatid",
      wechat: "微信号",
    },
  },
  {
    controller: "SendMap",
    action: "index",
    args: {
      token: "imToken",
      chatid: "imChatid",
      lat: "纬度",
      lng: "经度",
      title: "标题",
      address: "地址",
    },
  },
  {
    controller: "SendJob",
    action: "index",
    args: {
      token: "imToken",
      chatid: "imChatid",
    },
  },
  {
    controller: "SendResume",
    action: "index",
    args: {
      token: "imToken",
      chatid: "imChatid",
    },
  },
  {
    controller: "SendInvite",
    action: "invite",
    args: {
      token: "imToken",
      chatid: "imChatid",
    },
  },
  {
    controller: "SendInvite",
    action: "applyjob",
    args: {
      token: "imToken",
      messageid: "messageid",
    },
  },
  {
    controller: "SendImage",
    action: "index",
    args: {
      token: "imToken",
      chatid: "chatid",
      content: "base64 类型数据",
    },
  },
  {
    controller: "SendText",
    action: "index",
    args: {
      token: "imToken",
      chatid: "imChatid",
      content: "消息内容",
    },
  },
  {
    controller: "Ping",
    action: "index",
    args: {},
  },
];
